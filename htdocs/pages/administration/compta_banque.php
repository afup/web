<?php

// Impossible to access the file itself
use Afup\Site\Comptabilite\Comptabilite;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/** @var \AppBundle\Controller\LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'exporter', 'download_attachments'));

$smarty->assign('action', $action);

if (isset($_GET['compte']) && $_GET['compte']) {
    $compte = $_GET['compte'];
} else {
    $compte = 1;
}

$compta = new Comptabilite($bdd);

if (isset($_GET['id_periode']) && $_GET['id_periode']) {
    $id_periode = $_GET['id_periode'];
} else {
    $id_periode = "";
}

$id_periode = $compta->obtenirPeriodeEnCours($id_periode);
$smarty->assign('id_periode', $id_periode);

$listPeriode = $compta->obtenirListPeriode();
$smarty->assign('listPeriode', $listPeriode);

$listeComptes = [
    '1' => 'Courant',
    '5' => 'Courant CM',
    '2' => 'Espece',
    '3' => 'Livret A',
    '6' => 'Livret A CM',
    '4' => 'Paypal',
];
$smarty->assign('listeComptes', $listeComptes);
$smarty->assign('compte_id', $compte);

if ($action == 'lister') {
    $periode_debut = $listPeriode[$id_periode - 1]['date_debut'];
    $periode_fin = $listPeriode[$id_periode - 1]['date_fin'];

    $smarty->assign('compteurLigne', 1);

    $journal = $compta->obtenirJournalBanque($compte, $periode_debut, $periode_fin);
    $smarty->assign('journal', $journal);

    $sousTotal = $compta->obtenirSousTotalJournalBanque($compte, $periode_debut, $periode_fin);
    $smarty->assign('sousTotal', $sousTotal);

    $total = $compta->obtenirTotalJournalBanque($compte, $periode_debut, $periode_fin);
    $smarty->assign('total', $total);
} elseif ($action == 'exporter') {
    $periode_debut = $listPeriode[$id_periode - 1]['date_debut'];
    $periode_fin = $listPeriode[$id_periode - 1]['date_fin'];

    $journal = $compta->obtenirJournalBanque($compte, $periode_debut, $periode_fin);
    $sousTotal = $compta->obtenirSousTotalJournalBanque($compte, $periode_debut, $periode_fin);
    setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

    //require_once 'PEAR/PHPExcel.php';
    $workbook = new Spreadsheet();

    for ($i = 1; $i < 13; $i++) {
        $compteurLigne[$i] = 4;
        $sheet = $workbook->createSheet($i);
        $sheet->setTitle('Mois de ' . strftime('%B %Y', mktime(0, 0, 0, $i, 1, date('Y', strtotime($periode_debut)))));
        $sheet->setCellValue('A1',
            'Mois de ' . strftime('%B %Y', mktime(0, 0, 0, $i, 1, date('Y', strtotime($periode_debut)))));
        $sheet->setCellValue('A3', 'Date');
        $sheet->setCellValue('B3', 'Opération');
        $sheet->setCellValue('C3', 'Description');
        $sheet->setCellValue('D3', 'Événement');
        $sheet->setCellValue('E3', 'Catégorie');
        $sheet->setCellValue('F3', 'Dépense');
        $sheet->setCellValue('G3', 'Recette');
        $sheet->setCellValue('H3', 'Commentaire');
        $sheet->setCellValue('I3', 'Justificatif');
        $sheet->setCellValue('J3', 'Nom du justificatif');
    }
    foreach ($journal as $ecriture) {
        $sheet = $workbook->getSheet($ecriture['mois']);
        $sheet->setCellValue('A' . $compteurLigne[$ecriture['mois']], date('d/m/Y', strtotime($ecriture['date_regl'])));
        $sheet->setCellValue('B' . $compteurLigne[$ecriture['mois']], $ecriture['reglement']);
        $sheet->setCellValue('C' . $compteurLigne[$ecriture['mois']], $ecriture['description']);
        $sheet->setCellValue('D' . $compteurLigne[$ecriture['mois']], $ecriture['evenement']);
        $sheet->setCellValue('E' . $compteurLigne[$ecriture['mois']], $ecriture['categorie']);
        if ($ecriture['idoperation'] == 1) {
            $sheet->setCellValue('F' . $compteurLigne[$ecriture['mois']], $ecriture['montant']);
        } else {
            $sheet->setCellValue('G' . $compteurLigne[$ecriture['mois']], $ecriture['montant']);
        }
        $sheet->setCellValue('H' . $compteurLigne[$ecriture['mois']], $ecriture['comment']);
        $sheet->setCellValue('I' . $compteurLigne[$ecriture['mois']], $ecriture['attachment_required'] ? 'Oui' : 'Non');
        $sheet->setCellValue('J' . $compteurLigne[$ecriture['mois']], $ecriture['attachment_filename']);
        $compteurLigne[$ecriture['mois']]++;
    }
    for ($i = 1; $i < 13; $i++) {
        $sheet = $workbook->getSheet($i);

        $sheet->getStyle('A1')->applyFromArray(array(
            'font' => array(
                'size' => 12,
                'bold' => true,
                'name' => 'Ubuntu'
            )
        ));
        $sheet->getStyle('A3:J3')->applyFromArray(array(
            'font' => array(
                'size' => 10,
                'bold' => true,
                'name' => 'Ubuntu'
            ),
            'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER),
            'borders' => array(
                'allborders' => array(
                    'style' => Border::BORDER_THIN,
                    'color' => array('rgb' => 'FF666666')
                )
            )
        ));
        $sheet->getStyle('A4:J' . ($compteurLigne[$i] + 1))->applyFromArray(array(
            'font' => array(
                'size' => 10,
                'name' => 'Ubuntu'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => Border::BORDER_THIN,
                    'color' => array('rgb' => 'FF666666')
                )
            )
        ));
        $sheet->getStyle('J3:I200')->applyFromArray(array('alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)));

        $sheet->setCellValue('E' . $compteurLigne[$i], 'TOTAL');
        $sheet->setCellValue('F' . $compteurLigne[$i], $sousTotal[$i]['debit']);
        $sheet->setCellValue('G' . $compteurLigne[$i], $sousTotal[$i]['credit']);
        $sheet->setCellValue('E' . ($compteurLigne[$i] + 1), 'SOLDE');
        $sheet->setCellValue('F' . ($compteurLigne[$i] + 1), $sousTotal[$i]['dif']);
        $sheet->mergeCells('F' . ($compteurLigne[$i] + 1) . ':G' . ($compteurLigne[$i] + 1));

        $sheet->getStyle('A' . $compteurLigne[$i] . ':J' . ($compteurLigne[$i] + 1))->applyFromArray(array(
            'font' => array(
                'size' => 10,
                'bold' => true,
                'name' => 'Ubuntu'
            )
        ));
        $sheet->getStyle('F' . ($compteurLigne[$i] + 1))->getAlignment()->applyFromArray(array('horizontal' => Alignment::HORIZONTAL_CENTER));

        $sheet->getStyle('F4:G200')->applyFromArray(array('numberformat' => array('code' => NumberFormat::FORMAT_NUMBER_00)));

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('C')->setWidth(36);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 3);
        $sheet->getHeaderFooter()->setOddFooter('&CPage &P de &N');

        $objDrawing = new Drawing();
        $objDrawing->setName('Logo_AFUP');
        $objDrawing->setDescription('Logo_AFUP');
        $objDrawing->setPath(__DIR__ . '/../../templates/administration/images/logo_afup.png');
        $objDrawing->setCoordinates('H1');
        $objDrawing->setHeight(35);
        $objDrawing->setWidth(70);
        $objDrawing->setWorksheet($sheet);

    }
    //$workbook->removeSheetByIndex(0);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="compta_afup_' . date('Y', strtotime($periode_debut)) . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($workbook);
    $writer->save('php://output');
    exit();
} elseif ($action === 'download_attachments') {
    /**
     * Export all attachments in a zipball
     */

    try {
        // Get the year
        $year = date('Y', strtotime($listPeriode[$id_periode - 1]['date_debut']));

        // Create the zip
        $zipFilename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'afup_justificatifs-' . $year . '.zip';
        $zip = new ZipArchive();
        $ret = $zip->open($zipFilename, ZipArchive::CREATE);
        if ($ret !== true) {
            throw new RuntimeException("Impossible to open the Zip archive.");
        } else {
            for ($month = 1; $month <= 12; $month++) {
                $searchDir = sprintf('%d%02d', $year, $month);
                $zipDir = sprintf('%d%02d', $year, $month);
                $options = [
                    'add_path' => 'afup_justificatifs-' . $year . '/' . $zipDir . '/',
                    'remove_all_path' => true,
                ];
                $zip->addGlob(AFUP_CHEMIN_RACINE . '/uploads/' . $searchDir . '/*.*', 0, $options);
            }
            $zip->close();

            // Download it
            header('Content-Type: application/zip');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . basename($zipFilename) . "\"");
            readfile($zipFilename);
            unlink($zipFilename);
            exit;
        }

    } catch (Exception $e) {
        header('HTTP/1.1 400 Bad Request');
        header('X-Info: ' . $e->getMessage());
        exit;
    }
}
