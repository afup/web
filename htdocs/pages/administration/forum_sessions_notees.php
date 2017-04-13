<?php

// Impossible to access the file itself
use Afup\Site\Forum\AppelConferencier;
use Afup\Site\Forum\Forum;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister'));
$smarty->assign('action', $action);





$forum = new Forum($bdd);
$forum_appel = new AppelConferencier($bdd);

if ($action == 'lister') {
    if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
        $_GET['id_forum'] = $forum->obtenirDernier();
    }
    $smarty->assign('id_forum', $_GET['id_forum']);

    $smarty->assign('forums', $forum->obtenirListe());
    $smarty->assign('sessions', $forum_appel->obtenirListeSessionsNotees($_GET['id_forum']));
}

?>