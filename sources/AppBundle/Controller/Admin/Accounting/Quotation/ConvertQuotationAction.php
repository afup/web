<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Quotation;

use AppBundle\Accounting\InvoicingNumberGenerator;
use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConvertQuotationAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingNumberGenerator $numberGenerator,
        private readonly InvoicingRepository $invoicingRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $quotationRef = $request->query->get('ref');
        $quotation = $this->invoicingRepository->getOneBy(['quotationNumber' => $quotationRef]);
        if ($quotation === null) {
            throw new NotFoundHttpException("Ce devis n'existe pas");
        }

        $this->invoicingRepository->convertQuotationToInvoice($quotation, $this->numberGenerator->generateInvoiceNumber());
        $this->addFlash('notice', 'Le devis a été transformé en facture');

        return $this->redirectToRoute('admin_accounting_invoices_list');
    }
}
