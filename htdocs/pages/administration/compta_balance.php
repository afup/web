<?php

$action = verifierAction(array('lister', 'editer','raccourci','view'));
//$compte = verifierAction(array('espece','paypal','courant','livreta'));

//$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
//$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

if (isset($_GET['details']) && $_GET['details'])
	$details=$_GET['details'];
else
	$details ="";


require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Compta.php';
$compta = new AFUP_Compta($bdd);

if (isset($_GET['id_periode']) && $_GET['id_periode']) 
	$id_periode=$_GET['id_periode'];
else
	$id_periode="";
	 
$id_periode = $compta->obtenirPeriodeEnCours($id_periode);
$smarty->assign('id_periode', $id_periode);

$listPeriode = $compta->obtenirListPeriode();
$smarty->assign('listPeriode', $listPeriode );

	$periode_debut=$listPeriode[$id_periode-1]['date_debut'];
	$periode_fin=$listPeriode[$id_periode-1]['date_fin'];

	$smarty->assign('compteurLigne',1);
	
if ($action == 'lister') {
	
	$balance = $compta->obtenirBalance('',$periode_debut,$periode_fin);
	$smarty->assign('balance', $balance);
	
	$totalDepense = $compta->obtenirTotalBalance(1,$periode_debut,$periode_fin);
	$smarty->assign('totalDepense', $totalDepense);
	
	$totalRecette = $compta->obtenirTotalBalance(2,$periode_debut,$periode_fin);
	$smarty->assign('totalRecette', $totalRecette);
	
	$difMontant = $totalRecette - $totalDepense ;
	$smarty->assign('difMontant', $difMontant);

	if ($details!='')
	{
		$dataDetails = $compta->obtenirBalanceDetails($details,$periode_debut,$periode_fin);
		$smarty->assign('dataDetails', $dataDetails);

		$sousTotal = $compta->obtenirSousTotalBalance($details,$periode_debut,$periode_fin);
		$smarty->assign('sousTotal', $sousTotal);		
	}

}

if ($action == 'view' && $details) {

		$dataDetails = $compta->obtenirBalanceDetails($details,$periode_debut,$periode_fin);
		$smarty->assign('dataDetails', $dataDetails);

		$sousTotal = $compta->obtenirSousTotalBalance($details,$periode_debut,$periode_fin);
		$smarty->assign('sousTotal', $sousTotal);		
		

}		


?>
