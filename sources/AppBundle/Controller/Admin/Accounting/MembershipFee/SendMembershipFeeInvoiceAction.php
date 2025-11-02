<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\MembershipFee;

use Afup\Site\Association\Cotisations;
use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Controller\Admin\Membership\MemberTypeEnum;
use AppBundle\Email\Mailer\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SendMembershipFeeInvoiceAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private readonly Mailer $mailer,
        private readonly UserRepository $userRepository,
        private Cotisations $cotisations,
    ) {}

    public function __invoke(MemberTypeEnum $memberType, int $memberId, int $membershipFeeId): Response
    {
        if ($this->isGranted('ROLE_ADMIN') === false) {
            $this->addFlash('error', 'Vous n\'avez pas le droit d\'accéder à cette page');
            return $this->redirectToRoute('admin_home');
        }

        if ($this->cotisations->envoyerFacture($membershipFeeId, $this->mailer, $this->userRepository)) {
            $this->log('Envoi par email de la facture pour la cotisation n°' . $membershipFeeId);
            $this->addFlash('notice', 'La facture a été envoyée');
        } else {
            $this->addFlash('error', 'La facture n\'a pas pu être envoyée');
        }
        return $this->redirectToRoute('admin_membership_fee_list', ['memberType' => $memberType->value, 'memberId' => $memberId]);
    }
}
