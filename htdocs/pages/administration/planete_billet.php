<?php

$action = verifierAction(array('lister'));
$tris_valides = array('id', 'titre', 'contenu', 'etat');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

require_once 'Afup/AFUP_Planete_Billet.php';
$planete_billet = new AFUP_Planete_Billet($bdd);

if ($action == 'lister') {
    $smarty->assign('pertinence', $conf->obtenir('planete|pertinence'));
    $smarty->assign('billets', $planete_billet->obtenirListe('*', 'id DESC', false, false, 20));
}

?>