<?php

// Cette page attend deux paramètres : 
// - session_id : identifiant de la conférence
// - conferencier_id : identifiant du conférencier
 
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';
require_once 'afup/AFUP_AppelConferencier.php';

$conferences_manager = new AFUP_AppelConferencier($bdd) ; 
$session = $conferences_manager->obtenirSession($_GET['session_id'], '*') ;
$conferencier = $conferences_manager->obtenirConferencier($_GET['conferencier_id'], '*') ;  
$planning = $conferences_manager->obtenirPlanningDeSession($_GET['session_id']) ; 
$date_horaire_array = getdate($planning['debut']) ; 
$date_horaire = $date_horaire_array['mday'].'/'.$date_horaire_array['mon'].' à '.$date_horaire_array['hours'].':'.str_pad($date_horaire_array['minutes'], 2, '0', STR_PAD_LEFT) ; 

if(empty($session) || empty($conferencier))
{
  header('Location: /pages/forumphp'.$config_forum['annee'].'/index.php') ; 
}

// var_dump($conferencier) ; var_dump($session) ; var_dump($date_horaire_array) ; 

$smarty->assign('conferencier', $conferencier) ; 
$smarty->assign('session', $session) ; 
$smarty->assign('date_horaire', $date_horaire) ; 
$smarty->display('rappel_conferenciers.html') ; 
