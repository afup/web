<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Comptabilite\Comptabilite;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(['lister', 'editer']);
$smarty->assign('action', $action);

$compta = new Comptabilite($bdd);

if ($action == 'lister') {
    $listEvenement = $compta->obtenirListEvenements('liste');
    $smarty->assign('listEvenement', $listEvenement);

    $idevnt = !isset($_GET['idevnt']) || intval($_GET['idevnt']) == 0 ? 8 : $_GET['idevnt'];
    $smarty->assign('idevnt', $idevnt);

    $debit = $compta->obtenirSyntheseEvenement($idevnt, 1);
    $smarty->assign('debit', $debit);

    $credit = $compta->obtenirSyntheseEvenement($idevnt, 2);
    $smarty->assign('credit', $credit);

    $totalDepense = $compta->obtenirTotalSyntheseEvenement($idevnt, 1);
    $smarty->assign('totalDepense', $totalDepense);

    $totalRecette = $compta->obtenirTotalSyntheseEvenement($idevnt, 2);
    $smarty->assign('totalRecette', $totalRecette);

    $difMontant = $totalRecette - $totalDepense ;
    $smarty->assign('difMontant', $difMontant);
}
