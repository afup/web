<?php
use Afup\Site\Forum\AppelConferencier;

if (isset($_GET['nolayout']) === false) {
    header('HTTP/1.0 301 Moved Permanently');
    header('Location:http://event.afup.org/forum-php-2015/programme/');
}

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR');



$forum_appel = new AppelConferencier($bdd);
$sessions = $forum_appel->obtenirListeSessionsPlannifies($config_forum['id']);
$journees = array(
  'Lundi 23 novembre 2015' => array(),
  'Mardi 24 novembre 2015' => array()
);
foreach ($sessions as $index => $session) {
	$session['conferenciers'] = $forum_appel->obtenirConferenciersPourSession($session['session_id']);
  $session['journees'] = explode(" ", $session['journee']);

  if ('23' == date('d', $session['debut'])) {
    $journees['Lundi 23 novembre 2015'][] = $session;
  } else {
    $journees['Mardi 24 novembre 2015'][] = $session;
  }
}

$smarty->assign('journees', $journees);
$smarty->display('sessions.html');
