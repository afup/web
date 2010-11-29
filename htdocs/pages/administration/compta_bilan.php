<?php

$action = verifierAction(array('lister', 'editer'));
//$compte = verifierAction(array('espece','paypal','courant','livreta'));

$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

$compte=$_GET['compte'];

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Compta.php';
$compta = new AFUP_Compta($bdd);

if ($action == 'lister') {
    $list_ordre = 'date';
    $list_sens = 'asc';
    $list_associatif = false;
    $list_filtre = false;
	
	$journal = $compta->obtenirBilan();
	$smarty->assign('bilan', $journal);

}

?>
