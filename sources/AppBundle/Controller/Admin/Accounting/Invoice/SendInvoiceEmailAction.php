<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Invoice;

use AppBundle\Accounting\InvoicingMailer;
use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use AppBundle\Association\Model\User;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SendInvoiceEmailAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingMailer $invoicingMailer,
        private readonly InvoicingRepository $invoicingRepository,
        private readonly Audit $audit,
        private readonly Security $security,
    ) {}

    public function __invoke(Request $request): Response
    {
        $invoiceRef = $request->query->get('ref');
        $invoice = $this->invoicingRepository->getOneByInvoiceNumber($invoiceRef);
        if ($invoice === null) {
            throw new NotFoundHttpException("Cette facture n'existe pas");
        }

        if ($this->invoicingMailer->sendInvoice($invoice)) {
            $invoice->setDateEnvoi(new \DateTime());
            $user = $this->security->getUser();
            if ($user instanceof User) {
                $invoice->setEnvoyePar($user->getUsername());
            }
            $this->invoicingRepository->save($invoice);
            $this->audit->log('Envoi par email de la facture n°' . $invoiceRef);
            $this->addFlash('notice', 'La facture a été envoyée');
        } else {
            $this->addFlash('error', 'La facture n\'a pas pu être envoyée');
        }

        return $this->redirectToRoute('admin_accounting_invoices_list');
    }
}
