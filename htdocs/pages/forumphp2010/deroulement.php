<?php
use Afup\Site\Forum\AppelConferencier;
use Afup\Site\Forum\Forum;

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR');

$appel = new AppelConferencier($bdd);
$sessions = $appel->obtenirListeSessionsPlannifies($config_forum['id']);

$forum = new Forum($bdd);
$deroulement = $forum->afficherDeroulement($sessions);

$smarty->assign('deroulement', $deroulement);
$smarty->display('deroulement.html');
