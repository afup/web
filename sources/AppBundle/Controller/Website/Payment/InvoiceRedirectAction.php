<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Payment;

use Afup\Site\Utils\Utils;
use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use AppBundle\Payment\PayboxResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class InvoiceRedirectAction extends AbstractController
{
    public function __construct(private readonly InvoicingRepository $invoicingRepository) {}

    public function __invoke(Request $request, string $type = 'success'): RedirectResponse
    {
        $invoiceRef = $request->query->get('cmd', '');
        if (!$invoiceRef) {
            throw $this->createNotFoundException('Facture inexistante, ref manquant');
        }
        // getOneBy() intentionally used here: only getId() is needed, no details required
        $invoice = $this->invoicingRepository->getOneBy(['invoiceNumber' => $invoiceRef]);
        if ($invoice === null) {
            throw $this->createNotFoundException('Facture inexistante');
        }
        $cryptRef = urlencode(Utils::cryptFromText($invoice->getId()));

        $payboxResponse = PayboxResponseFactory::createFromRequest($request);
        if ($payboxResponse->isSuccessful()) {
            $this->addFlash('success', "Le paiement de votre facture s'est bien passé, merci.");
        } elseif ($type === 'refused') {
            $this->addFlash('error', "Votre paiement a été refusé.\n Aucun montant n'a été prélevé.");
        } elseif ($type === 'canceled') {
            $this->addFlash('error', "Votre paiement a été annulé.\n Aucun montant n'a été prélevé.");
        } elseif ($type === 'error') {
            $this->addFlash('error', "Il y a eu une erreur lors de l'enregistrement de paiement.");
        }

        return $this->redirectToRoute('payment_invoice', ['ref' => $cryptRef]);
    }
}
