<?php

$action = verifierAction(array('lister', 'editer'));

$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

//$idevnt=isset($_GET['idevnt']);




require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Compta.php';
$compta = new AFUP_Compta($bdd);

	$evenement = $compta->obtenirFormesEvenements();
	$smarty->assign('evenements', $evenement);

if ($action == 'lister' ) {
 /*   $list_ordre = 'date';
    $list_sens = 'asc';
    $list_associatif = false;
    $list_filtre = false;
	*/
if (isset($idevnt) && $idevnt=='') $idevnt='8'; else $idvent=$_GET['idevnt'];
$smarty->assign('idevnt', $idevnt);

	$debit = $compta->obtenirSyntheseEvenement(1,$idevnt);
	$smarty->assign('debit', $debit);
	
	$credit = $compta->obtenirSyntheseEvenement(2,$idevnt);
	$smarty->assign('credit', $credit);
	
}

?>
