<?php

// Impossible to access the file itself
use Afup\Site\Association\Personnes_Physiques;

/** @var \AppBundle\Controller\LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'detail', 'rechercher'));
$smarty->assign('action', $action);


$personnes_physiques = new Personnes_Physiques($bdd);

$administrateurs = $personnes_physiques->getListeAvecDroitsAdministration();

$smarty->assign('administrateurs', $administrateurs);
