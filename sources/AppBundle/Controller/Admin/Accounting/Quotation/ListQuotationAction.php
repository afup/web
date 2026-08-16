<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Quotation;

use Afup\Site\Utils\Vat;
use AppBundle\Accounting\Entity\Repository\InvoicingPeriodRepository;
use AppBundle\Accounting\Entity\Repository\InvoicingRepository;
use AppBundle\Accounting\Form\InvoicingPeriodType;
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
        $quotations = $this->invoiceRepository->getQuotationsByPeriodId($period->id, $sort, $direction);
        $periods = $this->invoicingPeriodRepository->findAll();

        return new Response($this->twig->render('admin/accounting/quotation/list.html.twig', [
            'lines' => $quotations,
            'periods' => $periods,
            'periodId' => $period->id,
            'formPeriod' => $formPeriod->createView(),
            'direction' => $direction,
            'sort' => $sort,
            'isSubjectedToVat' => Vat::isSubjectedToVat($period->dateFin),
        ]));
    }
}
