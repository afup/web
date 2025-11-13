<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\MembershipFee;

use Afup\Site\Association\Cotisations;
use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Controller\Admin\Membership\MemberTypeEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DownloadMembershipFeeInvoiceAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private Cotisations $cotisations,
    ) {}

    public function __invoke(MemberTypeEnum $memberType, int $memberId, int $membershipFeeId): Response
    {
        if ($this->isGranted('ROLE_ADMIN') === false) {
            $this->addFlash('error', 'Vous n\'avez pas le droit d\'accéder à cette page');
            return $this->redirectToRoute('admin_home');
        }

        ob_start();
        $this->cotisations->genererFacture($membershipFeeId);
        $pdf = ob_get_clean();

        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
