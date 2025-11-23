<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Quotation;

use AppBundle\Accounting\Form\InvoicingPeriodType;
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
        $period = $this->invoicingPeriodRepository->getCurrentPeriod($periodId);
        $formPeriod = $this->createForm(InvoicingPeriodType::class, $period);

        $direction = $request->query->get('direction', 'desc');
        $sort = $request->query->get('sort', 'date');
        $quotations = $this->invoiceRepository->getQuotationsByPeriodId($period->getId(), $sort, $direction);
        $periods = $this->invoicingPeriodRepository->getAll();

        return new Response($this->twig->render('admin/accounting/quotation/list.html.twig', [
            'lines' => $quotations,
            'periods' => $periods,
            'periodId' => $period->getId(),
            'formPeriod' => $formPeriod->createView(),
            'direction' => $direction,
            'sort' => $sort,
        ]));
    }
}
