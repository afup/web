<?php
use Afup\Site\Forum\AppelConferencier;

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale(LC_TIME, 'fr_FR');



$forum_appel = new AppelConferencier($bdd);

$sessions = $forum_appel->obtenirListeSessionsPlannifies($config_forum['id']);
$conferenciers = array();
foreach ($sessions as $index => $session) {
	$tmp_conferenciers = $forum_appel->obtenirConferenciersPourSession($session['session_id']);

  foreach ($tmp_conferenciers as $conferencier) {
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
$smarty->assign('conferenciers', $conferenciers);
$smarty->display('conferenciers.html');
