<?php        
     $root = dirname(dirname(dirname(dirname(__FILE__))));
	 require_once($root.'/wp-config.php');

	$recall = $_GET["RECALL"];

	$www = get_bloginfo('wpurl'); //$_SESSION['PERMALINK'];

    if (!empty($recall)) {
      $AUTH = urlencode(get_option('abn_document_full'));//$AUTH = urlencode("265118/1067775/688935");
       $r = @file( "http://payment.allopass.com/api/checkcode.apu?code=$recall&auth=$AUTH" );
      // on teste la reponse du serveur ALLOPASS
      if( substr( $r[0],0,2 ) != "OK" ) {
        // Le serveur a repondu ERR ou NOK 
        $_SESSION['sessionCode'] = false;
              // Affichage des erreurs
             if( substr( $r[0],0,3 ) == "NOK" ) {
                  echo "Le code saisi est erron&eacute;. <br /><a href='$www' >cliquez ici pour continuer</a>";
             } elseif ( substr( $r[0],0,3 ) == "ERR" ) {
                  echo "Erreur de connexion. <br /><a href='$www' >cliquez ici pour continuer</a>";
             } else {
                  echo "Une erreur inconnue s'est produite. <br /><a href='$www' >cliquez ici pour continuer</a>";
             }
      } elseif(substr( $r[0],0,2 ) == "OK"){
      $_SESSION['sessionCode'] = true;

         echo "<script language='Javascript'>
         document.location.replace('$www');
         </script>";
    echo "Merci de patienter, Vous allez &ecirc;tre redirig&eacute; automatiquement vers la page...";
   
    echo "<br />Si vous n'&ecirc;tes pas redirig&eacute;, cliquez sur le lien: <a href='$www' >cliquez ici pour continuer</a>";    
      }
}
echo get_option('abn_document_full');
?>