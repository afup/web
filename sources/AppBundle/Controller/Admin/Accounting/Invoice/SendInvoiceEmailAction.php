<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Invoice;

use Afup\Site\Comptabilite\Facture;
use AppBundle\Accounting\Model\Invoicing;
use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SendInvoiceEmailAction extends AbstractController
{
    public function __construct(
        private readonly Facture $facture,
        private readonly InvoicingRepository $invoicingRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $invoiceRef = $request->query->get('ref');
        $invoice = $this->invoicingRepository->getOneBy(['invoiceNumber' => $invoiceRef]);
        if (!$invoice instanceof Invoicing) {
            throw new NotFoundHttpException("Cette facture n'existe pas");
        }

        if ($this->facture->envoyerfacture($invoiceRef)) {
            $this->audit->log('Envoi par email de la facture n°' . $invoiceRef);
            $this->addFlash('notice', 'La facture a été envoyée');
        } else {
            $this->addFlash('error', 'La facture n\'a pas pu être envoyée');
        }

        return $this->redirectToRoute('admin_accounting_invoices_list');
    }
}
