<?php

// Impossible to access the file itself
use Afup\Site\Comptabilite\Comptabilite;

/** @var \AppBundle\Controller\LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
	trigger_error("Direct access forbidden.", E_USER_ERROR);
	exit;
}

$action = verifierAction(['lister', 'editer']);
$smarty->assign('action', $action);

$compta = new Comptabilite($bdd);

if ($action == 'lister' ) {
	$listEvenement = $compta->obtenirListEvenements('liste');
	$smarty->assign('listEvenement', $listEvenement );

	if (!isset($_GET['idevnt']) || intval($_GET['idevnt']) == 0) {
        $idevnt= 8;
    }
    else
    {
    	$idevnt=$_GET['idevnt']; 
    }
    $smarty->assign('idevnt', $idevnt);

	$debit = $compta->obtenirSyntheseEvenement(1,$idevnt);
	$smarty->assign('debit', $debit);

	$credit = $compta->obtenirSyntheseEvenement(2,$idevnt);
	$smarty->assign('credit', $credit);

	$totalDepense = $compta->obtenirTotalSyntheseEvenement(1,$idevnt);
	$smarty->assign('totalDepense', $totalDepense);
	
	$totalRecette = $compta->obtenirTotalSyntheseEvenement(2,$idevnt);
	$smarty->assign('totalRecette', $totalRecette);
	
	$difMontant = $totalRecette - $totalDepense ;
	$smarty->assign('difMontant', $difMontant);

}

?>
