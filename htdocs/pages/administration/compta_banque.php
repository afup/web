<?php

$action = verifierAction(array('lister', 'editer'));
//$compte = verifierAction(array('espece','paypal','courant','livreta'));

$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
//$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

if (isset($_GET['compte']) && $_GET['compte'])
	$compte=$_GET['compte'];
else
	$compte="courant";
	
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Compta.php';
$compta = new AFUP_Compta($bdd);

$id_periode =isset ($_GET['id_periode']);
$id_periode = $compta->obtenirPeriodeEnCours($id_periode);
$smarty->assign('id_periode', $id_periode);

$listPeriode = $compta->obtenirListPeriode();
$smarty->assign('listPeriode', $listPeriode );
	
if ($action == 'lister') {
/*    $list_ordre = 'date';
    $list_sens = 'asc';
    $list_associatif = false;
    $list_filtre = false;
	*/
	$journal = $compta->obtenirJournalBanque($compte);
	$smarty->assign('journal', $journal);

}

?>
