<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR');

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_AppelConferencier.php';

$forum_appel = new AFUP_AppelConferencier($bdd);

$sessions = $forum_appel->obtenirListeSessionsPlannifies($config_forum['id']);
$conferenciers = array();
foreach ($sessions as $index => $session) {
	$tmp_conferenciers = $forum_appel->obtenirConferenciersPourSession($session['session_id']);
    foreach ($tmp_conferenciers as $conferencier) {
        if (!isset($conferenciers[$conferencier['conferencier_id']])) {
            $conferenciers[$conferencier['conferencier_id']] = $conferencier;
        }
        $conferenciers[$conferencier['conferencier_id']]['sessions'][] = array(
            'id' => $session['session_id'],
            'titre' => $session['titre'],
        );
    }
}

$smarty->assign('conferenciers', $conferenciers);
$smarty->display('conferenciers.html');
