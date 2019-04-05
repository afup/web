<?php

// 0. initialisation (bootstrap) de l'application

use Afup\Site\Rendez_Vous;
use Afup\Site\Utils\Logs;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

// 1. chargement des classes nécessaires




// 2. récupération et filtrage des données

Logs::initialiser($bdd, 0);

$rendezvous = new Rendez_Vous($bdd);
if (isset($_GET['id'])) {
	$archive_rendezvous = $rendezvous->obtenirRendezVousPasse((int)$_GET['id']);
} else {
	$archive_rendezvous = $rendezvous->obtenirProchain();
}

if (isset($archive_rendezvous) and is_array($archive_rendezvous)) {
	$archive_rendezvous['date'] = date("d/m/Y", $archive_rendezvous['debut']);
	$archive_rendezvous['debut'] = date("H\hi", $archive_rendezvous['debut']);
	$archive_rendezvous['fin'] = date("H\hi", $archive_rendezvous['fin']);
	$smarty->assign('rendezvous', $archive_rendezvous);

	if ($rendezvous->accepteSurListeAttenteUniquement($archive_rendezvous['id'])) {
        $smarty->assign('resultat', 'erreur');
        $smarty->assign('message', 'Attention, les inscriptions sont closes. Votre inscription sera mise sur liste d\'attente. Si des places se lib�rent, vous recevrez un email.');
	}
	if ($rendezvous->estComplet($archive_rendezvous['id'])) {
		$smarty->display('rendezvous-complet.html');
		die();
	}
	
    $formulaire = instancierFormulaire();

    $formulaire->addElement('hidden'  , 'id_rendezvous' , $archive_rendezvous['id']);
    $formulaire->addElement('hidden'  , 'id'            , 0);
    $formulaire->addElement('hidden'  , 'creation'      , time());
    $formulaire->addElement('hidden'  , 'presence'      , 0);
    $formulaire->addElement('hidden'  , 'confirme'      , 0);

    $formulaire->addElement('header'  , ''              , 'Inscription');
	$formulaire->addElement('text'    , 'nom'           , 'Nom');
	$formulaire->addElement('text'    , 'entreprise'    , 'Entreprise');
	$formulaire->addElement('text'    , 'email'         , 'Email');
	$formulaire->addElement('text'    , 'telephone'     , 'T�l�phone');
    $formulaire->addElement('submit'  , 'soumettre'     , 'S\'inscrire');
    
    $formulaire->addRule('nom'        , 'Nom manquant'       , 'required');
    $formulaire->addRule('email'      , 'Email manquant'     , 'required');
    $formulaire->addRule('email'      , 'Email invalide'     , 'email');
    $formulaire->addRule('telephone'  , 'Téléphone manquant' , 'required');
    
    if ($formulaire->validate()) {
        $ok = $rendezvous->enregistrerInscrit($formulaire);

        if ($ok) {
            Logs::log('Pré-inscription au prochain rendez-vous de '.$formulaire->exportValue('nom'));
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
	$smarty->display('archive-rendezvous.html');

} else {
	$smarty->display('pas-de-rendezvous.html');
}
