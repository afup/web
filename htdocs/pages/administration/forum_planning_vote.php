<?php

$action = verifierAction(array('lister', 'mail','envoyer'));
$tris_valides = array();
$sens_valides = array('asc' , 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_AppelConferencier.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Droits.php';

$forum = new AFUP_Forum($bdd);
$forum_appel = new AFUP_AppelConferencier($bdd);
$droits = new AFUP_Droits($bdd);
$identifiant = $droits->obtenirIdentifiant();
$forum_vote_id= $forum->obtenirDernier();
if ($action == 'lister') {
    // Valeurs par défaut des paramètres de tri
    $vote = isset( $_POST['vote']) ? (int) $_POST['vote'] : 0;
    $session_id = isset( $_POST['session_id']) ? (int) $_POST['session_id'] : 0;
    if ($vote > 0 && $session_id >  0 &&  $forum_appel->dejaVote($identifiant, $session_id) === false )
    {
    	      $today = date('Y-m-d');
            $salt = $forum_appel->obtenirGrainDeSel($identifiant);
            $res = $forum_appel->noterLaSession($session_id, $vote, $salt, $today);
            $forum_appel->aVote($identifiant,$session_id);
            AFUP_Logs::log($_SESSION['afup_login'] . ' a voté sur la session n°' . $session_id);
    }
    $sessions_all = $forum_appel->obtenirListeSessionsPlannifies($forum_vote_id);
    $sessions_non_votes = array();

    foreach ($sessions_all as $session)
    {
    	if ($forum_appel->dejaVote($identifiant, $session['session_id']) === false)
    	{
    		$sessions_non_votes[] = $session;
    	};
    }
    $smarty->assign('sessions', $sessions_non_votes);




}
elseif($action =='envoyer')
{
        $ok = $forum->envoyeMailVotePlanning();;

        if ($ok !== false) {
            AFUP_Logs::log('Envoi du mail aux membres pour le vote des sessions');
            afficherMessage('Les mails ont été envoyés ('.$ok.')', 'index.php');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de la préparation des personnes physiques');
        }

}

?>
