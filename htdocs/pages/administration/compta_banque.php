<?php
$action = verifierAction(array('lister', 'exporter'));

$smarty->assign('action', $action);

if (isset($_GET['compte']) && $_GET['compte']) {
    $compte=$_GET['compte'];
} else {
    $compte=1;
}

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Compta.php';
$compta = new AFUP_Compta($bdd);

if (isset($_GET['id_periode']) && $_GET['id_periode']) {
    $id_periode=$_GET['id_periode'];
} else {
    $id_periode="";
}

$id_periode = $compta->obtenirPeriodeEnCours($id_periode);
$smarty->assign('id_periode', $id_periode);

$listPeriode = $compta->obtenirListPeriode();
$smarty->assign('listPeriode', $listPeriode );

if ($action == 'lister') {
    $periode_debut=$listPeriode[$id_periode-1]['date_debut'];
    $periode_fin=$listPeriode[$id_periode-1]['date_fin'];

    $smarty->assign('compteurLigne',1);

    $journal = $compta->obtenirJournalBanque($compte,$periode_debut,$periode_fin);
    $smarty->assign('journal', $journal);

    $sousTotal = $compta->obtenirSousTotalJournalBanque($compte,$periode_debut,$periode_fin);
    $smarty->assign('sousTotal', $sousTotal);

    $total = $compta->obtenirTotalJournalBanque($compte,$periode_debut,$periode_fin);
    $smarty->assign('total', $total);
} elseif ($action == 'exporter') {
    $periode_debut=$listPeriode[$id_periode-1]['date_debut'];
    $periode_fin=$listPeriode[$id_periode-1]['date_fin'];

    $journal = $compta->obtenirJournalBanque($compte,$periode_debut,$periode_fin);
    $sousTotal = $compta->obtenirSousTotalJournalBanque($compte,$periode_debut,$periode_fin);
    setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

    require_once 'PEAR/PHPExcel.php';
    $workbook = new PHPExcel;

    for ($i = 1 ; $i < 13 ; $i++) {
        $compteurLigne[$i] = 4;
        $sheet = $workbook->createSheet($i);
        $sheet->setTitle('Mois de ' . strftime('%B %Y', mktime(0, 0, 0, $i, 1, date('Y', strtotime($periode_debut)))));
        $sheet->setCellValue('A1', 'Mois de ' . strftime('%B %Y', mktime(0, 0, 0, $i, 1, date('Y', strtotime($periode_debut)))));
        $sheet->setCellValue('A3', 'Date');
        $sheet->setCellValue('B3', 'Opération');
        $sheet->setCellValue('C3', 'Description');
        $sheet->setCellValue('D3', 'Evénement');
        $sheet->setCellValue('E3', 'Catégorie');
        $sheet->setCellValue('F3', 'Dépense');
        $sheet->setCellValue('G3', 'Recette');
        $sheet->setCellValue('H3', 'Justificatif');
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
        switch (true) {
            case $ecriture['idevenement'] == 27 && $ecriture['idcategorie'] == 4: //'Association AFUP' 'Cotisation'
            case $ecriture['idevenement'] == 26 && $ecriture['idcategorie'] == 28: //'Gestion' 'Frais de compte'
            case $ecriture['idevenement'] == 25 && $ecriture['idcategorie'] == 3: //'PHPTour Lille' 'Inscription'
            case $ecriture['idevenement'] == 28 && $ecriture['idcategorie'] == 3: //'Forum 2012' 'Inscription'
            case $ecriture['idevenement'] == 29 && $ecriture['idcategorie'] == 3: //'PHPTour Nantes' 'Inscription'
                $sheet->setCellValue('H' . $compteurLigne[$ecriture['mois']], 'Non');
                break;
        }
        $compteurLigne[$ecriture['mois']]++;
    }
    for ($i = 1 ; $i < 13 ; $i++) {
        $sheet = $workbook->getSheet($i);
        $sheet->duplicateStyleArray(array('font' => array('size' => 12,
                                                          'bold' => true,
                                                          'name' => 'Ubuntu')),
                                    'A1');
        $sheet->duplicateStyleArray(array('font' => array('size' => 10,
                                                          'bold' => true,
                                                          'name' => 'Ubuntu'),
                                          'alignment'=>array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                          'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,
                                                                                   'color' => array('rgb' => 'FF666666')))),
                                    'A3:H3');
        $sheet->duplicateStyleArray(array('font' => array('size' => 10,
                                                          'name' => 'Ubuntu'),
                                          'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,
                                                                                   'color' => array('rgb' => 'FF666666')))),
                                    'A4:H' . ($compteurLigne[$i] + 1));
        $sheet->duplicateStyleArray(array('alignment'=>array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
                                    'H3:H200');
        $sheet->setCellValue('E' . $compteurLigne[$i], 'TOTAL');
        $sheet->setCellValue('F' . $compteurLigne[$i], $sousTotal[$i]['debit']);
        $sheet->setCellValue('G' . $compteurLigne[$i], $sousTotal[$i]['credit']);
        $sheet->setCellValue('E' . ($compteurLigne[$i] + 1), 'SOLDE');
        $sheet->setCellValue('F' . ($compteurLigne[$i] + 1), $sousTotal[$i]['dif']);
        $sheet->mergeCells('F' . ($compteurLigne[$i] + 1) . ':G' . ($compteurLigne[$i] + 1));
        $sheet->duplicateStyleArray(array('font' => array('size' => 10,
                                                          'bold' => true,
                                                          'name' => 'Ubuntu')),
                                    'A' . $compteurLigne[$i] . ':H' . ($compteurLigne[$i] + 1));
        $sheet->getStyle('F' . ($compteurLigne[$i] + 1))->getAlignment()->applyFromArray(array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $sheet->duplicateStyleArray(array('numberformat' => array('code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00)),
                                    'F4:G200');
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('C')->setWidth(36);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 3);
        $sheet->getHeaderFooter()->setOddFooter('&CPage &P de &N');

        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo_AFUP');
        $objDrawing->setDescription('Logo_AFUP');
        $objDrawing->setPath(dirname(__FILE__).'/../../templates/administration/images/logo_afup.png');
        $objDrawing->setCoordinates('H1');
        $objDrawing->setHeight(35);
        $objDrawing->setWidth(70);
        $objDrawing->setWorksheet($sheet);

    }
    //$workbook->removeSheetByIndex(0);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="compta_afup_' . date('Y', strtotime($periode_debut)) . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new PHPExcel_Writer_Excel2007($workbook);
    $writer->save('php://output');
    exit();
}