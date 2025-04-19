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
    'supprimer',
    'importer',
    'ventiler',
    'modifier_colonne',
    'export',
    'download_attachment',
    'upload_attachment',
]);

$smarty->assign('action', $action);

$compta = new Comptabilite($bdd);


$id_periode = isset($_GET['id_periode']) && $_GET['id_periode'] ? $_GET['id_periode'] : "";

$id_periode = $compta->obtenirPeriodeEnCours($id_periode);
$smarty->assign('id_periode', $id_periode);

$listPeriode = $compta->obtenirListPeriode();
$smarty->assign('listPeriode', $listPeriode);


$periode_debut=$listPeriode[$id_periode-1]['date_debut'];
$periode_fin=$listPeriode[$id_periode-1]['date_fin'];

if ($action == 'lister' || $action == 'debit' || $action == 'credit' || $action == 'export') {
    $alsoDisplayClassifed = isset($_GET['also_display_classifed_entries']) && $_GET['also_display_classifed_entries'];

    $smarty->assign('also_display_classifed_entries', $alsoDisplayClassifed);
}

if ($action == 'lister' || $action == 'debit' || $action == 'credit') {
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
} elseif ($action == 'ajouter' || $action == 'modifier') {
    $formulaire = instancierFormulaire();

    if ($action === 'modifier') {
        $champsRecup = $compta->obtenir((int) $_GET['id']);

        $champs['idcompte']          = $champsRecup['idcompte'];
        $champs['date_saisie']          = $champsRecup['date_ecriture'];
        $champs['idoperation']          = $champsRecup['idoperation'];
        $champs['idcategorie']          = $champsRecup['idcategorie'];
        $champs['nom_frs']          = $champsRecup['nom_frs'];
        $champs['tva_intra']        = $champsRecup['tva_intra'];
        $champs['montant']          = $champsRecup['montant'];
        $champs['description']          = $champsRecup['description'];
        $champs['numero']          = $champsRecup['numero'];
        $champs['idmode_regl']          = $champsRecup['idmode_regl'];
        $champs['date_reglement']          = $champsRecup['date_regl'];
        $champs['obs_regl']          = $champsRecup['obs_regl'];
        $champs['idevenement']          = $champsRecup['idevenement'];
        $champs['comment'] = $champsRecup['comment'];
        $champs['montant_ht_soumis_tva_0'] = $champsRecup['montant_ht_soumis_tva_0'];
        $champs['montant_ht_soumis_tva_5_5'] = $champsRecup['montant_ht_soumis_tva_5_5'];
        $champs['montant_ht_soumis_tva_10'] = $champsRecup['montant_ht_soumis_tva_10'];
        $champs['montant_ht_soumis_tva_20'] = $champsRecup['montant_ht_soumis_tva_20'];
        $champs['tva_zone'] = $champsRecup['tva_zone'];



        //$formulaire->setDefaults($champsRecup);
        $formulaire->addElement('hidden', 'id', $_GET['id']);
    } else {
        $champs['idcompte'] = 1;
        $champs['date_saisie'] = date('Y-m-d');
        $champs['date_reglement'] = date('Y-m-d');
    }
    $formulaire->setDefaults($champs);

    // facture associé à un évènement
    $formulaire->addElement('header'  , ''                         , 'Sélectionner un Journal');
    $formulaire->addElement('select'  , 'idoperation', 'Type d\'opération', $compta->obtenirListOperations());
    $formulaire->addElement('select'  , 'idcompte'   , 'Compte', $compta->obtenirListComptes());
    $formulaire->addElement('select'  , 'idevenement', 'Evenement', $compta->obtenirListEvenements());

    //detail facture
    $formulaire->addElement('header'  , ''                         , 'Détail Facture');

    //$mois=10;
    $formulaire->addElement('date'    , 'date_saisie'     , 'Date saisie', ['language' => 'fr',
                                                                                'format'   => 'd F Y',
                                                                                'minYear' => date('Y')-5,
                                                                                'maxYear' => date('Y')+1]);

    $formulaire->addElement('select'  , 'idcategorie', 'Type de compte', $compta->obtenirListCategories());
    $formulaire->addElement('text', 'nom_frs', 'Nom fournisseurs' , ['size' => 30, 'maxlength' => 40]);
    $formulaire->addElement('text'    , 'tva_intra'  , 'TVA intracommunautaire (facture)', ['size' => 30, 'maxlength' => 100]);
    $formulaire->addElement('text', 'numero', 'Numero facture' , ['size' => 30, 'maxlength' => 40]);
    $formulaire->addElement('textarea', 'description', 'Description', ['cols' => 42, 'rows' => 5]);
    $formulaire->addElement('text', 'montant', 'Montant' , ['size' => 30, 'maxlength' => 40, 'id' => 'compta_journal_montant']);
    $formulaire->addElement('text', 'comment', 'Commentaire' , ['size' => 30, 'maxlength' => 255]);

    $formulaire->addElement('header'  , ''                         , 'TVA');
    $formulaire->addElement('text', 'montant_ht_soumis_tva_5_5', 'Montant HT soumis à TVA 5.5%' , ['size' => 30, 'maxlength' => 40, 'id' => 'compta_journal_ht_5_5']);
    $formulaire->addElement('static'  , 'note', '', '<a href="#" id="apply-vat-5-5">Calculer le montant HT soumis à TVA 5.5% sur la base de l\'intégralité du montant TTC</a><br /><br />');
    $formulaire->addElement('text', 'montant_ht_soumis_tva_10', 'Montant HT soumis à TVA 10%' , ['size' => 30, 'maxlength' => 40, 'id' => 'compta_journal_ht_10']);
    $formulaire->addElement('static'  , 'note', '', '<a href="#" id="apply-vat-10">Calculer le montant HT soumis à TVA 10% sur la base de l\'intégralité du montant TTC</a><br /><br />');
    $formulaire->addElement('text', 'montant_ht_soumis_tva_20', 'Montant HT soumis à TVA 20%' , ['size' => 30, 'maxlength' => 40, 'id' => 'compta_journal_ht_20']);
    $formulaire->addElement('static'  , 'note', '', '<a href="#" id="apply-vat-20">Calculer le montant HT soumis à TVA 20% sur la base de l\'intégralité du montant TTC</a><br /><br />');
    $formulaire->addElement('text', 'montant_ht_soumis_tva_0', 'Montant HT non soumis à TVA' , ['size' => 30, 'maxlength' => 40, 'id' => 'compta_journal_ht_0']);
    $formulaire->addElement('static'  , 'note', '', '<a href="#" id="apply-vat-0">Calculer le montant non soumis à TVA sur la base de l\'intégralité du montant TTC</a><br /><br />');

    $formulaire->addElement('select'  , 'tva_zone', 'Zone TVA', array_merge(['' => 'Non définie'], Comptabilite::TVA_ZONES));

    //reglement
    $formulaire->addElement('header'  , ''                         , 'Réglement');
    $formulaire->addElement('select'  , 'idmode_regl', 'Réglement', $compta->obtenirListReglements());
    $formulaire->addElement('date'    , 'date_reglement'     , 'Date', ['language' => 'fr',
                                                                            'format'   => 'd F Y',
                                                                            'minYear' => date('Y')-5,
                                                                            'maxYear' => date('Y')+1]);
    $formulaire->addElement('text', 'obs_regl', 'Info reglement' , ['size' => 30, 'maxlength' => 40]);


    // boutons
    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));

    // 2012-02-18 A. Gendre
    $passer = null;
    if ($action !== 'ajouter') {
        $res = $compta->obtenirSuivantADeterminer($_GET['id']);
        if (is_array($res)) {
            $passer = $res['id'];
            $formulaire->addElement('submit', 'soumettrepasser'   , 'Soumettre & passer');
            $formulaire->addElement('submit', 'passer'   , 'Passer');
        }
    }

    // ajoute des regles
    $formulaire->addRule('idoperation'   , 'Type d\'opération manquant'    , 'required');
    $formulaire->addRule('idcompte'      , 'Compte manquant'    , 'required');
    $formulaire->addRule('idoperation'   , 'Type d\'opération manquant'    , 'nonzero');
    $formulaire->addRule('idevenement'    , 'Evenement manquant'   , 'required');
    $formulaire->addRule('idevenement'    , 'Evenement manquant'   , 'nonzero');
    $formulaire->addRule('idcategorie'    , 'Type de compte manquant'     , 'required');
    $formulaire->addRule('idcategorie'    , 'Type de compte manquant'     , 'nonzero');
    $formulaire->addRule('montant'       , 'Montant manquant'      , 'required');


    // 2012-02-18 A. Gendre
    if (isset($_POST['passer']) && isset($passer)) {
        afficherMessage('L\'écriture n\'a pas été ' . (($action === 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=compta_journal&action=modifier&id=' . $passer);
        return;
    }

    if ($formulaire->validate()) {
        $valeur = $formulaire->exportValues();

        $date_ecriture= $valeur['date_saisie']['Y'] . "-" . $valeur['date_saisie']['F'] . "-" . $valeur['date_saisie']['d'] ;
        $date_regl=$valeur['date_reglement']['Y'] . "-" . $valeur['date_reglement']['F'] . "-" . $valeur['date_reglement']['d'] ;

        if ($action === 'ajouter') {
            $ok = $compta->ajouter(
                                    $valeur['idoperation'],
                                    $valeur['idcompte'],
                                    $valeur['idcategorie'],
                                    $date_ecriture,
                                    $valeur['nom_frs'],
                                    $valeur['tva_intra'],
                                    $valeur['montant'],
                                    $valeur['description'],
                                    $valeur['numero'],
                                    $valeur['idmode_regl'],
                                    $date_regl,
                                    $valeur['obs_regl'],
                                    $valeur['idevenement'],
                                    $valeur['comment'],
                                    0,
                                    $valeur['montant_ht_soumis_tva_0'],
                                    $valeur['montant_ht_soumis_tva_5_5'],
                                    $valeur['montant_ht_soumis_tva_10'],
                                    $valeur['montant_ht_soumis_tva_20'],
                                    $valeur['tva_zone']

                                    );
        } else {
            $ok = $compta->modifier(
                                    $valeur['id'],
                                    $valeur['idoperation'],
                                    $valeur['idcompte'],
                                    $valeur['idcategorie'],
                                    $date_ecriture,
                                    $valeur['nom_frs'],
                                    $valeur['tva_intra'],
                                    $valeur['montant'],
                                    $valeur['description'],
                                    $valeur['numero'],
                                    $valeur['idmode_regl'],
                                    $date_regl,
                                    $valeur['obs_regl'],
                                    $valeur['idevenement'],
                                    $valeur['comment'],
                                    null,
                                    0,
                                    $valeur['montant_ht_soumis_tva_0'],
                                    $valeur['montant_ht_soumis_tva_5_5'],
                                    $valeur['montant_ht_soumis_tva_10'],
                                    $valeur['montant_ht_soumis_tva_20'],
                                    $valeur['tva_zone']
                                    );
        }
        if ($ok) {
            if ($action === 'ajouter') {
                Logs::log('Ajout une écriture ' . $formulaire->exportValue('titre'));
            } else {
                Logs::log('Modification une écriture ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            // 2012-02-18 A. Gendre
            if (isset($_POST['soumettrepasser']) && isset($passer)) {
                $urlredirect = 'index.php?page=compta_journal&action=modifier&id=' . $passer;
            } else {
                $urlredirect = 'index.php?page=compta_journal&action=lister#L' . $valeur['id'];
            }
            afficherMessage('L\'écriture a été ' . (($action === 'ajouter') ? 'ajoutée' : 'modifiée'), $urlredirect);
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action === 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'écriture');
        }
    }


    $smarty->assign('formulaire', genererFormulaire($formulaire));
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
        $periode_fin
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
                Comptabilite::getTvaZoneLabel($line['tva_zone'], 'Non définie')
            ],
            $csvDelimiter,
            $csvEnclosure
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
            !isset($_FILES['file']['error']) ||
            is_array($_FILES['file']['error'])
        ) {
            throw new RuntimeException('Invalid parameters. You can\'t upload multiple files.');
        }

        // The directory
        $directory = date('Ym', strtotime($line['date_ecriture'])) . DIRECTORY_SEPARATOR;
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
                true
        )) {
            throw new RuntimeException('Invalid file format. Only jpg/png/pdf allowed.');
        }

        // Move/Rename
        $filename = sprintf('%s.%s',
            date('Y-m-d', strtotime($line['date_ecriture'])) . '_' . $line['id'] . '_' . substr(sha1_file($_FILES['file']['tmp_name']), 0, 6),
            $ext
        );
        $moved = move_uploaded_file(
            $_FILES['file']['tmp_name'],
            $uploadDirectory . $filename
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
}

