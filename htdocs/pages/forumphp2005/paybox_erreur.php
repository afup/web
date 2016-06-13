<?php
use Afup\Site\Forum\Inscriptions;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';


$inscriptions = new Inscriptions($bdd);
$inscriptions->modifierEtatInscription($_GET['cmd'], AFUP_FORUM_ETAT_ERREUR);

$smarty->display('paybox_erreur.html');
?>