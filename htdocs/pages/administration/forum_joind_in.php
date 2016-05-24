<?php

// Impossible to access the file itself
use Afup\Site\Forum\Forum;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('afficher', 'telecharger_joindin', 'telecharger_xmliphone'));
$tris_valides = array();
$sens_valides = array('asc' , 'desc');
$smarty->assign('action', $action);




if ($action == 'afficher') {
    // Ne rien faire. L'écran affiche simplement un lien.
} elseif ($action == 'telecharger_joindin') {
    $forum    = new Forum($bdd);
    $forum_id = $forum->obtenirDernier();

    $csv = $forum->obtenirCsvJoindIn($forum_id);

    header('Content-type: text/plain');
    header('Content-disposition: attachment; filename=joind_in_forum_php.csv');
    echo $csv;
    exit;
} elseif ($action == 'telecharger_xmliphone') {
    $forum    = new Forum($bdd);
    $forum_id = $forum->obtenirDernier();

    $xml = $forum->obtenirXmlPourAppliIphone($forum_id);

    header('Content-type: text/xml');
    header('Content-disposition: attachment; filename=appli_iphone_forum_php.xml');
    echo $xml;
    exit;
}
?>
