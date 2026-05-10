<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Facturation;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Invoice\InvoiceService;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class DeleteFactureAction extends AbstractController
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceService $invoiceService,
        private readonly Audit $audit,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {}

    public function __invoke(Request $request, string $token): Response
    {
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('event_invoice_delete', $token))) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('admin_event_factures');
        }

        $reference = $request->query->get('ref');
        $facture = $this->invoiceRepository->getByReference($reference);
        if (!$facture instanceof Invoice) {
            throw new NotFoundHttpException("Cette facture n'existe pas");
        }

        try {
            $this->invoiceService->deleteInvoice($facture);
            $this->audit->log('Supprimer => facture n°' . $reference);
            $this->addFlash('notice', 'La facture est supprimée');
            return $this->redirectToRoute('admin_event_factures');
        } catch (Exception) {
        }

        $this->addFlash('error', "La facture n'a pas pu être supprimée");
        return $this->redirectToRoute('admin_event_factures');
    }
}
