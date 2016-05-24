<?php
use Afup\Site\Association\Cotisations;
use Afup\Site\Utils\Logs;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

Logs::initialiser($bdd, 0);

$cotisations = new Cotisations($bdd);
$cotisations->validerReglementEnLigne($_GET['cmd'], round($_GET['total'] / 100, 2), $_GET['autorisation'], $_GET['transaction']);
$cotisations->notifierRegelementEnLigneAuTresorier($_GET['cmd'], round($_GET['total'] / 100, 2), $_GET['autorisation'], $_GET['transaction']);

Logs::log("Ajout de la cotisation " . $_GET['cmd'] . " via Paybox.");

$message  = "<p>Votre paiement a été enregistré. Merci et à bientôt.</p>";
$message .= "<p>Une questions ? N'hésitez pas à contacter <a href=\"mailto:tresorier@afup.org\">le trésorier</a>.</p>";
$message .= "<p><strong></srong><a href=\"index.php\">retour à votre compte</a></strong></p>";

$smarty->assign('paybox', $message);
$smarty->display('paybox.html');
