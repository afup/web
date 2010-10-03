<?php

$action = verifierAction(array('afficher', 'telecharger'));
$tris_valides = array();
$sens_valides = array('asc' , 'desc');
$smarty->assign('action', $action);

require_once 'Afup/AFUP_Forum.php';
require_once 'Afup/AFUP_Droits.php';

if ($action == 'afficher') {
    // Ne rien faire. L'écran affiche simplement un lien.
} elseif ($action == 'telecharger') {
    $forum    = new AFUP_Forum($bdd);
    $forum_id = $forum->obtenirDernier();

    $csv = $forum->obtenirCsvJoindIn($forum_id);

    header('Content-type: text/plain');
    header('Content-disposition: attachment; filename=joind_in_forum_php.csv');
    echo $csv;
    exit;
}
?>
