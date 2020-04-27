<?php

namespace AppBundle\Controller\Admin\Accounting;

use AppBundle\Accounting\InvoiceService;
use AppBundle\Controller\Admin\BackOfficeLegacyBridge;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class InvoiceDownloadAction
{
    /** @var BackOfficeLegacyBridge */
    private $backOfficeLegacyBridge;
    /** @var InvoiceService */
    private $invoiceService;

    public function __construct(
        BackOfficeLegacyBridge $backOfficeLegacyBridge,
        InvoiceService $invoiceService
    ) {
        $this->backOfficeLegacyBridge = $backOfficeLegacyBridge;
        $this->invoiceService = $invoiceService;
    }

    public function __invoke(Request $request)
    {
        $response = $this->backOfficeLegacyBridge->handlePage('compta_facture');
        if (null !== $response) {
            return $response;
        }

        $reference = $request->attributes->get('ref');
        $filename = 'fact' . $reference . '.pdf';
        $response = new Response($this->invoiceService->get($reference));
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename));

        return $response;
    }
}
