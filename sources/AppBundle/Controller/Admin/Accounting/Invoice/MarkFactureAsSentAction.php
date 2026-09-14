<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Invoice;

use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use AppBundle\Association\Model\User;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MarkFactureAsSentAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingRepository $invoicingRepository,
        private readonly Audit $audit,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly Security $security,
    ) {}

    public function __invoke(Request $request): Response
    {
        $token = new CsrfToken('admin_accounting_invoices_mark_sent', (string) $request->request->get('_token'));
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        $invoiceRef = $request->request->get('ref');
        $invoice = $this->invoicingRepository->getOneByInvoiceNumber((string) $invoiceRef);
        if ($invoice === null) {
            throw new NotFoundHttpException("Cette facture n'existe pas");
        }

        $invoice->setDateEnvoi(new \DateTime());
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $invoice->setEnvoyePar($user->getUsername());
        }
        $this->invoicingRepository->save($invoice);
        $this->audit->log('Facture n°' . $invoiceRef . ' marquée comme envoyée manuellement');
        $this->addFlash('notice', 'La facture a été marquée comme envoyée');

        return $this->redirectToRoute('admin_accounting_invoices_list');
    }
}
