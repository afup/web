<?php
use Afup\Site\Association\Cotisations;
use Afup\Site\Utils\Logs;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

Logs::initialiser($bdd, 0);

$cotisations = new Cotisations($bdd);

if (in_array($_SERVER['REMOTE_ADDR'], ['195.101.99.73', '195.101.99.76', '194.2.160.66', '194.2.122.158','195.25.7.146', '195.25.7.166']) === false) {
    /// Ici sont rencensees les IP indiquées par paybox dans leur doc
    die('...');
}

$status = $_GET['status'];
$etat = AFUP_COTISATIONS_PAIEMENT_ERREUR;

if ($status === '00000') {
    $etat = AFUP_COTISATIONS_PAIEMENT_REGLE;
} elseif ($status === '00015') {
    // Designe un paiement deja effectue : on a surement deja eu le retour donc on s'arrete
    die;
} elseif ($status === '00117') {
    $etat = AFUP_COTISATIONS_PAIEMENT_ANNULE;
} elseif (substr($status, 0, 3) === '001') {
    $etat = AFUP_COTISATIONS_PAIEMENT_REFUSE;
}

if ($etat == AFUP_COTISATIONS_PAIEMENT_REGLE) {
    $cotisations->validerReglementEnLigne($_GET['cmd'], round($_GET['total'] / 100, 2), $_GET['autorisation'], $_GET['transaction']);
    $cotisations->notifierRegelementEnLigneAuTresorier($_GET['cmd'], round($_GET['total'] / 100, 2), $_GET['autorisation'], $_GET['transaction']);
    Logs::log("Ajout de la cotisation " . $_GET['cmd'] . " via Paybox.");
}
