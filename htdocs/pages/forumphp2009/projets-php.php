<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';
setlocale(LC_TIME, 'fr_FR');

require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_AppelConferencier.php';

$forum_appel = new AFUP_AppelConferencier($bdd);
$sort = 's.titre';
if (isset($_GET['admin_preview']))
{
	$smarty->assign('admin', true);;
	// on affiche tous les projets
	$config_forum['project_ids'] = array();
	$sort = 's.date_soumission DESC';
}

$sessions = $forum_appel->obtenirListeProjets($config_forum['id'],     's.*',
                          $sort,
                         false,
                           false,
                          $config_forum['project_ids']);


foreach ($sessions as $index => $session) {
    $sessions[$index]['conferenciers'] = $forum_appel->obtenirConferenciersPourSession($session['session_id']);
    $sessions[$index]['journees'] = explode(" ", $session['journee']);
}

$smarty->assign('projets', $sessions);
$smarty->display('projets-php.html');
?>