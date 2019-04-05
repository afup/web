<?php

// Impossible to access the file itself
use Afup\Site\Rendez_Vous;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'preparer', 'envoyer', 'ajouter', 'modifier', 'supprimer', 'remplir', 'exporter','listing'));
$tris_valides = array('nom', 'entreprise', 'email', 'telephone', 'presence', 'confirme', 'creation');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

$rendez_vous = new Rendez_Vous($bdd);

if ($action == 'lister' || $action== 'listing' ) {
    if (isset($_GET['id'])) {
		$rendezvous = $rendez_vous->obtenir((int)$_GET['id']);
       } else {
		$rendezvous = $rendez_vous->obtenirProchain();
    }
    if (!isset($rendezvous['id'])) {
    	$rendezvous['id'] = 0;
    }
    if (!isset($rendezvous['capacite'])) {
    	$rendezvous['capacite'] = 0;
    }
    $list_ordre = 'creation';
    $list_associatif = false;

    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }

    if ($action == "listing")
    {
    	$list_ordre="nom";
    }

    $inscrits = $rendez_vous->obtenirListeInscrits($rendezvous['id'], $list_ordre, $list_associatif);
    $smarty->assign('lesrendezvous', $rendez_vous->obtenirListe());
    $smarty->assign('nb_inscrits', $rendez_vous->obtenirNombreInscritsQuiViennent($rendezvous['id']));
    $smarty->assign('nb_en_attente', $rendez_vous->obtenirNombreInscritsEnAttente($rendezvous['id']));
    $smarty->assign('capacite', $rendezvous['capacite']);
    $smarty->assign('rendezvous', $rendezvous);
    $smarty->assign('inscrits', $inscrits);
    $smarty->assign('now', time());

} elseif ($action == 'exporter') {
    $smarty->assign('inscrits', $rendez_vous->exporterVersBarCampListeInscritsQuiViennent($_GET['id']));

} elseif ($action == 'remplir') {
	$ok = $rendez_vous->remplirAvecListeAttente($_GET['id']);

    if ($ok) {
        Logs::log('Remplissage du rendez-vous avec la liste d\'attente');
        afficherMessage('Le remplissage avec la liste d\'attente a été effectué', 'index.php?page=rendez_vous&action=lister');
    } else {
        $smarty->assign('erreur', 'Une erreur est survenue lors du remplissage avec la liste d\'attente pour le prochain rendez-vous');
    }

} elseif ($action == 'envoyer') {
    $formulaire = instancierFormulaire();
    $sujet = $rendez_vous->preparerSujetDuMessage();
    $corps = $rendez_vous->preparerCorpsDuMessage($_GET['id']);
    $formulaire->setDefaults(array('sujet' => $sujet,
                                   'corps' => $corps));

    $formulaire->addElement('header'  , ''     , 'Message pour la demande de confirmation du prochain rendez-vous');
    $formulaire->addElement('text'    , 'sujet', 'Sujet');
    $formulaire->addElement('textarea', 'corps', 'Corps', array('cols' => 42, 'rows' => 10));

    $formulaire->addElement('header'  , 'boutons'            , '');
    $formulaire->addElement('submit'  , 'soumettre'          , ucfirst($action));

    $formulaire->addRule('sujet'      , 'Sujet manquant'     , 'required');
    $formulaire->addRule('corps'      , 'Corps manquant'   , 'required');

    if ($formulaire->validate()) {
        $ok = $rendez_vous->envoyerDemandesConfirmation($_GET['id'],
                                                       $formulaire->exportValue('sujet'),
                                                       $formulaire->exportValue('corps'));

        if ($ok) {
            Logs::log('Envoi des emails de demande de confirmation aux inscrits');
            afficherMessage('L\'envoi des emails de demande de confirmation aux inscrits pour le prochain rendez-vous a été effectué', 'index.php?page=rendez_vous&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de l\'envoi des emails de demande de confirmation aux inscrits pour le prochain rendez-vous');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));

} elseif ($action == 'preparer') {
    $formulaire = instancierFormulaire();

	$id = 0;
	$current_year = date('Y');
	if (isset($_GET['id'])) {
		$id = (int)$_GET['id'];
        $champs = $rendez_vous->obtenir($id);
        $champs['date'] = date("Y/m/d", $champs['debut']);
        $champs['debut'] = date("H\hi", $champs['debut']);
        $champs['fin'] = date("H\hi", $champs['fin']);

        $champsSlides = $rendez_vous->obtenirSlides($id);
        for ($i=0;$i<sizeof($champsSlides);$i++) {
        	$champs['slides'.$i]= $champsSlides[$i]['fichier'];
         	$champs['urlslides'.$i]=$champsSlides[$i]['url'];
        }

        $formulaire->setDefaults($champs);
	} else {
	    $formulaire->setDefaults(array('date' => date("Y/m/d", time())));
	}


    $formulaire->addElement('hidden'  , 'id'       , $id);

    $formulaire->addElement('header'  , ''         , 'Informations');
	$formulaire->addElement('text'    , 'titre'    , 'Titre'    , array('size' => 50, 'maxlength' => 255));
    $formulaire->addElement('textarea', 'accroche' , 'Accroche' , array('cols' => 42, 'rows' => 10));
    $formulaire->addElement('textarea', 'theme'    , 'Thème'    , array('cols' => 42, 'rows' => 10));

    $formulaire->addElement('header'  , ''         , 'Organisateur & Horaire');
    $formulaire->addElement('select'  , 'id_antenne', 'Antenne ', $rendez_vous->obtenirListAntennes());
    $options = array('language' => 'fr', 'format' => 'd/m/Y', 'minYear' => "2005", 'maxYear' => $current_year + 3);
	$formulaire->addElement('date'    , 'date'     , 'Date'     , $options);
	$formulaire->addElement('text'    , 'debut'    , 'Heure début (00:00)'    , array('size' => 6, 'maxlength' => 5));
	$formulaire->addElement('text'    , 'fin'      , 'Heure fin (00:00)'      , array('size' => 6, 'maxlength' => 5));

    $formulaire->addElement('header'  , ''         , 'Pratique');
    $formulaire->addElement('textarea', 'lieu'     , 'Lieu'     , array('cols' => 42, 'rows' => 3));
    $formulaire->addElement('text'    , 'url'      , 'Url'      , array('size' => 42));
    $formulaire->addElement('textarea', 'adresse'  , 'Adresse'  , array('cols' => 42, 'rows' => 10));
    $formulaire->addElement('text'    , 'plan'     , 'Plan'     , array('size' => 42));
    $formulaire->addElement('text'    , 'capacite' , 'Capacité' , array('size' => 6, 'maxlength' => 5));

    $formulaire->addElement('header'  , ''         , 'Mode d\'inscriptions');
    $formulaire->addElement('static', null, null, "L'inscription est gérée par le back-office de l'AFUP");
    $grp_inscription = array();
    $grp_inscription[] = $formulaire->createElement('radio', 'inscription', null, 'oui', 1);
    $grp_inscription[] = $formulaire->createElement('radio', 'inscription', null, 'non', 0);
    $formulaire->addGroup($grp_inscription, 'inscription', null, '&nbsp;', false);
    
    $formulaire->addElement('static', null, null, "Si non, saisir l'URL d'enregistrement (possible ne rien remplir) ");
    $formulaire->addElement('text'    , 'url_externe'      , 'Url Externe'      , array('size' => 42));       

    $formulaire->addElement('header'  , ''         , 'Slides');
    $formulaire->addElement('static', null, null, " Fichier ou url (ex : slide share)");

    $formulaire->addElement('text'    , 'urlslides0' , 'Url 1'      , array('size' => 42));
   	$formulaire->addElement('text'    , 'urlslides1' , 'Url 2'      , array('size' => 42));
   	$formulaire->addElement('text'    , 'urlslides2' , 'Url 3'      , array('size' => 42));
   	$formulaire->addElement('text'    , 'urlslides3' , 'Url 4'      , array('size' => 42));
   	

	$formulaire->addElement('file'    , 'slides0' , 'slides 1');
   	$formulaire->addElement('file'    , 'slides1' , 'slides 2 ');   	
   	$formulaire->addElement('file'    , 'slides2' , 'slides 3'); 	 
   	$formulaire->addElement('file'    , 'slides3' , 'slides 4');
   	
   	if ($action == 'preparer') {
		
		for ($i=0;$i<4;$i++) {
			if (isset($champs['slides'.$i])) {		
	    		$chemin = realpath('../../templates/rendezvous/slides/'.$champs['slides'.$i]);
	   			if ($champs['slides'.$i] && file_exists($chemin)) {
	 			     $formulaire->addElement('static', null, null,"<ul> ". $champs['slides'.$i]."</ul>");
	    		}
	   			$formulaire->addElement('hidden'  , 'slides'.$i.'_default'      , $champs['slides'.$i]);
			} 
		}		
	} else {
   			$formulaire->addElement('hidden'  , 'slides_default'            , null);
   	}
   		 
   	
    
    $formulaire->addElement('header'  , 'boutons'   , '');
    $formulaire->addElement('submit'  , 'soumettre' , ucfirst($action));

    $formulaire->addRule('titre'      , 'Titre manquant'     , 'required');
    $formulaire->addRule('date'       , 'Date manquante'     , 'required');
    $formulaire->addRule('debut'      , 'Début manquant'     , 'required');
    $formulaire->addRule('fin'        , 'Fin manquante'      , 'required');
    $formulaire->addRule('id_antenne' , 'Antenne manquante'  , 'required');
    
    if ($formulaire->validate()) {

		$valeurs = $formulaire->exportValues();
	  	for ($i=0;$i<=3;$i++) { 		
    		$file = $formulaire->getElement('slides'.$i);

    		$data = $file->getValue();
    		if ($data['name']) {
    			$file->moveUploadedFile(realpath('../../templates/rendezvous/slides/'));
    			$data = $file->getValue();
    			$valeurs['newslides'.$i] = $data['name'];
    		} else {
    			$valeurs['newslides'.$i] = $formulaire->exportValue('slides'.$i.'_default');
    		}
		}


  	
   	$ok = $rendez_vous->enregistrer($formulaire,$valeurs);
    	 
        if ($ok) {
        	$logdate = $formulaire->exportValue('date');
            Logs::log('Enregistrement du rendez-vous du ' . $logdate["d"] . "/" . $logdate["m"] . "/" . $logdate["Y"] );
            afficherMessage('Le rendez-vous a été enregistré.', 'index.php?page=rendez_vous&action=lister&id='.$id);
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de l\'enregistrement du rendez-vous');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));

} elseif (in_array($action, array('ajouter', 'modifier'))) {
    $formulaire = instancierFormulaire();

	if (isset($_GET['id'])) {
		if ($action == 'modifier') {
			$id = (int)$_GET['id'];
	        $champs = $rendez_vous->obtenirInscrit($id);
			$id_rendezvous = $champs['id_rendezvous'];
	        $formulaire->setDefaults($champs);
		} else {
			$id_rendezvous = (int)$_GET['id'];
			$champs['creation'] = time();
		}
	} else {
        afficherMessage('Il manque l\'identifiant du rendez-vous pour effectuer l\'inscription.', 'index.php?page=rendez_vous&action=lister', true);
	}

    $formulaire->addElement('hidden'  , 'id_rendezvous' , $id_rendezvous);
    $formulaire->addElement('hidden'  , 'id'            , 0);
    $formulaire->addElement('hidden'  , 'creation'      , $champs['creation']);

    $formulaire->addElement('header'  , ''              , 'Inscription');
	$formulaire->addElement('text'    , 'nom'           , 'Nom');
	$formulaire->addElement('text'    , 'prenom'        , 'Prénom');
	$formulaire->addElement('text'    , 'entreprise'    , 'Entreprise');
	$formulaire->addElement('text'    , 'email'         , 'Email');
	$formulaire->addElement('text'    , 'telephone'     , 'Téléphone');

    $formulaire->addElement('header'  , ''              , 'Réservé à l\'administration');
    $formulaire->addElement('select'  , 'presence'      , 'Présence'    , array(null                        => '',
                                                                            AFUP_RENDEZ_VOUS_REFUSE     => 'Refusé',
                                                                            AFUP_RENDEZ_VOUS_VIENT      => 'Vient',
                                                                            AFUP_RENDEZ_VOUS_EN_ATTENTE => 'En attente',
                                                                            ));
    $formulaire->addElement('select'  , 'confirme'      , 'Confirmation', array(null                        => '',
                                                                            AFUP_RENDEZ_VOUS_CONFIRME       => 'Confirme',
                                                                            AFUP_RENDEZ_VOUS_DECLINE        => 'Décline',
                                                                            ));

    $formulaire->addElement('header'  , 'boutons'   , '');
    $formulaire->addElement('submit'  , 'soumettre' , ucfirst($action));

    $formulaire->addRule('nom'        , 'Nom manquant'       , 'required');
    $formulaire->addRule('email'      , 'Email manquant'     , 'required');
    $formulaire->addRule('email'      , 'Email invalide'     , 'email');
    $formulaire->addRule('telephone'  , 'Téléphone manquant' , 'required');

    if ($formulaire->validate()) {
        $ok = $rendez_vous->enregistrerInscrit($formulaire);

        if ($ok) {
            Logs::log('Enregistrement de l\'inscription au prochain rendez-vous');
            afficherMessage('L\'inscription a été enregistrée.', 'index.php?page=rendez_vous&action=lister&id='.$id_rendezvous);
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de l\'enregistrement de l\'inscription');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
} elseif ($action == 'supprimer') {
    if ($rendez_vous->supprimerInscrit($_GET['id'])) {
        Logs::log('Suppression de l\'inscrit ' . $_GET['id'] . ' au rendez-vous');
        afficherMessage('L\'inscrit au rendez-vous a été supprimé', 'index.php?page=rendez_vous&action=lister&id='.$id_rendezvous);
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'inscrit au rendez-vous', 'index.php?page=rendez_vous&action=lister&id='.$id_rendezvous, true);
    }
}
?>
