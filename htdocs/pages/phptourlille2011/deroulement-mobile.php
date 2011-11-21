<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR');

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_AppelConferencier.php';

$appel = new AFUP_AppelConferencier($bdd);
$sessions = $appel->obtenirListeSessionsPlannifies($config_forum['id']);

$forum = new AFUP_Forum($bdd);
$deroulement = $forum->afficherDeroulementMobile($sessions);

$smarty->assign('deroulement', $deroulement);
$smarty->display('deroulement-mobile.html');
