<?php
	if (!session_id()){
	
       	    session_start();
        }
/*
* Les valeurs par défaut lors de la première installation.
*/
     /* lancer la reqete*/
	if( !get_option('abn_id_site') ) {
			delete_option('abn_id_site');
			add_option('abn_id_site', '123456');
		} elseif( !get_option('abn_document') ) {
			delete_option('abn_document');
			add_option('abn_document', '123456');
		} elseif( !get_option('abn_document_full') ) {
			delete_option('abn_document_full');
			add_option('abn_document_full', '123456/123456/123456');
		} elseif( !get_option('abn_nbr_max_heures') ) {
			delete_option('abn_nbr_max_heures');
			add_option('abn_nbr_max_heures', 'no');
		} elseif( !get_option('abn_char_extrait') ) {
			delete_option('abn_char_extrait');
			add_option('abn_char_extrait', 'zero');
		} elseif( !get_option('abn_LANGUAGE') ) {
			delete_option('abn_LANGUAGE');
			add_option('abn_LANGUAGE', 'fr');
		} elseif( !get_option('abn_commentaire') ) {
			delete_option('abn_commentair');
			add_option('abn_commentaire', 'hide');
		} elseif( !get_option('abn_msg_1') ) {
			delete_option('abn_msg_1');
			add_option('abn_msg_1', '...Connectez-vous');
		}	
		
				
?>