<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Quotation;

use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use AppBundle\Accounting\Model\Repository\InvoicingPeriodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListQuotationAction extends AbstractController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly InvoicingPeriodRepository $invoicingPeriodRepository,
        private readonly InvoicingRepository $invoiceRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $periodId = $request->query->has('periodId') ? $request->query->getInt('periodId') : null;
        $periodId = $this->invoicingPeriodRepository->getCurrentPeriodId($periodId);
        $direction = $request->query->get('direction', 'desc');
        $sort = $request->query->get('sort', 'date');
        $quotations = $this->invoiceRepository->getQuotationsByPeriodId($periodId, $sort, $direction);
        $periods = $this->invoicingPeriodRepository->getAll();

        return new Response($this->twig->render('admin/accounting/quotation/list.html.twig', [
            'lines' => $quotations,
            'periods' => $periods,
            'periodId' => $periodId,
            'direction' => $direction,
            'sort' => $sort,
        ]));
    }
}
