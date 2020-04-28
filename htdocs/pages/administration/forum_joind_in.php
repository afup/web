<?php

// Impossible to access the file itself
use Afup\Site\Forum\Forum;

/** @var \AppBundle\Controller\LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('telecharger_joindin'));
$tris_valides = array();
$sens_valides = array('asc' , 'desc');
$smarty->assign('action', $action);


if ($action == 'telecharger_joindin') {
    $forum    = new Forum($bdd);

    if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
        $forum_id = $forum->obtenirDernier();
    } else {
        $forum_id = $_GET['id_forum'];
    }

    $csv = $forum->obtenirCsvJoindIn($forum_id);

    header('Content-type: text/plain');
    header('Content-disposition: attachment; filename=joind_in_forum_php.csv');
    echo $csv;
    exit;
}
