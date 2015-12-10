<?php
if (isset($_GET['nolayout']) === false) {
    header('HTTP/1.0 301 Moved Permanently');
    header('Location:http://event.afup.org/php-tour-2016/programme/');
}

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR');

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_AppelConferencier.php';

$forum_appel = new AFUP_AppelConferencier($bdd);
$sessions = $forum_appel->obtenirListeSessionsPlannifies($config_forum['id']);
$journees = array(
  $translator->trans('Lundi 23 mai 2016') => array(),
  $translator->trans('Mardi 24 mai 2016') => array()
);
foreach ($sessions as $index => $session) {
	$session['conferenciers'] = $forum_appel->obtenirConferenciersPourSession($session['session_id']);
  $session['journees'] = explode(" ", $session['journee']);

  if ('23' == date('d', $session['debut'])) {
    $journees[$translator->trans('Lundi 23 mai 2016')][] = $session;
  } else {
    $journees[$translator->trans('Mardi 24 mai 2016')][] = $session;
  }
}

$smarty->assign('journees', $journees);
$smarty->display('sessions.html');
