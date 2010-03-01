<?php
/**
 * Fichier principal site 'RendezVous'
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

require_once dirname(__FILE__) . '/../../include/prepend.inc.php';

// 1. chargement des classes nécessaires

require_once 'afup/AFUP_Rendez_Vous.php';
require_once 'afup/AFUP_Logs.php';

// 2. récupération et filtrage des données

AFUP_Logs::initialiser($bdd, 0);

$rendezvous = new AFUP_Rendez_Vous($bdd);
if (isset($_GET['id'])) {
	$prochain_rendezvous = $rendezvous->obtenirRendezVousFutur((int)$_GET['id']);
} else {
	$prochain_rendezvous = $rendezvous->obtenirProchain();
}

if (isset($prochain_rendezvous) and is_array($prochain_rendezvous)) {
	$prochain_rendezvous['date'] = date("d/m/Y", $prochain_rendezvous['debut']);
	$prochain_rendezvous['debut'] = date("H\hi", $prochain_rendezvous['debut']);
	$prochain_rendezvous['fin'] = date("H\hi", $prochain_rendezvous['fin']);
	$prochain_rendezvous['est_futur'] = TRUE;
	$smarty->assign('rendezvous', $prochain_rendezvous);

	if ($rendezvous->accepteSurListeAttenteUniquement($prochain_rendezvous['id'])) {
        $smarty->assign('resultat', 'erreur');
        $smarty->assign('message', 'Attention, les inscriptions sont closes. Votre inscription sera mise sur liste d\'attente. Si des places se libèrent, vous recevrez un email.');
	}
	if ($rendezvous->estComplet($prochain_rendezvous['id'])) {
		$smarty->display('rendezvous-complet.html');
		die();
	}
	
    $formulaire = &instancierFormulaire();

    $formulaire->addElement('hidden'  , 'id_rendezvous' , $prochain_rendezvous['id']);
    $formulaire->addElement('hidden'  , 'id'            , 0);
    $formulaire->addElement('hidden'  , 'creation'      , time());
    $formulaire->addElement('hidden'  , 'presence'      , 0);
    $formulaire->addElement('hidden'  , 'confirme'      , 0);

    $formulaire->addElement('header'  , ''              , 'Inscription');
	$formulaire->addElement('text'    , 'nom'           , 'Nom');
	$formulaire->addElement('text'    , 'entreprise'    , 'Entreprise');
	$formulaire->addElement('text'    , 'email'         , 'Email');
	$formulaire->addElement('text'    , 'telephone'     , 'Téléphone');
    $formulaire->addElement('submit'  , 'soumettre'     , 'S\'inscrire');
    
    $formulaire->addRule('nom'        , 'Nom manquant'       , 'required');
    $formulaire->addRule('email'      , 'Email manquant'     , 'required');
    $formulaire->addRule('email'      , 'Email invalide'     , 'email');
    $formulaire->addRule('telephone'  , 'Téléphone manquant' , 'required');
    
    if ($formulaire->validate()) {
        $ok = $rendezvous->enregistrerInscrit($formulaire);

        if ($ok) {
            AFUP_Logs::log('Pré-inscription au prochain rendez-vous de '.$formulaire->exportValue('nom'));
            $smarty->assign('resultat', 'succes');
            $smarty->assign('message', 'Votre pré-inscription a bien été prise en compte.');
			$smarty->display('message.html');
			die();
        } else {
            $smarty->assign('resultat', 'erreur');
            $smarty->assign('message', 'Il y a une erreur lors de votre pré-inscription. Merci de bien vouloir recommencer.');
        }
    }
    
    $smarty->assign('formulaire', genererFormulaire($formulaire));
	$smarty->display('prochain-rendezvous.html');

} else {
	$smarty->display('pas-de-rendezvous.html');
}