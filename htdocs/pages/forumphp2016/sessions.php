<?php
use Afup\Site\Forum\AppelConferencier;

if (isset($_GET['nolayout']) === false) {
    header('HTTP/1.0 301 Moved Permanently');
    header('Location:http://event.afup.org/forum-php-2016/programme/');
}

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR');



$forum_appel = new AppelConferencier($bdd);
$sessions = $forum_appel->obtenirListeSessionsPlannifies($config_forum['id']);
$journees = array(
  $translator->trans('jeudi 27 octobre 2016') => array(),
  $translator->trans('vendredi 28 octobre 2016') => array()
);
foreach ($sessions as $index => $session) {
	$session['conferenciers'] = $forum_appel->obtenirConferenciersPourSession($session['session_id']);
  $session['journees'] = explode(" ", $session['journee']);

  if ('27' == date('d', $session['debut'])) {
    $journees[$translator->trans('jeudi 27 octobre 2016')][] = $session;
  } else {
    $journees[$translator->trans('vendredi 28 octobre 2016')][] = $session;
  }
}

$smarty->assign('journees', $journees);
$smarty->display('sessions.html');
