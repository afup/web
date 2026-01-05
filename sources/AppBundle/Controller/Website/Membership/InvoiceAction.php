<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership;

use Afup\Site\Association\Cotisations;
use AppBundle\MembershipFee\Model\MembershipFee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class InvoiceAction extends AbstractController
{
    public function __construct(
        private readonly Cotisations $cotisations,
    ) {}

    public function __invoke(string $invoiceNumber, ?string $token): Response
    {
        $invoice = $this->cotisations->getByInvoice($invoiceNumber, $token);

        if (!$invoice instanceof MembershipFee) {
            throw $this->createNotFoundException(sprintf('Could not find the invoice "%s" with token "%s"', $invoiceNumber, $token));
        }

        ob_start();
        $this->cotisations->genererFacture($invoice->getId());
        $pdf = ob_get_clean();

        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
