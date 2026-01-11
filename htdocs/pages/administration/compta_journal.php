<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Comptabilite\Comptabilite;
use Afup\Site\Utils\Logs;
use AppBundle\Compta\Importer\CreditMutuel;
use AppBundle\Compta\Importer\CreditMutuelLivret;
use AppBundle\Compta\Importer\Factory;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction([
    'lister',
    'debit',
    'credit',
    'ajouter',
    'modifier',
    'importer',
    'modifier_colonne',
    'export',
    'upload_attachment',
]);

$smarty->assign('action', $action);

$compta = new Comptabilite($bdd);


$id_periode = isset($_GET['id_periode']) && $_GET['id_periode'] ? $_GET['id_periode'] : "";

$id_periode = $compta->obtenirPeriodeEnCours($id_periode);
$smarty->assign('id_periode', $id_periode);

$listPeriode = $compta->obtenirListPeriode();
$smarty->assign('listPeriode', $listPeriode);


$periode_debut = $listPeriode[$id_periode - 1]['date_debut'];
$periode_fin = $listPeriode[$id_periode - 1]['date_fin'];

if (in_array($action, ['lister', 'debit', 'credit', 'export'])) {
    $alsoDisplayClassifed = isset($_GET['also_display_classifed_entries']) && $_GET['also_display_classifed_entries'];

    $smarty->assign('also_display_classifed_entries', $alsoDisplayClassifed);
}

if (in_array($action, ['lister', 'debit', 'credit'])) {
    $smarty->assign('categories', $compta->obtenirListCategoriesJournal());
    $smarty->assign('events', $compta->obtenirListEvenementsJournal());
    $smarty->assign('payment_methods', $compta->obtenirListReglementsJournal());
}

if ($action == 'lister') {
    // Accounting lines for the selected period
    $journal = $compta->obtenirJournal('', $periode_debut, $periode_fin, !$alsoDisplayClassifed);
    $smarty->assign('journal', $journal);
} elseif ($action == 'debit') {
    $journal = $compta->obtenirJournal('1',$periode_debut,$periode_fin, !$alsoDisplayClassifed);
    $smarty->assign('journal', $journal);
} elseif ($action == 'credit') {
    $journal = $compta->obtenirJournal('2',$periode_debut,$periode_fin, !$alsoDisplayClassifed);
    $smarty->assign('journal', $journal);
} elseif ($action === 'export') {
    /*
     * This action allows the admin to export the full period in a CSV file.
     * This is really useful when you need to filter by columns using Excel.
     */
    $journal = $compta->obtenirJournal('', $periode_debut, $periode_fin, !$alsoDisplayClassifed);

    // Pointer to output
    $fp = fopen('php://output', 'w');

    // CSV
    $csvDelimiter = ';';
    $csvEnclosure = '"';
    $csvFilename  = sprintf(
        'AFUP_%s_journal_from-%s_to-%s.csv',
        date('Y-M-d'),
        $periode_debut,
        $periode_fin,
    );

    // headers
    header('Content-Type: text/csv');
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"$csvFilename\"");

    // First line
    $columns = [
        'Date',
        'Compte',
        'Événement',
        'Catégorie',
        'Description',
        'Débit',
        'Crédit',
        'Règlement',
        'Commentaire',
        'Justificatif',
        'Nom justificatif',
        'Montant HT',
        'TVA',
        'Montant HT non soumis à TVA',
        'Montant HT soumis à TVA 5,5',
        'TVA 5,5',
        'Montant HT soumis à TVA 10',
        'TVA 10',
        'Montant HT soumis à TVA 20',
        'TVA 20',
        "Zone de TVA",
    ];
    fputcsv($fp, $columns, $csvDelimiter, $csvEnclosure);

    // Set the current local and get variables to use in number_format
    $l = setlocale(LC_ALL, 'fr_FR.utf8');
    $locale = localeconv();

    foreach ($journal as $line) {
        $total = number_format((float) $line['montant'], 2, $locale['decimal_point'], $locale['thousands_sep']);
        fputcsv(
            $fp,
            [
                $line['date_ecriture'],
                $line['nom_compte'],
                $line['evenement'],
                $line['categorie'],
                $line['description'],
                $line['idoperation'] == 1 ? "-$total" : '',
                $line['idoperation'] != 1 ? $total : '',
                $line['reglement'],
                $line['comment'],
                $line['attachment_required'] ? 'Oui' : 'Non',
                $line['attachment_filename'],
                $line['montant_ht'],
                $line['montant_tva'],
                $line['montant_ht_0'],
                $line['montant_ht_5_5'],
                $line['montant_tva_5_5'],
                $line['montant_ht_10'],
                $line['montant_tva_10'],
                $line['montant_ht_20'],
                $line['montant_tva_20'],
                Comptabilite::getTvaZoneLabel($line['tva_zone'], 'Non définie'),
            ],
            $csvDelimiter,
            $csvEnclosure,
        );
    }

    fclose($fp);

    exit;
}

