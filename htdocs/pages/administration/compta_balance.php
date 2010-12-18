<?php

$action = verifierAction(array('lister', 'editer','raccourci'));
//$compte = verifierAction(array('espece','paypal','courant','livreta'));

//$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
//$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

if (isset($_GET['details']) && $_GET['details'])
	$details=$_GET['details'];

	
//$compte=$_GET['compte'];

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Compta.php';
$compta = new AFUP_Compta($bdd);

$id_periode =isset ($_GET['id_periode']);
$id_periode = $compta->obtenirPeriodeEnCours($id_periode);
$smarty->assign('id_periode', $id_periode);

$listPeriode = $compta->obtenirListPeriode();
$smarty->assign('listPeriode', $listPeriode );

if ($action == 'lister') {
	if (isset($_GET['id_periode']) && $_GET['id_periode'])
	{
	$periode_debut=$listPeriode[$_GET['id_periode']-1]['date_debut'];
	$periode_fin=$listPeriode[$_GET['id_periode']-1]['date_fin'];
	} else {
		$periode_debut="";
		$periode_fin="";	
	}
	
	$balance = $compta->obtenirBalance($periode_debut,$periode_fin);
	$smarty->assign('balance', $balance);
	
	$totalDepense = $compta->obtenirTotalBalance(1,$periode_debut,$periode_fin);
	$smarty->assign('totalDepense', $totalDepense);
	
	$totalRecette = $compta->obtenirTotalBalance(2,$periode_debut,$periode_fin);
	$smarty->assign('totalRecette', $totalRecette);
	
	$difMontant = $totalRecette - $totalDepense ;
	$smarty->assign('difMontant', $difMontant);

	if ($details)
	{
		$details = $compta->obtenirBalanceDetails($details,$periode_debut,$periode_fin);
		$smarty->assign('details', $details);
	}

}

?>
