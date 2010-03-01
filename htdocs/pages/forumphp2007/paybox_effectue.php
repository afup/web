<?php
require_once '../../include/prepend.inc.php';

require_once 'afup/AFUP_Inscriptions_Forum.php';
require_once 'afup/AFUP_Facturation_Forum.php';

$forum_inscriptions = new AFUP_Inscriptions_Forum($bdd);
$forum_facturation = new AFUP_Facturation_Forum($bdd);

$forum_inscriptions->modifierEtatInscription($_GET['cmd'], AFUP_FORUM_ETAT_REGLE);
$forum_facturation->enregistrerInformationsTransaction($_GET['cmd'], $_GET['autorisation'], $_GET['transaction']);

$smarty->display('paybox_effectue.html');
?>