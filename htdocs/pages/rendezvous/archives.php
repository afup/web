<?php
require_once '../../include/prepend.inc.php';

require_once dirname(__FILE__) . '/../../classes/afup/AFUP_Rendez_Vous.php';
require_once dirname(__FILE__) . '/../../classes/afup/AFUP_Logs.php';
AFUP_Logs::initialiser($bdd, 0);

$rendezvous = new AFUP_Rendez_Vous($bdd);
$lister_rendezvous = $rendezvous->obtenirListe();

if (isset($lister_rendezvous) and is_array($lister_rendezvous)) {
	foreach ($lister_rendezvous as &$rendezvous) {
		$rendezvous['date'] = date("d/m/Y", $rendezvous['debut']);
		$rendezvous['debut'] = date("H\hi", $rendezvous['debut']);
		$rendezvous['fin'] = date("H\hi", $rendezvous['fin']);
	}
	//die(print_r($lister_rendezvous));
	$smarty->assign('listerendezvous', $lister_rendezvous);
	$smarty->display('archives-rendezvous.html');

} else {
	$smarty->display('pas-de-rendezvous.html');
}

?>