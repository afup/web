<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Forum\Facturation;
use Afup\Site\Forum\Forum;
use Afup\Site\Utils\Logs;
use AppBundle\Controller\LegacyController;

/** @var LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$invoiceRepository = $this->invoiceRepository;
$invoiceService = $this->invoiceService;

$action = verifierAction(['lister', 'telecharger_devis', 'telecharger_facture', 'envoyer_facture', 'facturer_facture', 'supprimer_facture', 'changer_date_reglement']);
$tris_valides = ['date_facture', 'email', 'societe', 'etat'];
$sens_valides = ['asc' , 'desc'];
$smarty->assign('action', $action);



$forum = new Forum($bdd);
$forum_facturation = new Facturation($bdd);

if ($action == 'lister') {
    // Valeurs par défaut des paramètres de tri
    $list_champs = 'reference, date_facture, montant, email, societe, etat, facturation, date_reglement, nom, prenom';
    $list_ordre = 'date_facture DESC';
    $list_associatif = false;
    $list_filtre = false;

    // Modification des paramètres de tri en fonction des demandes passées en GET
    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }
    if (isset($_GET['filtre'])) {
        $list_filtre = $_GET['filtre'];
    }

    if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
        $_GET['id_forum'] = $forum->obtenirDernier();
    }
    $smarty->assign('id_forum', $_GET['id_forum']);


    // Mise en place de la liste dans le scope de smarty
    $smarty->assign('forums', $forum->obtenirListe());
    $smarty->assign('facturations', $forum_facturation->obtenirListe($_GET['id_forum'], $list_champs, $list_ordre, $list_associatif, $list_filtre));
} elseif ($action == 'telecharger_devis') {
    $forum_facturation->genererDevis($_GET['ref']);
} elseif ($action == 'telecharger_facture') {
    $forum_facturation->genererFacture($_GET['ref']);
    // on évite de renvoyer du html en fin de PDF
    exit(0);
} elseif ($action == 'envoyer_facture') {
    if ($forum_facturation->envoyerFacture($_GET['ref'])) {
        Logs::log('Envoi par email de la facture n°' . $_GET['ref']);
        afficherMessage('La facture a été envoyée', 'index.php?page=forum_facturation&action=lister');
    } else {
        afficherMessage("La facture n'a pas pu être envoyée", 'index.php?page=forum_facturation&action=lister', true);
    }
} elseif ($action == 'facturer_facture') {
    if ($forum_facturation->estFacture($_GET['ref'])) {
        Logs::log('Facturation => facture n°' . $_GET['ref']);
        afficherMessage('La facture est prise en compte', 'index.php?page=forum_facturation&action=lister');
    } else {
        afficherMessage("La facture n'a pas pu être prise en compte", 'index.php?page=forum_facturation&action=lister', true);
    }
} elseif ($action == 'supprimer_facture') {
    $invoice = $invoiceRepository->getByReference($_GET['ref']);
    if (null !== $invoice) {
        try {
            $invoiceService->deleteInvoice($invoice);
            Logs::log('Supprimer => facture n°' . $_GET['ref']);
            afficherMessage('La facture est supprimée', 'index.php?page=forum_facturation&action=lister');
        } catch (Exception $e) {
        }
    }
    afficherMessage("La facture n'a pas pu être supprimée", 'index.php?page=forum_facturation&action=lister', true);
} elseif ($action == 'changer_date_reglement') {
    $reglement = strtotime(implode('-', array_reverse(explode('/', $_GET['reglement']))));
    if ($forum_facturation->changerDateReglement($_GET['ref'], $reglement)) {
        afficherMessage('La date de réglement a été changée', 'index.php?page=forum_facturation&action=lister');
    } else {
        afficherMessage('La date de réglement n\'a pas été changée', 'index.php?page=forum_facturation&action=lister', true);
    }
}
