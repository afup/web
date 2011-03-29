<?php

$action = verifierAction(array('listing'));
//$tris_valides = array('i.date', 'i.nom', 'f.societe', 'i.etat');
//$sens_valides = array( 'desc','asc' );
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Rendez_Vous.php';
$rendez_vous = new AFUP_Rendez_Vous($bdd);

if ($action == 'listing') 
{
	$rendezvous = $rendez_vous->obtenir((int)$_GET['id']);
	$list_ordre=" nom ";
    $inscrits = $rendez_vous->obtenirListeInscrits($rendezvous['id'], $list_ordre, FALSE);

    $smarty->assign('rendezvous', $rendezvous);
 //   $smarty->assign('lesrendezvous', $rendezvous->obtenirListe());
    $smarty->assign('inscrits', $inscrits);

}    