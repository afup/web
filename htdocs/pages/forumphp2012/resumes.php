<?php
use Afup\Site\Forum\AppelConferencier;

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR');



$forum_appel = new AppelConferencier($bdd);
$sessions = $forum_appel->obtenirListeSessionsAvecResumes($config_forum['id']);

foreach ($sessions as $index => $session) {
    $sessions[$index]['conferenciers'] = $forum_appel->obtenirConferenciersPourSession($session['session_id']);
    $sessions[$index]['journees'] = explode(" ", $session['journee']);
}

$smarty->assign('sessions', $sessions);
$smarty->display('resumes.html');
