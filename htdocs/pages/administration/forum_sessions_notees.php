<?php

$action = verifierAction(array('lister'));
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_AppelConferencier.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Droits.php';

$forum = new AFUP_Forum($bdd);
$forum_appel = new AFUP_AppelConferencier($bdd);
$droits = new AFUP_Droits($bdd);

if ($action == 'lister') {
    if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
        $_GET['id_forum'] = $forum->obtenirDernier();
    }
    $smarty->assign('id_forum', $_GET['id_forum']);

    $smarty->assign('forums', $forum->obtenirListe());
    $smarty->assign('sessions', $forum_appel->obtenirListeSessionsNotees($_GET['id_forum']));
}

?>