/*
 * This action is used in AJAX in order to update "compta" data.
 * Only 4 columns are available for update:
 *  - categorie
 *  - reglement
 *  - evenement
 *  - comment
 *  - attachment_required
 * The new value is passed with the `val` variable (POST).
 * The column and the "compta" identifier are passed with GET vars.
 *
 * There is no content return on failure, only headers.
 * If the update succeed we display a simple JSON element with a 200 status code.
 *
 * This action is added to perform Ajax updates directly on the "journal" list
 * in order to improve utilization.
 */ elseif ($action === 'modifier_colonne') {
    try {
        // Bad request?
        if (!isset($_POST['val']) || !isset($_GET['column']) || !isset($_GET['id']) || !($line = $compta->obtenir((int) $_GET['id']))) {
            throw new Exception("Please verify parameters", 400);
        }

        // Test line existence
        if (!$line['id']) {
            throw new Exception("Not found", 404);
        }

        $allowEmpty = false;

        switch ($_GET['column']) {
            case 'categorie':
                $column = 'idcategorie';
                $value  = (int) $_POST['val'];
                break;
            case 'reglement':
                $column = 'idmode_regl';
                $value  = (int) $_POST['val'];
                break;
            case 'evenement':
                $column = 'idevenement';
                $value  = (int) $_POST['val'];
                break;
            case 'comment':
                $column = 'comment';
                $value  = (string) $_POST['val'];
                $allowEmpty = true;
                break;
            case 'attachment_required':
                $column = 'attachment_required';
                $value  = (int) $_POST['val'];
                $allowEmpty = true;
                break;
            default:
                throw new Exception("Bad column name", 400);
        }

        // No value?
        if (!$allowEmpty && !$value) {
            throw new Exception("Bad value", 400);
        }

        if ($compta->modifierColonne($line['id'], $column, $value)) {
            $response = [
                'success' => true,
            ];

            // Done!
            header('Content-Type: application/json; charset=utf-8');
            header('HTTP/1.1 200 OK');
            die(json_encode($response));
        } else {
            throw new Exception("An error occurred", 409);
        }
    } catch (Exception $e) {
        switch ($e->getCode()) {
            case 404:
                $httpStatus = "Not Found";
                break;
            case 409:
                $httpStatus = "Conflict";
                break;
            case 400:
            default:
                $httpStatus = "Bad Request";
                break;
        }
        header('HTTP/1.1 ' . $e->getCode() . ' ' . $httpStatus);
        header('X-Info: ' . $e->getMessage());
        exit;
    }
}

