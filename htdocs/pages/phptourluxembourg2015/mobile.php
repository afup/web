<?php
use Afup\Site\Forum\AppelConferencier;

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR.utf8', 'fra');



$forum_appel = new AppelConferencier($bdd);
$sessions = $forum_appel->obtenirListeSessionsPlannifies($config_forum['id']);

$conferenciers = array();

$journees[0] = array();
$journees[1] = array();
$deuxprochaines = array();
foreach ($sessions as $index => $session) {
  $session['conferenciers'] = $forum_appel->obtenirConferenciersPourSession($session['session_id']);
  $session['journees'] = explode(" ", $session['journee']);

  if ('12' == date('d', $session['debut'])) {
    $journees[0][] = $session;
  } else {
    $journees[1][] = $session;
  }

  if ($session['fin'] > time() && count($deuxprochaines) < 4) {
    $deuxprochaines[] = $session;
  }

  foreach ($session['conferenciers'] as $conferencier) {
    if ('À définir' == $conferencier['nom']) {
      continue;
    }

    if (!isset($conferenciers[$conferencier['conferencier_id']])) {
      $conferencier['prenom'] = ucfirst(strtolower($conferencier['prenom']));
      $conferencier['nom'] = strtoupper($conferencier['nom']);
      $conferenciers[$conferencier['conferencier_id']] = $conferencier;
    }

    $conferenciers[$conferencier['conferencier_id']]['sessions'][] = array(
      'id' => $session['session_id'],
      'titre' => $session['titre'],
    );
  }

}

function compareNames($a, $b) {
  if ($a['prenom'] == $b['prenom']) {
    return 0;
  }

  return ($a['prenom'] < $b['prenom']) ? -1 : 1;
}
uasort($conferenciers, 'compareNames');

$smarty->assign('conferenciers', array_values($conferenciers));
$smarty->assign('deuxprochaines', $deuxprochaines);
$smarty->assign('journees', $journees);
$smarty->display('mobile.html');
exit();