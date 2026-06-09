<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Facturation;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Invoice\InvoiceService;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IssueFactureAction extends AbstractController
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceService $invoiceService,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $reference = $request->query->get('ref');
        $facture = $this->invoiceRepository->getByReference($reference);
        if (!$facture instanceof Invoice) {
            throw new NotFoundHttpException("Cette facture n'existe pas");
        }

        try {
            $this->invoiceService->markAsInvoiced($facture);
        } catch (\Exception $e) {
            $this->addFlash('error', "La facture n'a pas pu être prise en compte");
        }

        $this->audit->log('Facturation => facture n°' . $reference);
        $this->addFlash('notice', 'La facture est prise en compte');
        return $this->redirectToRoute('admin_event_factures');
    }
}