/**
 * Upload an attachment and save it on the specific line.
 * We save the uploads in a directory at the same month of the line
 * and we don't forget to rename the file with the date of the line
 * and a unique identifier to keep it safe.
 * If the line already has an attachment, we remove it before saving
 * the new one in the line.
 */ elseif ($action === 'upload_attachment') {
    try {
        // Bad request?
        if (!isset($_GET['id']) || !($line = $compta->obtenir((int) $_GET['id']))) {
            throw new Exception("Please verify parameters", 400);
        }

        // Test line existence
        if (!$line['id']) {
            throw new Exception("Not found", 404);
        }

        // Avoid multiple upload
        if (
            !isset($_FILES['file']['error'])
            || is_array($_FILES['file']['error'])
        ) {
            throw new RuntimeException('Invalid parameters. You can\'t upload multiple files.');
        }

        // The directory
        $directory = date('Ym', strtotime((string) $line['date_ecriture'])) . DIRECTORY_SEPARATOR;
        $uploadDirectory = AFUP_CHEMIN_RACINE . 'uploads' . DIRECTORY_SEPARATOR . $directory;
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0750, true);
        }

        // Get the file, rename it, and move it.
        // Check $_FILES['file']['error'] value.
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }

        // You should also check filesize here.
        if ($_FILES['upfile']['size'] > 1000000) {
            throw new RuntimeException('Exceeded filesize limit.');
        }

        // Check MIME Type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
                $finfo->file($_FILES['file']['tmp_name']),
                [
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                    'pdf' => 'application/pdf',
                ],
                true,
        )) {
            throw new RuntimeException('Invalid file format. Only jpg/png/pdf allowed.');
        }

        // Move/Rename
        $filename = sprintf('%s.%s',
            date('Y-m-d', strtotime((string) $line['date_ecriture'])) . '_' . $line['id'] . '_' . substr(sha1_file($_FILES['file']['tmp_name']), 0, 6),
            $ext,
        );
        $moved = move_uploaded_file(
            $_FILES['file']['tmp_name'],
            $uploadDirectory . $filename,
        );
        if (!$moved) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        // Remove old file if exists
        if ($line['attachment_filename']) {
            $oldFilename = AFUP_CHEMIN_RACINE . 'uploads' . DIRECTORY_SEPARATOR . $line['attachment_filename'];
            if (is_file($oldFilename)) {
                unlink($oldFilename);
            }
        }

        // Update line
        $compta->modifierColonne($line['id'], 'attachment_filename', $directory . $filename);

        header('HTTP/1.1 200 OK');
        header('X-Info: File uploaded \o/');
    } catch (Exception $e) {
        header('HTTP/1.1 400 Bad Request');
        echo $e->getMessage();
    }
    exit;
} elseif ($action == 'importer') {
    $formulaire = instancierFormulaire();
    $formulaire->addElement('header', null          , 'Import CSV');
    $formulaire->addElement('file', 'fichiercsv', 'Fichier banque');
    $formulaire->addElement('select', 'banque', 'Banque', [
        CreditMutuel::CODE => 'Crédit Mutuel - Compte Courant',
        CreditMutuelLivret::CODE => 'Crédit Mutuel - Livret',
    ]);

    $formulaire->addElement('header', 'boutons'  , '');
    $formulaire->addElement('submit', 'soumettre', 'Soumettre');

    if ($formulaire->validate()) {
        $valeurs = $formulaire->exportValues();
        $file = & $formulaire->getElement('fichiercsv');
        $tmpDir = __DIR__ . '/../../../tmp';
        if ($file->isUploadedFile()) {
            $file->moveUploadedFile($tmpDir, 'banque.csv');
            $importerFactory = new Factory();
            $importer = $importerFactory->create(
                $tmpDir . '/banque.csv',
                $valeurs['banque'],
            );
            $importer->initialize($tmpDir . '/banque.csv');
            if ($compta->extraireComptaDepuisCSVBanque($importer)) {
                Logs::log('Chargement fichier banque');
                afficherMessage('Le fichier a été importé', 'index.php?page=compta_journal&action=lister');
            } else {
                afficherMessage("Le fichier n'a pas été importé. Le format est-il valide ?", 'index.php?page=compta_journal&action=lister', true);
            }
            unlink($tmpDir . '/banque.csv');
        }
    }
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
