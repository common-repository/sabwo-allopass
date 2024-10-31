/* * Plugin Name: SABWO (Allopass)*/
/* * abonnement.js */


/** * chargement de la page */
$(window).load(function () {
	// Animation de chargement	
	$("#loading").ajaxStart(function(){		   
	$(this).fadeIn();		 
	});
	$("#loading").ajaxStart(function(){
		   $(this).fadeIn();
		 });
	$("#loading").ajaxStop(function(){
	      $(this).fadeOut();
	      });
	$("#loading").hide();
	//pour la navigation par le bouton "Entr�e"
	$(document).keydown(function(e) {
		if ($("#username").length != 0){
			if ( e.keyCode == "13" ) {
				saveAbonnementOptions();
			}
		}
	});
});


/**
 * Enregistrement de la config
 */
function saveAbonnementOptions(){	
	var contenu = $("#contenu option:selected").val();
	var commentaire = $("#commentaire option:selected").val();
	var id_site = $("#champ_id_site").val();
	var document = $("#champ_document").val();
	var document_full = $("#champ_document_full").val();
	var nbr_char_extrait = $("#nbr_char_extrait").val();
	var nbr_max_heures = $("#champ_nbr_max_heures").val();
	var msg_1 = $("#champ_msg_1").val()
	var LANGUAGE = $("#champ_langage option:selected").val();
	$.post("../wp-content/plugins/sabwo-allopass/saveconfig.php", {
	  	contenu:contenu,
	  	commentaire:commentaire,
		id_site:id_site,
		document:document,
		document_full:document_full,
		nbr_char_extrait:nbr_char_extrait,
		nbr_max_heures:nbr_max_heures,
		msg_1:msg_1,
		LANGUAGE:LANGUAGE,
	},
	  function(data){
		$("#response").fadeOut().empty();
		$("#response").fadeIn().append(data);
  });
}