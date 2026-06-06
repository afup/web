<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Quotation;

use AppBundle\Accounting\InvoicingPdfGenerator;
use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadQuotationAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingPdfGenerator $pdfGenerator,
        private readonly InvoicingRepository $invoicingRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $quotationRef = $request->query->get('ref');
        $quotation = $this->invoicingRepository->getOneByQuotationNumber($quotationRef);
        if ($quotation === null) {
            throw new NotFoundHttpException("Ce devis n'existe pas");
        }

        $pdf = $this->pdfGenerator->generateQuotation($quotation);
        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition('attachment', $this->pdfGenerator->getQuotationFilename($quotation)));

        return $response;
    }
}
