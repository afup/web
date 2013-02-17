<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR');

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_AppelConferencier.php';

$forum_appel = new AFUP_AppelConferencier($bdd);
$sessions = $forum_appel->obtenirListeSessionsPlannifies($config_forum['id']);
$journees = array(
  'Jeudi 21 novembre 2013' => array(),
  'Vendredi 22 novembre 2013' => array()
);
foreach ($sessions as $index => $session) {
	$session['conferenciers'] = $forum_appel->obtenirConferenciersPourSession($session['session_id']);
  $session['journees'] = explode(" ", $session['journee']);

  if ('21' == date('d', $session['debut'])) {
    $journees['Jeudi 21 novembre 2013'][] = $session;
  } else {
    $journees['Vendredi 22 novembre 2013'][] = $session;
  }
}

$smarty->assign('journees', $journees);
$smarty->display('sessions.html');
