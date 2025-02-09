<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Comptabilite\Comptabilite;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(['lister', 'editer','view']);
$smarty->assign('action', $action);

//$compte=$_GET['compte'];
$details = isset($_GET['details']) && $_GET['details'] ? $_GET['details'] : "";

$compta = new Comptabilite($bdd);

$id_periode = isset($_GET['id_periode']) && $_GET['id_periode'] ? $_GET['id_periode'] : "";

$id_periode = $compta->obtenirPeriodeEnCours($id_periode);
$smarty->assign('id_periode', $id_periode);

$listPeriode = $compta->obtenirListPeriode();
$smarty->assign('listPeriode', $listPeriode);

    $periode_debut=$listPeriode[$id_periode-1]['date_debut'];
    $periode_fin=$listPeriode[$id_periode-1]['date_fin'];

if ($action == 'lister') {
    $debit = $compta->obtenirBilan(1,$periode_debut,$periode_fin);
    $smarty->assign('debit', $debit);

    $credit = $compta->obtenirBilan(2,$periode_debut,$periode_fin);
    $smarty->assign('credit', $credit);

    //	$journal = $compta->obtenirBilan();
    //	$smarty->assign('bilan', $journal);

    $totalDepense = $compta->obtenirTotalBilan($periode_debut,$periode_fin, 1);
    $smarty->assign('totalDepense', $totalDepense);

    $totalRecette = $compta->obtenirTotalBilan($periode_debut,$periode_fin, 2);
    $smarty->assign('totalRecette', $totalRecette);

    $difMontant = $totalRecette - $totalDepense ;
    $smarty->assign('difMontant', $difMontant);

    if ($details!='') {
        $dataDetailsDebit = $compta->obtenirBilanDetails(1, $details,$periode_debut,$periode_fin);
        $smarty->assign('dataDetailsDebit', $dataDetailsDebit);

        $dataDetailsCredit = $compta->obtenirBilanDetails(2, $details,$periode_debut,$periode_fin);
        $smarty->assign('dataDetailsCredit', $dataDetailsCredit);
    }
} elseif ($action == 'view' && $details) {
    $dataDetailsDebit = $compta->obtenirBilanDetails(1, $details,$periode_debut,$periode_fin);
    $smarty->assign('dataDetailsDebit', $dataDetailsDebit);

    $dataDetailsCredit = $compta->obtenirBilanDetails(2, $details,$periode_debut,$periode_fin);
    $smarty->assign('dataDetailsCredit', $dataDetailsCredit);

    $sousTotalDebit = $compta->obtenirSousTotalBilan($periode_debut,$periode_fin,$details, 1);
    $smarty->assign('sousTotalDebit', $sousTotalDebit);

    $sousTotalCredit = $compta->obtenirSousTotalBilan($periode_debut,$periode_fin,$details, 2);
    $smarty->assign('sousTotalCredit', $sousTotalCredit);

    $difMontant = $sousTotalCredit - $sousTotalDebit ;
    $smarty->assign('difMontant', $difMontant);
} elseif ($action == 'supprimer') {
    if ($compta->supprimerEcriture($_GET['id'])) {
        Logs::log('Suppression de l\'écriture ' . $_GET['id']);
        afficherMessage('L\'écriture a été supprimée', 'index.php?page=compta_journal&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'écriture', 'index.php?page=compta_journal&action=lister', true);
    }
}
