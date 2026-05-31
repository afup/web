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

class DownloadDevisAction extends AbstractController
{
    public function __construct(
        private readonly EventInvoicePdfGenerator $pdfGenerator,
        private readonly InvoiceRepository $invoiceRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $reference = $request->query->get('ref');
        $devis = $this->invoiceRepository->getByReference($reference);
        if (!$devis instanceof Invoice) {
            throw new NotFoundHttpException("Ce devis n'existe pas");
        }

        $date = $devis->getInvoiceDate() ?? new \DateTime();
        $label = $devis->getCompany() ?: ($devis->getLastname() . ' ' . $devis->getFirstname());
        $filename = 'Devis - ' . $label . ' - ' . $date->format('Y-m-d_H-i') . '.pdf';

        $response = new Response($this->pdfGenerator->generateQuote($reference));
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
