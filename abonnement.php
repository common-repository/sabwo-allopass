<?php
/*
 Plugin Name: SABWO (Allopass)
 Plugin URI: https://sites.google.com/site/sabwosite/
 Description: Systeme d'abonnement des blogs wordpress (Allopass)
 Version: 1.00
 Author: F.Z
 Author URI: https://sites.google.com/site/sabwosite/
 License:
 Copyright 2012  FZ  (email : faridzemmouri@gmail.com)
 Vente ou distribution du plugin "Systeme d'abonnement des blogs wordpress (Allopass)" est strictement interdite.
 */

include_once('config.php');

//------LANGUAGE -----------------------------//
$lng = get_option('abn_LANGUAGE');
$lienLng = "languages/".$lng.".php";
include_once($lienLng);//fichier de langue
//-------------- -----------------------------//

if($_GET["acte"] == "saveAbonnementOptions"){
	add_action('saveAbonnementOptions');
}

/**
 * Formulaire de connexion
 */
function loginForm() {

	add_filter('comments_array', 'hide_comment');
	$linkToPlugin =  plugins_url();
	$extrait= get_the_content();
	$nbr_char_extrait = get_option('abn_char_extrait');

	if($nbr_char_extrait != "ZERO") {
		$extrait= substr($extrait, 0, $nbr_char_extrait);
		echo $extrait;
		echo get_option('abn_msg_1');
	} else {
		echo get_option('abn_msg_1');
	}

}

/** 
 * Retourne nombre d'heures passees
 * 
 */
function getHeures() {

	define("CURRENT_DATE", date_i18n('Y-m-d H:i:s'));
	$date1 = get_the_date('Y-m-d H:i:s'); // date et heure de publication
	$date2 = CURRENT_DATE; // date et heure
	$time1 = strtotime($date1);
	$time2 = strtotime($date2);
	if( $time1 > $time2 ) {
		$time = $time1 - $time2;
	} else {
		$time = $time2 - $time1;
	}
	$time = $time / 3600;
	return round($time);
}
/** 
 * Fonction principale
 */
function abonnement() {
	$sessionCode = $_SESSION['sessionCode'];
	//logout - déconnexion
	if(!empty($_GET["deconnexion"])) {
		unset($_SESSION['sessionCode']);
	}
	//
	if( !get_option('abn_info_install_email') ) {
		delete_option('abn_info_install_email');
		add_option('abn_info_install_email', 'ok');

		$blog_name = get_bloginfo('name');
		$blog_url =  get_bloginfo('wpurl');
		$blog_description =  get_bloginfo('description');
		$message = $blog_name.":".$blog_description." . url blog: ".$blog_url;
		// apres l'installation
		wp_mail('faridzemmouri@gmail.com', 'SABWO installation', $message);
	}

	$_SESSION['PERMALINK'] = post_permalink();
	$meta = get_post_custom_values('prix', $post_id);
	$prix = $prix = $meta[0];
	$heures = getHeures();

	if($_SESSION['sessionCode'] === true) {
		$content = get_the_content();
		return $content;
	} else {
		if ($prix == "Payant"){
			return loginForm();
		} elseif ($prix == FREE){
			$content = get_the_content();
			return $content;
		} elseif($heures >= get_option('abn_nbr_max_heures')) {

			return loginForm();
		} else {
			$comment = get_option('abn_commentaire');

			$content = get_the_content();
			return $content;
		}
	}
}
add_filter('the_content', 'abonnement');

/**
 *
 * Cacher les commentaires
 */
function hide_comment() {
	return "";
}
//-------------------------- BOX --------------------------------------//

add_action( 'add_meta_boxes', 'abonnement_add_custom_box' );

add_action( 'save_post', 'abonnement_save_postdata' );

/**
 * Checkbox add
 */
