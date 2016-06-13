<?php
use Afup\Site\Forum\Inscriptions;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';


$inscriptions = new Inscriptions($bdd);
$inscriptions->modifierEtatInscription($_GET['cmd'], AFUP_FORUM_ETAT_REGLE);
$inscriptions->enregistrerInformationsTransaction($_GET['cmd'], $_GET['autorisation'], $_GET['transaction']);

$smarty->display('paybox_effectue.html');
?>