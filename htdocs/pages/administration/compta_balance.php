<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Comptabilite\Comptabilite;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(['lister', 'editer','raccourci','view']);
//$compte = verifierAction(array('espece','paypal','courant','livreta'));

//$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
//$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

$details = isset($_GET['details']) && $_GET['details'] ? $_GET['details'] : "";


$compta = new Comptabilite($bdd);

$id_periode = isset($_GET['id_periode']) && $_GET['id_periode'] ? $_GET['id_periode'] : "";

$id_periode = $compta->obtenirPeriodeEnCours($id_periode);
$smarty->assign('id_periode', $id_periode);

$listPeriode = $compta->obtenirListPeriode();
$smarty->assign('listPeriode', $listPeriode);

    $periode_debut=$listPeriode[$id_periode-1]['date_debut'];
    $periode_fin=$listPeriode[$id_periode-1]['date_fin'];

    $smarty->assign('compteurLigne',1);

if ($action == 'lister') {
    $balance = $compta->obtenirBalance('',$periode_debut,$periode_fin);
    $smarty->assign('balance', $balance);

    $totalDepense = $compta->obtenirTotalBalance($periode_debut,$periode_fin, 1);
    $smarty->assign('totalDepense', $totalDepense);

    $totalRecette = $compta->obtenirTotalBalance($periode_debut,$periode_fin, 2);
    $smarty->assign('totalRecette', $totalRecette);

    $difMontant = $totalRecette - $totalDepense ;
    $smarty->assign('difMontant', $difMontant);

    if ($details!='') {
        $dataDetails = $compta->obtenirBalanceDetails($details,$periode_debut,$periode_fin);
        $smarty->assign('dataDetails', $dataDetails);

        $sousTotal = $compta->obtenirSousTotalBalance($details,$periode_debut,$periode_fin);
        $smarty->assign('sousTotal', $sousTotal);
    }
}

if ($action == 'view' && $details) {
    $dataDetails = $compta->obtenirBalanceDetails($details,$periode_debut,$periode_fin);
    $smarty->assign('dataDetails', $dataDetails);

    $sousTotal = $compta->obtenirSousTotalBalance($details,$periode_debut,$periode_fin);
    $smarty->assign('sousTotal', $sousTotal);
}
