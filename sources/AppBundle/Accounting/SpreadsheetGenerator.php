<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

use AppBundle\Accounting\Model\InvoicingPeriod;
use DatePeriod;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class SpreadsheetGenerator
{
    public function generate(array $statements, array $subTotal, InvoicingPeriod $period): Spreadsheet
    {
        $formater = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE);
        $formater->setPattern('MMMM yyyy');

        $monthsOfYear = new DatePeriod($period->getStartDate(), new \DateInterval('P1M'), $period->getEndDate());
        $compteurLigne = [];
        $workbook = new Spreadsheet();
        /** @var \DateTime $month */
        foreach ($monthsOfYear as $month) {
            $currentMonth = (int) $month->format('n');
            $compteurLigne[$currentMonth] = 4;
            $sheet = $workbook->createSheet($currentMonth);
            $sheet->setTitle('Mois de ' . $formater->format($month));
            $sheet->setCellValue('A1', 'Mois de ' . $formater->format($month));
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
            $sheet->setCellValue('K3', 'Nom du compte');
        }

        foreach ($statements as $ecriture) {
            $sheet = $workbook->getSheet($ecriture['mois']);
            $sheet->setCellValue('A' . $compteurLigne[$ecriture['mois']], date('d/m/Y', strtotime((string) $ecriture['date_regl'])));
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
            $sheet->setCellValue('K' . $compteurLigne[$ecriture['mois']], $ecriture['compta_compte_nom_compte']);
            $compteurLigne[$ecriture['mois']]++;
        }
        for ($i = 1; $i < 13; $i++) {
            $sheet = $workbook->getSheet($i);

            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'size' => 12,
                    'bold' => true,
                    'name' => 'Ubuntu',
                ],
            ]);
            $sheet->getStyle('A3:K3')->applyFromArray([
                'font' => [
                    'size' => 10,
                    'bold' => true,
                    'name' => 'Ubuntu',
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allborders' => [
                        'style' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'FF666666'],
                    ],
                ],
            ]);
            $sheet->getStyle('A4:K' . ($compteurLigne[$i] + 1))->applyFromArray([
                'font' => [
                    'size' => 10,
                    'name' => 'Ubuntu',
                ],
                'borders' => [
                    'allborders' => [
                        'style' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'FF666666'],
                    ],
                ],
            ]);
            $sheet->getStyle('J3:I200')->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

            $sheet->setCellValue('E' . $compteurLigne[$i], 'TOTAL');
            $sheet->setCellValue('F' . $compteurLigne[$i], $subTotal[$i]['debit']);
            $sheet->setCellValue('G' . $compteurLigne[$i], $subTotal[$i]['credit']);
            $sheet->setCellValue('E' . ($compteurLigne[$i] + 1), 'SOLDE');
            $sheet->setCellValue('F' . ($compteurLigne[$i] + 1), $subTotal[$i]['dif']);
            $sheet->mergeCells('F' . ($compteurLigne[$i] + 1) . ':G' . ($compteurLigne[$i] + 1));

            $sheet->getStyle('A' . $compteurLigne[$i] . ':J' . ($compteurLigne[$i] + 1))->applyFromArray([
                'font' => [
                    'size' => 10,
                    'bold' => true,
                    'name' => 'Ubuntu',
                ],
            ]);
            $sheet->getStyle('F' . ($compteurLigne[$i] + 1))->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);

            $sheet->getStyle('F4:G200')->applyFromArray(['numberformat' => ['code' => NumberFormat::FORMAT_NUMBER_00]]);

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
            $objDrawing->setPath(__DIR__ . '/../../../htdocs/templates/administration/images/logo_afup.png');
            $objDrawing->setCoordinates('H1');
            $objDrawing->setHeight(35);
            $objDrawing->setWidth(70);
            $objDrawing->setWorksheet($sheet);
        }

        return $workbook;
    }
}
