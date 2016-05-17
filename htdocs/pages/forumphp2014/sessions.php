<?php
use Afup\Site\Forum\AppelConferencier;

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR');



$forum_appel = new AppelConferencier($bdd);
$sessions = $forum_appel->obtenirListeSessionsPlannifies($config_forum['id']);
$journees = array(
  'Jeudi 23 octobre 2014' => array(),
  'Vendredi 24 octobre 2014' => array()
);
foreach ($sessions as $index => $session) {
	$session['conferenciers'] = $forum_appel->obtenirConferenciersPourSession($session['session_id']);
  $session['journees'] = explode(" ", $session['journee']);

  if ('23' == date('d', $session['debut'])) {
    $journees['Jeudi 23 octobre 2014'][] = $session;
  } else {
    $journees['Vendredi 24 octobre 2014'][] = $session;
  }
}

$smarty->assign('journees', $journees);
$smarty->display('sessions.html');
