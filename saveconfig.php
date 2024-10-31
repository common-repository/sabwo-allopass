<?php 
$root = dirname(dirname(dirname(dirname(__FILE__))));
require_once($root.'/wp-config.php');

/** * 
 * Sauvegarde le formulaire des options
 */
	$id_site = addslashes($_POST['id_site']);
	$document = addslashes($_POST['document']);
	$document_full = addslashes($_POST['document_full']);
	$nbr_char_extrait = addslashes($_POST['nbr_char_extrait']);
	$nbr_max_heures = addslashes($_POST['nbr_max_heures']);
	$msg_1 = addslashes($_POST['msg_1']);
	$language = addslashes($_POST['LANGUAGE']);
	$abn_commentaire = addslashes($_POST['commentaire']);
if ($id_site == "" || 
	$document == "" || 
	$document_full == "" || 
	$nbr_char_extrait == "" ||
	$nbr_max_heures == "" ||
	$abn_commentaire == "" || 
	$msg_1 =="" || 
	$language == "") {
		echo "<div class='abn_error'>".ALL_FIELDS_ARE_REQUIRED."</div>";
	}       
//------------------------------------------------------
//0 et 1 sont des chiffres non autorisés dans les valeurs des champs.
//------------------------------------------------------
		elseif ($id_site == '0' || $id_site == '1' || $document == '0' || $document == '1' || 
		  $document_full == '0' || $document_full == '1' || $nbr_char_extrait == '0' || $nbr_char_extrait == '1' || $nbr_max_heures == '0' || $nbr_max_heures == '1' || 
		 $abn_commentaire == '0' || $abn_commentaire == '1' || 
		  $msg_1 == '0' || $msg_1 == '1' || $language == '0' ||  $language == '1') {
	echo "<div class='abn_error'>".NOT_ALLOWED_0_1."</div> ";
	} else {
     /* lancer la reqete*/
	if( !get_option('abn_commentaire') ) {
			add_option('abn_commentaire', $abn_commentaire);
		} else {
			update_option('abn_commentaire', $abn_commentaire);
		}
	if( !get_option('abn_id_site') ) {
			add_option('abn_id_site', $id_site);
		} else {
			update_option('abn_id_site', $id_site);
		}
	if( !get_option('abn_document') ) {
			add_option('abn_document', $document);
		} else {
			update_option('abn_document', $document);
		}
	if( !get_option('abn_document_full') ) {
			add_option('abn_document_full', $document_full);
		} else {
			update_option('abn_document_full', $document_full);
		}
	if( !get_option('abn_char_extrait') ) {
			add_option('abn_char_extrait', $nbr_char_extrait);
			echo get_option('abn_char_extrait');
		} else {
			update_option('abn_char_extrait', $nbr_char_extrait);
		}

	if( !get_option('abn_nbr_max_heures') ) {
			add_option('abn_nbr_max_heures', $nbr_max_heures);
		} else {
			update_option('abn_nbr_max_heures', $nbr_max_heures);
		}
	if( !get_option('abn_msg_1') ) {
			add_option('abn_msg_1', $msg_1);
		} else {
			update_option('abn_msg_1', $msg_1);
		}
	if( !get_option('abn_LANGUAGE') ) {
			add_option('abn_LANGUAGE', $language);
		} else {
			update_option('abn_LANGUAGE', $language);
		}
		echo "<div class='abn_succes'> ".SAVE_SUCCESSFULLY."</div>";
	}
?>