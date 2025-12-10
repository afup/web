<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Invoice;

use Afup\Site\Comptabilite\Facture;
use Afup\Site\Utils\Utils;
use AppBundle\Payment\PayboxBilling;
use AppBundle\Payment\PayboxFactory;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PublicPaymentAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly Facture $facture,
        private readonly PayboxFactory $payboxFactory,
    ) {}

    public function __invoke(Request $request): Response
    {
        $ref = $request->query->get('ref', '');
        $id = Utils::decryptFromText(urldecode($ref));
        if (!$id) {
            throw $this->createNotFoundException('Facture inexistante');
        }
        $invoice = $this->facture->obtenir($id);
        if (!$invoice) {
            throw $this->createNotFoundException('Facture inexistante');
        }

        $paybox = null;
        if ($invoice['etat_paiement'] === '0') {
            $paybox = $this->buildPaybox($invoice);
        }

        return $this->view->render('admin/accounting/invoice/public_payment.html.twig', [
            'invoice_number' => $invoice['numero_facture'],
            'ref' => $ref,
            'paybox' => $paybox,
        ]);
    }

    private function buildPaybox(array $invoice): string
    {
        $details = $this->facture->obtenir_details($invoice['numero_facture']);

        $amount = 0;
        foreach ($details as $d) {
            $amount += $d['quantite'] * $d['pu'] * (1 + ($d['tva'] / 100));
        }

        $paybox = $this->payboxFactory->getPaybox();
        $paybox
            ->setTotal($amount * 100)
            ->setCmd($invoice['numero_facture'])
            ->setPorteur($invoice['email'])
            ->setUrlRetourEffectue($this->generateUrl('public_payment_redirect', ['type' => 'success'], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setUrlRetourAnnule($this->generateUrl('public_payment_redirect', ['type' => 'canceled'], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setUrlRetourRefuse($this->generateUrl('public_payment_redirect', ['type' => 'refused'], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setUrlRetourErreur($this->generateUrl('public_payment_redirect', ['type' => 'error'], UrlGeneratorInterface::ABSOLUTE_URL))
        ;

        $payboxBilling = new PayboxBilling($invoice['prenom'], $invoice['nom'], $invoice['adresse'], $invoice['code_postal'], $invoice['ville'], $invoice['id_pays']);

        return $paybox->generate(new \DateTime(), $payboxBilling);
    }
}
