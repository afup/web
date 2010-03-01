<?php
require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

require_once 'Afup/AFUP_Inscriptions_Forum.php';
$inscriptions = new AFUP_Inscriptions_Forum($bdd);
$inscriptions->modifierEtatInscription($_GET['cmd'], AFUP_FORUM_ETAT_REGLE);
$inscriptions->enregistrerInformationsTransaction($_GET['cmd'], $_GET['autorisation'], $_GET['transaction']);

$smarty->display('paybox_effectue.html');
?>