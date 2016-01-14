<?php

// Impossible to access the file itself
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'detail', 'rechercher'));
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Personnes_Physiques.php';
$personnes_physiques = new AFUP_Personnes_Physiques($bdd);

$administrateurs = $personnes_physiques->getListeAvecDroitsAdministration();

$smarty->assign('administrateurs', $administrateurs);
