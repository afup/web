<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\MembershipFee;

use AppBundle\MembershipFee\MembershipFeeInvoicePdfGenerator;
use AppBundle\Association\MemberType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_COMPTA_READER')]
class DownloadMembershipFeeInvoiceAction extends AbstractController
{
    public function __construct(private readonly MembershipFeeInvoicePdfGenerator $pdfGenerator) {}

    public function __invoke(MemberType $memberType, int $memberId, int $membershipFeeId): Response
    {
        ob_start();
        $this->pdfGenerator->genererFacture($membershipFeeId);
        $pdf = ob_get_clean();

        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
