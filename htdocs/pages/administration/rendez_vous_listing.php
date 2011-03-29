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
print_r($rendezvous);
	$list_ordre=" nom ";
    $inscrits = $rendez_vous->obtenirListeInscrits($rendezvous['id'], $list_ordre, FALSE);
echo"<hr>";
print_r($inscrits);
//    $smarty->assign('lesrendezvous', $rendez_vous->obtenirListe());
    $smarty->assign('rendezvous', $rendezvous);
    $smarty->assign('inscrits', $inscrits);

}    