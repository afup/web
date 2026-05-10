<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Facturation;

use Afup\Site\Forum\Facturation;
use AppBundle\AuditLog\Audit;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SendFactureAction extends AbstractController
{
    public function __construct(
        private readonly Facturation $facturation,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $reference = $request->query->get('ref');
        $devis = $this->invoiceRepository->getByReference($reference);
        if (!$devis instanceof Invoice) {
            throw new NotFoundHttpException("Cette facture n'existe pas");
        }

        if ($this->facturation->envoyerFacture($reference)) {
            $this->audit->log('Facturation => facture n°' . $reference);
            $this->addFlash('notice', 'La facture a été envoyée');
            return $this->redirectToRoute('admin_event_factures');
        }

        $this->addFlash('error', "La facture n'a pas pu être envoyée");
        return $this->redirectToRoute('admin_event_factures');
    }
}
