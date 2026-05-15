<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Facturation;

use AppBundle\Event\Invoice\EventInvoicePdfGenerator;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadFactureAction extends AbstractController
{
    public function __construct(
        private readonly EventInvoicePdfGenerator $pdfGenerator,
        private readonly InvoiceRepository $invoiceRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $reference = $request->query->get('ref');
        $facture = $this->invoiceRepository->getByReference($reference);
        if (!$facture instanceof Invoice) {
            throw new NotFoundHttpException("Cette facture n'existe pas");
        }

        $date = $facture->getInvoiceDate() ?? new \DateTime();
        $label = $facture->getCompany() ?: ($facture->getLastname() . ' ' . $facture->getFirstname());
        $filename = 'Facture - ' . $label . ' - ' . $date->format('Y-m-d_H-i') . '.pdf';

        $response = new Response($this->pdfGenerator->generateInvoice($reference));
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
