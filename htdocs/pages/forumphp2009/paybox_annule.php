<?php
use Afup\Site\Forum\Inscriptions;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
require_once dirname(__FILE__) . '/_config.inc.php';

$inscriptions = new Inscriptions($bdd);
if ($inscriptions->modifierEtatInscription($_GET['cmd'], AFUP_FORUM_ETAT_ANNULE)) {
    $texte = "Votre paiement a été annulé. Aucun montant n'a été débité.";
} else {
    $texte = "Une erreur est survenue lors de l'annulation de votre inscription.<br />Aucun montant n'a été débité.";
}

$smarty->assign('texte', $texte);
$smarty->display('paybox_annule.html');
?>