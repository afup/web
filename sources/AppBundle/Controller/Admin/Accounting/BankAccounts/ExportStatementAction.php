<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\BankAccounts;

use Afup\Site\Comptabilite\Comptabilite;
use AppBundle\Accounting\Model\Repository\InvoicingPeriodRepository;
use AppBundle\Accounting\SpreadsheetGenerator;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExportStatementAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingPeriodRepository $invoicingPeriodRepository,
        private readonly Comptabilite $comptabilite,
        private readonly SpreadsheetGenerator $spreadsheetGenerator,
    ) {}

    public function __invoke(Request $request): Response
    {
        $periodId = $request->query->has('periodId') && $request->query->get('periodId') ? (int) $request->query->get('periodId') : null;
        $accountId = $request->query->getInt('accountId', 1);
        $period = $this->invoicingPeriodRepository->getCurrentPeriod($periodId);

        $start = $period->getStartDate()->format('Y-m-d');
        $end = $period->getEndDate()->format('Y-m-d');
        $statements = $this->comptabilite->obtenirJournalBanque($accountId, $start, $end);
        $subTotal = $this->comptabilite->obtenirSousTotalJournalBanque($start, $end, $accountId);


        $spreadsheet = $this->spreadsheetGenerator->generate($statements, $subTotal, $period);
        $tempfile = tempnam(sys_get_temp_dir(), 'bank_acconts_export');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempfile);

        $response = new BinaryFileResponse($tempfile, Response::HTTP_OK, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="compta_afup_' . $period->getStartDate()->format('Y') . '.xlsx"',
            'Cache-Control' => 'max-age=0',
        ], false);
        $response->deleteFileAfterSend(true);
        return $response;
    }
}
