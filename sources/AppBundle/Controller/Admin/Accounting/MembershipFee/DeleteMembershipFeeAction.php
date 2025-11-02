<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\MembershipFee;

use AppBundle\AuditLog\Audit;
use AppBundle\Controller\Admin\Membership\MemberType;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class DeleteMembershipFeeAction extends AbstractController
{
    public function __construct(
        private readonly MembershipFeeRepository $membershipFeeRepository,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly Audit $audit,
    ) {}

    public function __invoke(MemberType $memberType, int $memberId, int $membershipFeeId, string $token, Request $request): Response
    {
        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('admin_membership_delete', $token))) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('admin_membership_fee_list', ['memberType' => $memberType->value, 'memberId' => $memberId]);
        }

        try {
            $membershipFee = $this->membershipFeeRepository->get($membershipFeeId);
            $this->membershipFeeRepository->delete($membershipFee);

            $this->audit->log('Suppression de la cotisation ' . $membershipFeeId);
            $this->addFlash('notice', 'La cotisation a été supprimée');
        } catch (\Exception) {
            $this->addFlash('error', 'Une erreur est survenue lors de la cotisation');
        }

        return $this->redirectToRoute('admin_membership_fee_list', ['memberType' => $memberType->value, 'memberId' => $memberId]);
    }
}
