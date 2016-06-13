<?php
use Afup\Site\Forum\Inscriptions;
use Afup\Site\Forum\Facturation;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';




$forum_inscriptions = new Inscriptions($bdd);
$forum_facturation = new Facturation($bdd);

$forum_inscriptions->modifierEtatInscription($_GET['cmd'], AFUP_FORUM_ETAT_REGLE);
$forum_facturation->enregistrerInformationsTransaction($_GET['cmd'], $_GET['autorisation'], $_GET['transaction']);

$smarty->display('paybox_effectue.html');
?>