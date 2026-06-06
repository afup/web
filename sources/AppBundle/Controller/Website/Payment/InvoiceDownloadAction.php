<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Payment;

use Afup\Site\Utils\Utils;
use AppBundle\Accounting\InvoicingPdfGenerator;
use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InvoiceDownloadAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingPdfGenerator $pdfGenerator,
        private readonly InvoicingRepository $invoicingRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $invoiceId = Utils::decryptFromText(urldecode($request->query->get('ref', '')));
        if (!$invoiceId) {
            throw $this->createNotFoundException('Facture inexistante, ref manquant');
        }
        $invoice = $this->invoicingRepository->getById((int) $invoiceId);
        if (!$invoice) {
            throw $this->createNotFoundException('Facture inexistante');
        }

        $pdf = $this->pdfGenerator->generateInvoice($invoice);
        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition('attachment', $this->pdfGenerator->getInvoiceFilename($invoice)));

        return $response;
    }
}
