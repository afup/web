<?php
require_once '../../include/prepend.inc.php';

require_once dirname(__FILE__) . '/../../classes/afup/AFUP_Planete_Flux.php';
require_once dirname(__FILE__) . '/../../classes/afup/AFUP_Planete_Billet.php';

$page = 0;
if (isset($_GET['page'])) {
	$page = abs((int)$_GET['page']);
}
$smarty->assign('suivant', $page + 1);
$smarty->assign('precedant', $page - 1);

$planete_billet = new AFUP_Planete_Billet($bdd);
$derniers_billets_complets = $planete_billet->obtenirDerniersBilletsTronques($page);
$smarty->assign('billets', $derniers_billets_complets);
if (count($derniers_billets_complets) == 0) {
	$smarty->assign('suivant', -1);
}

$planete_flux = new AFUP_Planete_Flux($bdd);
$tous_les_flux = $planete_flux->obtenirTousParDateDuDernierBillet();
$smarty->assign('flux', $tous_les_flux);

$smarty->display('index.html');

?>