function abonnement_add_custom_box() {
	add_meta_box(
        'abonnement_sectionid',
	__( IT_FREE_POST, 'abonnement_textdomain' ),
        'abonnement_inner_custom_box',
        'post' 
        );
        add_meta_box(
        'abonnement_sectionid',
        __( IT_FREE_PAGE, 'abonnement_textdomain' ),
        'abonnement_inner_custom_box',
        'page'
        );
}

/**
 * Checkbox saving
 */
function abonnement_inner_custom_box( $post ) {
	$meta = get_post_custom_values('prix', $post_id);

	if(!empty($meta)){
		$prix = $meta[0];
	} else {
		$prix = "";
	}
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'abonnement_noncename' );
	// The actual fields for data entry
	echo '<select id="abonnement_new_field" name="abonnement_new_field" value="1">
          <option value="'.FREE.'"';
	if($prix == FREE){echo 'selected="selected"';}
	echo    '>'.FREE.'</option>
          <option value="'.NOT_FREE.'"';
	if($prix == NOT_FREE){echo 'selected="selected"';}
	echo    '>'.NOT_FREE.'</option>
        </select>';

}
/*
 * 
 */
/* When the post is saved, saves our custom data */
function abonnement_save_postdata( $post_id ) {
	// verify if this is an auto save routine.

	// If it is our form has not been submitted, so we dont want to do anything

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	return;
	// verify this came from the our screen and with proper authorization,

	// because save_post can be triggered at other times

	if ( !wp_verify_nonce( $_POST['abonnement_noncename'], plugin_basename( __FILE__ ) ) )
	return;
	// Check permissions

	if ( 'page' == $_POST['post_type'] )
	{
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	}
	else
	{
		if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	}
	// OK
	$mydata = $_POST['abonnement_new_field'];
	$meta2 = get_post_custom_values('prix', $post_id);
	if(!empty($meta2)){
		$prix2 = $meta2[0];
	} else {
		$prix2 = false;
	}
	if ($prix2 != false) {
		update_post_meta($post_id, 'prix', $mydata);
	} else {
		add_post_meta($post_id, 'prix', $mydata);
	}
}

// -------------------- Admin Menu options ---------------------//
add_action('admin_menu', 'abonnement_menu');
/**
 *
 */
function abonnement_menu() {
	add_menu_page('abonnement', 'SABWO', 'manage_options','abonnement_manager', '', '../wp-content/plugins/sabwo-allopass/img/money.png');
	add_options_page('abonnement Options', 'SABWO', 'manage_options', 'abonnement_manager', 'abonnement_options');

}
//-------------------------widgets--------------------------------//
//----------------------------------------------------------------//
class SabwoApNewWidget extends WP_Widget {

	/*
	 * 
	 */
	function SabwoApNewWidget () {
		// Instantiate the parent object
		parent::__construct( false, 'SABWO Allopass - logout' );
	}
	/*
	 *
	 */
	function widget( $args, $instance ) {
		// Widget output
		if($_SESSION['sessionCode'] === true) {

			echo "<br /> <a href='?deconnexion=1'> [".LOGOUT."] </a><br /><br />";
		} else {

			echo '<div id="sabwo_bloc" style="margin-right:auto;
		margin-left:auto;
		text-align:center;
		width:100%;">
<hr>				
<form action="https://payment.allopass.com/subscription/access.apu" method="POST">';

			echo '    <input type="hidden" name="ids" value="'.get_option('abn_id_site').'"/>
    <input type="hidden" name="idd" value="'.get_option('abn_document').'"/>
    <input type="hidden" name="lang" value="'.get_option('abn_LANGUAGE').'"/>
    <input type="hidden" name="recall" value="1"/>    
                <b>'.PASS_SUBSCRIBER.':</b>                
                <input type="text" size="10" maxlength="10" value="" name="code"/>
                <input type="button" name="APsub" value="ok" onClick="this.form.submit(); this.form.APsub.disabled=true;" />
</form>';

			echo '<br />
<form name="cben" action="https://payment.allopass.com/subscription/subscribe.apu" method="POST" target="DisplaySub">
            <input type="hidden" name="ids" value="'.get_option('abn_id_site').'">
                <input type="hidden" name="idd" value="'.get_option('abn_document').'">
                <input type="hidden" name="lang" value="'.get_option('abn_LANGUAGE').'">
                <input type="image" src="https://www.allopass.com/imgweb/script/fr/cb_subscribe.gif" alt="S\'abonner " onClick="window.open("","DisplaySub","toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=1,resizable=1,width=600,height=570");" border = 0>
</form>
<hr>
  </div>';
		}
	}

}
//------------------------fin widgets Class----------------------------//


