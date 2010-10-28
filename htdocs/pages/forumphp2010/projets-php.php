<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';
setlocale(LC_TIME, 'fr_FR');

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_AppelConferencier.php';

$forum_appel = new AFUP_AppelConferencier($bdd);
$sort = 's.titre';
if (isset($_GET['admin_preview']))
{
	$smarty->assign('admin', true);;
}

$sessions = $forum_appel->obtenirListeProjetsPlannifies($config_forum['id']);

foreach ($sessions as $index => $session) {
    $sessions[$index]['conferenciers'] = $forum_appel->obtenirConferenciersPourSession($session['session_id']);
    $sessions[$index]['journees'] = explode(" ", $session['journee']);
}

$smarty->assign('projets', $sessions);
$smarty->display('projets-php.html');
