<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Payment;

use Afup\Site\Utils\Utils;
use AppBundle\Accounting\InvoicingPaymentStatus;
use AppBundle\Accounting\Model\Invoicing;
use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use AppBundle\Payment\PayboxBilling;
use AppBundle\Payment\PayboxFactory;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InvoiceAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly InvoicingRepository $invoicingRepository,
        private readonly PayboxFactory $payboxFactory,
    ) {}

    public function __invoke(Request $request): Response
    {
        $ref = $request->query->get('ref', '');
        $id = Utils::decryptFromText(urldecode($ref));
        if (!$id) {
            throw $this->createNotFoundException('Facture inexistante');
        }
        $invoice = $this->invoicingRepository->getById((int) $id);
        if (!$invoice) {
            throw $this->createNotFoundException('Facture inexistante');
        }

        $paybox = null;
        if ($invoice->getPaymentStatus() === InvoicingPaymentStatus::Waiting) {
            $paybox = $this->buildPaybox($invoice);
        }

        return $this->view->render('site/payment/invoice.html.twig', [
            'invoice_number' => $invoice->getInvoiceNumber(),
            'ref' => $ref,
            'paybox' => $paybox,
        ]);
    }

    private function buildPaybox(Invoicing $invoice): string
    {
        $amount = 0.0;
        foreach ($invoice->getDetails() as $detail) {
            $amount += $detail->getQuantity() * $detail->getUnitPrice() * (1 + ($detail->getTva() / 100));
        }

        $paybox = $this->payboxFactory->getPaybox();
        $paybox
            ->setTotal((int) ($amount * 100))
            ->setCmd($invoice->getInvoiceNumber())
            ->setPorteur($invoice->getEmail())
            ->setUrlRetourEffectue($this->generateUrl('payment_invoice_redirect', ['type' => 'success'], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setUrlRetourAnnule($this->generateUrl('payment_invoice_redirect', ['type' => 'canceled'], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setUrlRetourRefuse($this->generateUrl('payment_invoice_redirect', ['type' => 'refused'], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setUrlRetourErreur($this->generateUrl('payment_invoice_redirect', ['type' => 'error'], UrlGeneratorInterface::ABSOLUTE_URL))
        ;

        $payboxBilling = new PayboxBilling($invoice->getFirstname(), $invoice->getLastname(), $invoice->getAddress(), $invoice->getZipcode(), $invoice->getCity(), $invoice->getCountryId());

        return $paybox->generate(new \DateTime(), $payboxBilling);
    }
}
