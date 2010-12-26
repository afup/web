<?php

$action = verifierAction(array('lister', 'editer'));
//$compte = verifierAction(array('espece','paypal','courant','livreta'));

//$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
//$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

//$compte=$_GET['compte'];

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


if ($action == 'lister') {
	$periode_debut=$listPeriode[$id_periode-1]['date_debut'];
	$periode_fin=$listPeriode[$id_periode-1]['date_fin'];
	
	$debit = $compta->obtenirBilan(1,$periode_debut,$periode_fin);
	$smarty->assign('debit', $debit);
	
	$credit = $compta->obtenirBilan(2,$periode_debut,$periode_fin);
	$smarty->assign('credit', $credit);

//	$journal = $compta->obtenirBilan();
//	$smarty->assign('bilan', $journal);

	$totalDepense = $compta->obtenirTotalBilan(1,$periode_debut,$periode_fin);
	$smarty->assign('totalDepense', $totalDepense);
	
	$totalRecette = $compta->obtenirTotalBilan(2,$periode_debut,$periode_fin);
	$smarty->assign('totalRecette', $totalRecette);
	
	$difMontant = $totalRecette - $totalDepense ;
	$smarty->assign('difMontant', $difMontant);
	
}

?>
