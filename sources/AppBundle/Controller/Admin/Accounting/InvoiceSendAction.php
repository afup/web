<?php

namespace AppBundle\Controller\Admin\Accounting;

use AppBundle\Accounting\InvoiceService;
use AppBundle\Controller\Admin\BackOfficeLegacyBridge;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InvoiceSendAction
{
    /** @var BackOfficeLegacyBridge */
    private $backOfficeLegacyBridge;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var InvoiceService */
    private $invoiceService;

    public function __construct(
        BackOfficeLegacyBridge $backOfficeLegacyBridge,
        InvoiceService $invoiceService,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->backOfficeLegacyBridge = $backOfficeLegacyBridge;
        $this->urlGenerator = $urlGenerator;
        $this->invoiceService = $invoiceService;
    }

    public function __invoke(Request $request)
    {
        $response = $this->backOfficeLegacyBridge->handlePage('compta_facture');
        if (null !== $response) {
            return $response;
        }
        $reference = $request->query->get('ref');
        if (!$this->invoiceService->send($reference)) {
            return $this->backOfficeLegacyBridge->afficherMessage('La facture n\'a pas pu être envoyée', $this->urlGenerator->generate('admin_accounting_invoices'), true);
        }
        $this->backOfficeLegacyBridge->log('Envoi par email de la facture n°' . $reference);

        return $this->backOfficeLegacyBridge->afficherMessage('La facture a été envoyée', $this->urlGenerator->generate('admin_accounting_invoices'));
    }
}
