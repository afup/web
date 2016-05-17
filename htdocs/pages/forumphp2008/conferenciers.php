<?php
use Afup\Site\Forum\AppelConferencier;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

setlocale(LC_TIME, 'fr_FR');



$forum_appel = new AppelConferencier($bdd);
$sessions = $forum_appel->obtenirListeSessionsPlannifies(2);

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
?>
