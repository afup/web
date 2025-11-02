<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\MembershipFee;

use Afup\Site\Association\Cotisations;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\AuditLog\Audit;
use AppBundle\Controller\Admin\Membership\MemberType;
use AppBundle\Email\Mailer\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SendMembershipFeeInvoiceAction extends AbstractController
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly UserRepository $userRepository,
        private readonly Cotisations $cotisations,
        private readonly Audit $audit,
    ) {}

    public function __invoke(MemberType $memberType, int $memberId, int $membershipFeeId): Response
    {
        if ($this->cotisations->envoyerFacture($membershipFeeId, $this->mailer, $this->userRepository)) {
            $this->audit->log('Envoi par email de la facture pour la cotisation n°' . $membershipFeeId);
            $this->addFlash('notice', 'La facture a été envoyée');
        } else {
            $this->addFlash('error', 'La facture n\'a pas pu être envoyée');
        }
        return $this->redirectToRoute('admin_membership_fee_list', ['memberType' => $memberType->value, 'memberId' => $memberId]);
    }
}
