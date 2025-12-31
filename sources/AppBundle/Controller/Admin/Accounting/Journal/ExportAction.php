<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Journal;

use AppBundle\Accounting\Model\Repository\TransactionRepository;
use AppBundle\Accounting\Model\Repository\InvoicingPeriodRepository;
use AppBundle\Accounting\TvaZone;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExportAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingPeriodRepository $invoicingPeriodRepository,
        private readonly TransactionRepository $accountingRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $periodId = $request->query->has('periodId')  && !empty($request->query->get('periodId')) ? $request->query->getInt('periodId') : null;
        $withReconciled = $request->query->getBoolean('with_reconciled');
        $period = $this->invoicingPeriodRepository->getCurrentPeriod($periodId);

        $entries = $this->accountingRepository->getEntriesPerInvoicingPeriod($period, !$withReconciled);

        // CSV
        $csvFilename  = sprintf(
            'AFUP_%s_journal_from-%s_to-%s.csv',
            date('Y-M-d'),
            $period->getStartDate()->format('Y-m-d'),
            $period->getEndDate()->format('Y-m-d'),
        );
        $tmpFile = tempnam(sys_get_temp_dir(), $csvFilename);
        $file = new SplFileObject($tmpFile, 'w');
        $csvDelimiter = ';';
        $csvEnclosure = '"';

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
        $file->fputcsv($columns, $csvDelimiter, $csvEnclosure);

        foreach ($entries as $entry) {
            $total = number_format((float) $entry['montant'], 2, ',', "\u{202f}");
            $file->fputcsv(
                [
                    $entry['date_ecriture'],
                    $entry['nom_compte'],
                    $entry['evenement'],
                    $entry['categorie'],
                    $entry['description'],
                    $entry['idoperation'] == 1 ? '-' . $total : '',
                    $entry['idoperation'] != 1 ? $total : '',
                    $entry['reglement'],
                    $entry['comment'],
                    $entry['attachment_required'] ? 'Oui' : 'Non',
                    $entry['attachment_filename'],
                    number_format((float) $entry['montant_ht'], 2, '.', ''),
                    number_format((float) $entry['montant_tva'], 3, '.', ''),
                    $entry['montant_ht_0'] ? number_format((float) $entry['montant_ht_0'], 2, '.', '') : $entry['montant_ht_0'],
                    $entry['montant_ht_5_5'] ? number_format((float) $entry['montant_ht_5_5'], 2, '.', '') : $entry['montant_ht_5_5'],
                    $entry['montant_tva_5_5'] ? number_format((float) $entry['montant_tva_5_5'], 3, '.', '') : $entry['montant_tva_5_5'],
                    $entry['montant_ht_10'] ? number_format((float) $entry['montant_ht_10'], 2, '.', '') : $entry['montant_ht_10'],
                    $entry['montant_tva_10'] ? number_format((float) $entry['montant_tva_10'], 2, '.', '') : $entry['montant_tva_10'],
                    $entry['montant_ht_20'] ? number_format((float) $entry['montant_ht_20'], 2, '.', '') : $entry['montant_ht_20'],
                    $entry['montant_tva_20'] ? number_format((float) $entry['montant_tva_20'], 2, '.', '') : $entry['montant_tva_20'],
                    TvaZone::from((string) $entry['tva_zone'])->getLabel(),
                ],
                $csvDelimiter,
                $csvEnclosure,
            );
        }

        $response = new BinaryFileResponse($tmpFile);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $csvFilename);
        $response->deleteFileAfterSend(true);

        return $response;
    }
}
