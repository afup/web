<?php

// Impossible to access the file itself
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'telecharger_devis', 'telecharger_facture', 'envoyer_facture', 'envoyer_tout', 'facturer_facture', 'supprimer_facture', 'changer_date_reglement'));
$tris_valides = array('date_facture', 'email', 'societe', 'etat');
$sens_valides = array('asc' , 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Facturation_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';
$forum = new AFUP_Forum($bdd);
$forum_facturation = new AFUP_Facturation_Forum($bdd);

if ($action == 'lister') {
    // Valeurs par défaut des paramètres de tri
    $list_champs = 'reference, date_facture, montant, email, societe, etat, facturation, date_reglement, nom, prenom';
    $list_ordre = 'date_facture DESC';
    $list_associatif = false;
    $list_filtre = false;

    // Modification des paramètres de tri en fonction des demandes passées en GET
    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }
    if (isset($_GET['filtre'])) {
        $list_filtre = $_GET['filtre'];
    }

    if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
        $_GET['id_forum'] = $forum->obtenirDernier();
    }
    $smarty->assign('id_forum', $_GET['id_forum']);


    // Mise en place de la liste dans le scope de smarty
    $smarty->assign('forums', $forum->obtenirListe());
    $smarty->assign('facturations', $forum_facturation->obtenirListe($_GET['id_forum'], $list_champs, $list_ordre, $list_associatif, $list_filtre));
} elseif ($action == 'telecharger_devis') {
	$forum_facturation->genererDevis($_GET['ref']);
} elseif ($action == 'telecharger_facture') {
	$forum_facturation->genererFacture($_GET['ref']);
} elseif ($action == 'envoyer_facture'){
	if($forum_facturation->envoyerFacture($_GET['ref'])){
		AFUP_Logs::log('Envoi par email de la facture n°' . $_GET['ref']);
		afficherMessage('La facture a été envoyée', 'index.php?page=forum_facturation&action=lister');
	} else {
		afficherMessage("La facture n'a pas pu être envoyée", 'index.php?page=forum_facturation&action=lister', true);
	}
} elseif ($action == 'envoyer_tout'){
	if ($forum_facturation->envoyerATous($_GET['id_forum'])) {
		afficherMessage('Les factures ont été envoyées', 'index.php?page=forum_facturation&action=lister');
	} else {
		afficherMessage('Au moins une facture n\'a pas pu être envoyé. Se conférer aux logs pour plus de détails', 'index.php?page=forum_facturation&action=lister', true);
	}
} elseif ($action == 'facturer_facture'){
	if($forum_facturation->estFacture($_GET['ref'])){
		AFUP_Logs::log('Facturation => facture n°' . $_GET['ref']);
		afficherMessage('La facture est prise en compte', 'index.php?page=forum_facturation&action=lister');
	} else {
		afficherMessage("La facture n'a pas pu être prise en compte", 'index.php?page=forum_facturation&action=lister', true);
	}
} elseif ($action == 'supprimer_facture'){
	if($forum_facturation->supprimerFacturation($_GET['ref'])){
		AFUP_Logs::log('Supprimer => facture n°' . $_GET['ref']);
		afficherMessage('La facture est supprimée', 'index.php?page=forum_facturation&action=lister');
	} else {
		afficherMessage("La facture n'a pas pu être supprimée", 'index.php?page=forum_facturation&action=lister', true);
	}
} elseif ($action == 'changer_date_reglement'){
    $reglement = strtotime(implode('-', array_reverse(explode('/', $_GET['reglement']))));
    if ($forum_facturation->changerDateReglement($_GET['ref'], $reglement)) {
		afficherMessage('La date de réglement a été changée', 'index.php?page=forum_facturation&action=lister');
    } else {
		afficherMessage('La date de réglement n\'a pas été changée', 'index.php?page=forum_facturation&action=lister', true);
    }
}
?>