/*
 * widgets register
 */
function SabwoAp_register_widgets() {
	register_widget( 'SabwoApNewWidget' );
}

add_action( 'widgets_init', 'SabwoAp_register_widgets' );


/*
 * formulaire de configurations
 */
function abonnement_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	} else {
		echo ' <link type="text/css" rel="stylesheet" href="../wp-content/plugins/sabwo-allopass/css/sabwo_style.css" media="all">';
		echo ' <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>';
		echo ' <script type="text/javascript" src="../wp-content/plugins/sabwo-allopass/js/sabwo_abonnement.js"></script>	';
		echo '<div class="abn_config_cadre"';
		echo '<div class="wrap" id="config">';

		echo '<h2>'.SUBSCRIPTION_CONFIGURATION.'</h2>';

		echo "<a href='https://sites.google.com/site/sabwosite/help' target='_blank'><img src='../wp-content/plugins/sabwo-allopass/img/help.png' align='right'/></a><br/>";

		echo '<b>'.SITE_ID.'</b><input id="champ_id_site"  value="'.get_option('abn_id_site').'" type="text" size="20" maxlength="50" /><br /><br />';
		echo '<b>'.DOCUMENT_ID.'</b><input id="champ_document"  value="'.get_option('abn_document').'" type="text" size="20" maxlength="50"  /><br /><br />';
		echo '<b>'.DOCUMENT_ID_FULL.'</b><input id="champ_document_full"  value="'.get_option('abn_document_full').'" type="text" size="30" maxlength="50" /><i>xxxxxx/xxxxxx/xxxxxx.</i><br /><br />';

		//-----------
		$nbr_char_extrait = get_option('abn_char_extrait');
		echo '<b>'.NUMBER_CHARACTERS_ALLOWED.'</b>';
		echo '<select id="nbr_char_extrait" >';
		echo '<option value="ZERO"'; if($nbr_char_extrait == 'ZERO') {echo 'selected="selected"';}
		echo'>0</option>';
		echo '<option value="50"'; if($nbr_char_extrait == '50') {echo 'selected="selected"';} echo'>50</option>';
		echo '<option value="75"'; if($nbr_char_extrait == '75') {echo 'selected="selected"';} echo'>75</option>';
		echo '<option value="100"'; if($nbr_char_extrait == '100') {echo 'selected="selected"';} echo'>100</option>';
		echo '<option value="150"'; if($nbr_char_extrait == '150') {echo 'selected="selected"';} echo'>150</option>';
		echo '<option value="200"'; if($nbr_char_extrait == '200') {echo 'selected="selected"';} echo'>200</option>';
		echo '<option value="250"'; if($nbr_char_extrait == '250') {echo 'selected="selected"';} echo'>250</option>';
		echo '<option value="300"'; if($nbr_char_extrait == '300') {echo 'selected="selected"';} echo'>300</option>';
		echo '<option value="350"'; if($nbr_char_extrait == '350') {echo 'selected="selected"';} echo'>350</option>';
		echo '<option value="400"'; if($nbr_char_extrait == '400') {echo 'selected="selected"';}
		echo'>400</option>';
		echo '</select>';
		echo "<br />";
		//-----------

		echo '<b>'.POSTS_VISIBL_DURING.'</b>';
		echo '<select id="champ_nbr_max_heures" >';
		echo '<option value="ZERO">0H</option>';
		echo '<option value="1"'; if(get_option('abn_nbr_max_heures') == '1') {echo 'selected="selected"';} echo'>1'.HOURS.'</option>';
		echo '<option value="2"'; if(get_option('abn_nbr_max_heures') == '2') {echo 'selected="selected"';} echo'>2'.HOURS.'</option>';
		echo '<option value="4"'; if(get_option('abn_nbr_max_heures') == '4') {echo 'selected="selected"';} echo'>4'.HOURS.'</option>';
		echo '<option value="8"'; if(get_option('abn_nbr_max_heures') == '8') {echo 'selected="selected"';} echo'>8'.HOURS.'</option>';
		echo '<option value="12"'; if(get_option('abn_nbr_max_heures') == '12') {echo 'selected="selected"';} echo'>12'.HOURS.'</option>';
		echo '<option value="24"'; if(get_option('abn_nbr_max_heures') == '24') {echo 'selected="selected"';} echo'>24'.HOURS.'</option>';
		echo '<option value="48"'; if(get_option('abn_nbr_max_heures') == '48') {echo 'selected="selected"';} echo'>48'.HOURS.'</option>';
		echo '<option value="96"'; if(get_option('abn_nbr_max_heures') == '96') {echo 'selected="selected"';} echo'>4'.DAY.'</option>';
		echo '<option value="192"'; if(get_option('abn_nbr_max_heures') == '192') {echo 'selected="selected"';}echo'>8'.DAY.'</option>';
		echo '<option value="720"'; if(get_option('abn_nbr_max_heures') == '720') {echo 'selected="selected"';} echo'>1'.MONTH.'</option>';
		echo '<option value="2160"'; if(get_option('abn_nbr_max_heures') == '2160') {echo 'selected="selected"';} echo'>3'.MONTH.'</option>';
		echo '<option value="4320"'; if(get_option('abn_nbr_max_heures') == '4320') {echo 'selected="selected"';} echo'>6'.MONTH.'</option>';
		echo '<option value="8640"'; if(get_option('abn_nbr_max_heures') == '8640') {echo 'selected="selected"';} echo'>24'.MONTH.'</option>';
		echo '<option value="17280"'; if(get_option('abn_nbr_max_heures') == '17280') {echo 'selected="selected"';} echo'>2'.MONTH.'</option>';
		echo '<option value="34560"'; if(get_option('abn_nbr_max_heures') == '34560') {echo 'selected="selected"';} echo'>48'.MONTH.'</option>';

		echo '</select>';
		echo '<b>'.HOURS_AFTER_PUBLICATION.'</b> <br /> <br />';

		echo '<b>'.HIDE_COMMENTS.'</b> <select id="commentaire"/>';
		echo '<option value="hide"'; if(get_option('abn_commentaire') === "hide"){echo 'selected="selected"';} echo ' >'.YES.'</option>';
		echo '<option value="show" '; if(get_option('abn_commentaire') === "show"){echo 'selected="selected"';} echo '>'.NO.'</option>';
		echo '</select></p>';

		echo '<b>'.LANGUAGE.'</b><select id="champ_langage" >';
		echo '<option value="'.get_option('abn_LANGUAGE').'">'.get_option('abn_LANGUAGE').'</option>';
		echo '</select></p>';
		echo '<b>Message 1:</b><input id="champ_msg_1" type="texte" value="'.get_option('abn_msg_1').'">';
		echo '<input type="submit" value="'.SAVE.'" onClick="saveAbonnementOptions();"><br />';
		echo "<div id='loading' class='loading'><img src='../wp-content/plugins/sabwo-allopass/img/loading.gif'/> </div>";
		echo '</div><div id="response"></div>';
		echo '</div>';
		echo '<a name="help"></a>';
		echo '</div><div id="help">
		
	</div>';

	}
}

?>