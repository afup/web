<?php
/**
 * Fichier site 'RendezVous'
 * 
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 * 
 * @category RendezVous
 * @package  RendezVous
 * @group    Pages
 */

// 0. initialisation (bootstrap) de l'application

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

// 1. chargement des classes nécessaires

require_once 'Afup/AFUP_Rendez_Vous.php';
require_once 'Afup/AFUP_Logs.php';

// 2. récupération et filtrage des données

AFUP_Logs::initialiser($bdd, 0);

$rendezvous = new AFUP_Rendez_Vous($bdd);

$rendezvous->obtenirInscritAConfirmer($_GET['hash']);

if (isset($_GET['hash']) and $champs = $rendezvous->obtenirInscritAConfirmer($_GET['hash'])) {
	if (isset($champs['id_rendezvous']) and is_numeric($champs['id_rendezvous'])) {
		$prochain_rendezvous = $rendezvous->obtenirRendezVousFutur($champs['id_rendezvous']);
	}
}
if (!isset($prochain_rendezvous)) {
	$prochain_rendezvous = $rendezvous->obtenirProchain();
}

if (is_array($prochain_rendezvous)) {
	$prochain_rendezvous['date'] = date("d/m/Y", $prochain_rendezvous['debut']);
	$prochain_rendezvous['debut'] = date("H\hi", $prochain_rendezvous['debut']);
	$prochain_rendezvous['fin'] = date("H\hi", $prochain_rendezvous['fin']);
	$smarty->assign('rendezvous', $prochain_rendezvous);

	if (isset($champs) and is_array($champs)) {
	    $formulaire = &instancierFormulaire();
        $formulaire->setDefaults($champs);
	
	    $formulaire->addElement('hidden'  , 'id'            , $champs['id']);
	    $formulaire->addElement('hidden'  , 'id_rendezvous' , $champs['id_rendezvous']);
	    $formulaire->addElement('hidden'  , 'presence'      , $champs['presence']);
	
	    $formulaire->addElement('header'  , ''              , 'Inscription');
		$formulaire->addElement('text'    , 'nom'           , 'Nom');
		$formulaire->addElement('text'    , 'prenom'        , 'Prénom');
		$formulaire->addElement('text'    , 'entreprise'    , 'Entreprise');
		$formulaire->addElement('text'    , 'email'         , 'Email');
		$formulaire->addElement('text'    , 'telephone'     , 'Téléphone');
	    $formulaire->addElement('select'  , 'confirme'      , 'Confirmation', array(null                        => '',
	                                                                            AFUP_RENDEZ_VOUS_CONFIRME       => 'OUI, je serai bien présent',
                                                                            AFUP_RENDEZ_VOUS_DECLINE        => 'NON, je ne serai pas là finalement',
	                                                                            ));
	
	    $formulaire->addElement('header'  , 'boutons'   , '');

	    $formulaire->addElement('submit'  , 'soumettre'     , 'Modifier');
	    
	    $formulaire->addRule('nom'        , 'Nom manquant'       , 'required');
	    $formulaire->addRule('email'      , 'Email manquant'     , 'required');
	    $formulaire->addRule('email'      , 'Email invalide'     , 'email');
	    $formulaire->addRule('telephone'  , 'Téléphone manquant' , 'required');
	    
	    if ($formulaire->validate()) {
	        $ok = $rendezvous->enregistrerConfirmationInscrit($formulaire);
	
	        if ($ok) {
	            AFUP_Logs::log('Confirmation pour le prochain rendez-vous de '.$formulaire->exportValue('nom'));
	            $smarty->assign('resultat', 'succes');
	            $smarty->assign('message', 'Votre confirmation a bien été prise en compte.');
				$smarty->display('message.html');
				die();
	        } else {
	            $smarty->assign('resultat', 'erreur');
	            $smarty->assign('message', 'Il y a une erreur lors de votre confirmation. Merci de bien vouloir recommencer.');
	        }
	    }
	    
	    $smarty->assign('formulaire', genererFormulaire($formulaire));
		$smarty->display('confirmation.html');
	
	} else {
        $smarty->assign('resultat', 'erreur');
        $smarty->assign('message', 'La confirmation n\'est pas possible. N\'avez-vous pas déjà précisé que vous ne veniez pas ?');
		$smarty->display('message.html');
	}
	
} else {
	$smarty->display('pas-de-rendezvous.html');
}