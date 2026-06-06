<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Invoice;

use AppBundle\Accounting\InvoicingPdfGenerator;
use AppBundle\Accounting\Model\Invoicing;
use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadInvoiceAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingPdfGenerator $pdfGenerator,
        private readonly InvoicingRepository $invoicingRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $invoiceRef = $request->query->get('ref');
        $invoice = $this->invoicingRepository->getOneByInvoiceNumber($invoiceRef);
        if (!$invoice instanceof Invoicing) {
            throw new NotFoundHttpException("Cette facture n'existe pas");
        }

        $pdf = $this->pdfGenerator->generateInvoice($invoice);
        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition('attachment', $this->pdfGenerator->getInvoiceFilename($invoice)));

        return $response;
    }
}