/**
 * Download a line attachment
 */ elseif ($action === 'download_attachment') {
    try {
        // Bad request?
        if (!isset($_GET['id']) || !($line = $compta->obtenir((int) $_GET['id']))) {
            throw new Exception("Please verify parameters", 400);
        }

        // Test line existence
        if (!$line['id']) {
            throw new Exception("Not found", 404);
        }

        // Test file existence
        $filename = AFUP_CHEMIN_RACINE . 'uploads' . DIRECTORY_SEPARATOR . $line['attachment_filename'];
        if (!$line['attachment_filename'] || !is_file($filename)) {
            throw new RuntimeException('File not found.');
        }

        // Download it
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($filename);

        header('Content-Type: ' . $mime);
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($filename) . "\"");
        readfile($filename);
        exit;
    } catch (Exception $e) {
        header('HTTP/1.1 400 Bad Request');
        header('X-Info: ' . $e->getMessage());
    }
    exit;
} elseif ($action == 'supprimer') {
    if ($compta->supprimerEcriture($_GET['id'])) {
        Logs::log('Suppression de l\'écriture ' . $_GET['id']);
        afficherMessage('L\'écriture a été supprimée', 'index.php?page=compta_journal&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'écriture', 'index.php?page=compta_journal&action=lister', true);
    }
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
        $file =& $formulaire->getElement('fichiercsv');
        $tmpDir = __DIR__ . '/../../../tmp';
        if ($file->isUploadedFile()) {
            $file->moveUploadedFile($tmpDir, 'banque.csv');
            $importerFactory = new Factory();
            $importer = $importerFactory->create(
                $tmpDir . '/banque.csv',
                $valeurs['banque']
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
} elseif ($action == 'ventiler') {
    $idCompta = (int) $_GET['id'];
    $ligneCompta = $compta->obtenir($idCompta);
    $montantTotal = 0;

    foreach (explode(';', $_GET['montant']) as $montant) {
        $montant = (float) $montant;
        $compta->ajouter($ligneCompta['idoperation'],
            $ligneCompta['idcompte'],
            26, // A déterminer
            $ligneCompta['date_ecriture'],
            $ligneCompta['nom_frs'],
            $ligneCompta['tva_intra'],
            $montant,
            $ligneCompta['description'],
            $ligneCompta['numero'],
            $ligneCompta['idmode_regl'],
            $ligneCompta['date_regl'],
            $ligneCompta['obs_regl'],
            8, // A déterminer
            $ligneCompta['numero_operation']
        );
        $montantTotal += $montant;
    }

    $compta->modifier($ligneCompta['id'],
                      $ligneCompta['idoperation'],
                      $ligneCompta['idcompte'],
                      $ligneCompta['idcategorie'],
                      $ligneCompta['date_ecriture'],
                      $ligneCompta['nom_frs'],
                      $ligneCompta['tva_intra'],
                      $ligneCompta['montant'] - $montantTotal,
                      $ligneCompta['description'],
                      $ligneCompta['numero'],
                      $ligneCompta['idmode_regl'],
                      $ligneCompta['date_regl'],
                      $ligneCompta['obs_regl'],
                      $ligneCompta['idevenement'],
                      $ligneCompta['numero_operation']);
    afficherMessage('L\'écriture a été ventilée', 'index.php?page=compta_journal#journal-ligne-' . $compta->lastId);
}
