<?php

$action = verifierAction(array('lister', 'editer'));
//$compte = verifierAction(array('espece','paypal','courant','livreta'));

$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
//$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

if (isset($_GET['compte']) && $_GET['compte'])
	$compte=$_GET['compte'];
else
	$compte=1;

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

	$smarty->assign('compteurLigne',1);

	$journal = $compta->obtenirJournalBanque($compte,$periode_debut,$periode_fin);
	$smarty->assign('journal', $journal);

	$sousTotal = $compta->obtenirSousTotalJournalBanque($compte,$periode_debut,$periode_fin);
	$smarty->assign('sousTotal', $sousTotal);

	$total = $compta->obtenirTotalJournalBanque($compte,$periode_debut,$periode_fin);
	$smarty->assign('total', $total);

/*
	$totalDepense = $compta->obtenirTotalJournalBanque(1,$compte,$periode_debut,$periode_fin);
	$smarty->assign('totalDepense', $totalDepense);

	$totalRecette = $compta->obtenirTotalJournalBanque(2,$compte,$periode_debut,$periode_fin);
	$smarty->assign('totalRecette', $totalRecette);

	$difMontant = $totalRecette - $totalDepense ;
	$smarty->assign('difMontant', $difMontant);
	*/
}

?>
