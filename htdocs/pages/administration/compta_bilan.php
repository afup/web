<?php

$action = verifierAction(array('lister', 'editer'));
//$compte = verifierAction(array('espece','paypal','courant','livreta'));

//$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
//$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

//$compte=$_GET['compte'];

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Compta.php';
$compta = new AFUP_Compta($bdd);

if ($action == 'lister') {
/*    $list_ordre = 'date';
    $list_sens = 'asc';
    $list_associatif = false;
    $list_filtre = false;
*/	


	$debit = $compta->obtenirBilan(1);
	$smarty->assign('debit', $debit);
	
	$credit = $compta->obtenirBilan(2);
	$smarty->assign('credit', $credit);

//	$journal = $compta->obtenirBilan();
//	$smarty->assign('bilan', $journal);

}

?>
