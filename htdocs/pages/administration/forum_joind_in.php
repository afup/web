<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Forum\Forum;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(['telecharger_joindin']);
$tris_valides = [];
$sens_valides = ['asc' , 'desc'];
$smarty->assign('action', $action);


if ($action == 'telecharger_joindin') {
    $forum    = new Forum($bdd);

    $forum_id = !isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0 ? $forum->obtenirDernier() : $_GET['id_forum'];

    $csv = $forum->obtenirCsvJoindIn($forum_id);

    header('Content-type: text/plain');
    header('Content-disposition: attachment; filename=joind_in_forum_php.csv');
    echo $csv;
    exit;
}
