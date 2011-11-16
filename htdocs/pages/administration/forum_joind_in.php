<?php

$action = verifierAction(array('afficher', 'telecharger_joindin', 'telecharger_xmliphone'));
$tris_valides = array();
$sens_valides = array('asc' , 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Droits.php';

if ($action == 'afficher') {
    // Ne rien faire. L'écran affiche simplement un lien.
} elseif ($action == 'telecharger_joindin') {
    $forum    = new AFUP_Forum($bdd);
    $forum_id = $forum->obtenirDernier();

    $csv = $forum->obtenirCsvJoindIn($forum_id);

    header('Content-type: text/plain');
    header('Content-disposition: attachment; filename=joind_in_forum_php.csv');
    echo $csv;
    exit;
} elseif ($action == 'telecharger_xmliphone') {
    $forum    = new AFUP_Forum($bdd);
    $forum_id = $forum->obtenirDernier();

    $xml = $forum->obtenirXmlPourAppliIphone($forum_id);

    header('Content-type: text/xml');
    header('Content-disposition: attachment; filename=appli_iphone_forum_php.xml');
    echo $xml;
    exit;
}
?>
