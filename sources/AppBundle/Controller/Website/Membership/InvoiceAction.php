<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership;

use AppBundle\MembershipFee\MembershipFeeService;
use AppBundle\MembershipFee\MembershipFeeInvoicePdfGenerator;
use AppBundle\MembershipFee\Model\MembershipFee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class InvoiceAction extends AbstractController
{
    public function __construct(
        private readonly MembershipFeeService $membershipFeeService,
        private readonly MembershipFeeInvoicePdfGenerator $pdfGenerator,
    ) {}

    public function __invoke(string $invoiceNumber, ?string $token): Response
    {
        $invoice = $this->membershipFeeService->getByInvoice($invoiceNumber, $token);

        if (!$invoice instanceof MembershipFee) {
            throw $this->createNotFoundException(sprintf('Could not find the invoice "%s" with token "%s"', $invoiceNumber, $token));
        }

        ob_start();
        $this->pdfGenerator->genererFacture($invoice->getId());
        $pdf = ob_get_clean();

        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
