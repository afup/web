<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Comptabilite\Comptabilite;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction([
    'lister',
    'debit',
    'credit',
    'ajouter',
    'modifier',
    'modifier_colonne',
    'upload_attachment',
]);

$smarty->assign('action', $action);

$compta = new Comptabilite($bdd);


$id_periode = isset($_GET['id_periode']) && $_GET['id_periode'] ? $_GET['id_periode'] : "";

$id_periode = $compta->obtenirPeriodeEnCours($id_periode);
$smarty->assign('id_periode', $id_periode);

$listPeriode = $compta->obtenirListPeriode();
$smarty->assign('listPeriode', $listPeriode);


$periode_debut = $listPeriode[$id_periode - 1]['date_debut'];
$periode_fin = $listPeriode[$id_periode - 1]['date_fin'];

if (in_array($action, ['lister', 'debit', 'credit'])) {
    $alsoDisplayClassifed = isset($_GET['also_display_classifed_entries']) && $_GET['also_display_classifed_entries'];

    $smarty->assign('also_display_classifed_entries', $alsoDisplayClassifed);
}

if (in_array($action, ['lister', 'debit', 'credit'])) {
    $smarty->assign('categories', $compta->obtenirListCategoriesJournal());
    $smarty->assign('events', $compta->obtenirListEvenementsJournal());
    $smarty->assign('payment_methods', $compta->obtenirListReglementsJournal());
}

if ($action == 'lister') {
    // Accounting lines for the selected period
    $journal = $compta->obtenirJournal('', $periode_debut, $periode_fin, !$alsoDisplayClassifed);
    $smarty->assign('journal', $journal);
} elseif ($action == 'debit') {
    $journal = $compta->obtenirJournal('1',$periode_debut,$periode_fin, !$alsoDisplayClassifed);
    $smarty->assign('journal', $journal);
} elseif ($action == 'credit') {
    $journal = $compta->obtenirJournal('2',$periode_debut,$periode_fin, !$alsoDisplayClassifed);
    $smarty->assign('journal', $journal);
}
