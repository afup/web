<?php

// Impossible to access the file itself
use Afup\Site\Planete\Planete_Billet;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister'));
$tris_valides = array('id', 'titre', 'contenu', 'etat');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);


$planete_billet = new Planete_Billet($bdd);

if ($action == 'lister') {
    $smarty->assign('pertinence', $conf->obtenir('planete|pertinence'));
    $smarty->assign('billets', $planete_billet->obtenirListe('*', 'maj DESC', false, false, 20));
}

?>