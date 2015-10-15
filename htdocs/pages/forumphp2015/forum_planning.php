<?php
require_once __DIR__ . '/../../include/prepend.inc.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_AppelConferencier.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';
require_once __DIR__ . '/_config.inc.php';

$id_forum = $config_forum['id'];

$forum = new AFUP_Forum($bdd);
$forum_appel = new AFUP_AppelConferencier($bdd);

$rs_forum = $forum->obtenir($id_forum);
$annee_forum = $rs_forum['forum_annee'];

$sessions = $forum_appel->obtenirListeSessionsPlannifies($id_forum);
$salles = $forum_appel->obtenirListeSalles($id_forum, true);
$smarty->assign('agenda', $forum->genAgenda($annee_forum, false, false, $id_forum, '/forum-php-2015/programme/#$1'));
$smarty->assign('id_forum', $id_forum);
$smarty->assign('forums', $forum->obtenirListe());
$smarty->assign('sessions', $sessions);
$smarty->display('forum_planning.html');
