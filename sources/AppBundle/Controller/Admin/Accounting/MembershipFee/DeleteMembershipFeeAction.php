<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\MembershipFee;

use Afup\Site\Association\Cotisations;
use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Controller\Admin\Membership\MemberTypeEnum;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class DeleteMembershipFeeAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly UserRepository $userRepository,
        private readonly MembershipFeeRepository $membershipFeeRepository,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private Cotisations $cotisations,
    ) {}

    public function __invoke(MemberTypeEnum $memberType, int $memberId, int $membershipFeeId, string $token, Request $request): Response
    {
        if ($this->isGranted('ROLE_ADMIN') === false) {
            $this->addFlash('error', 'Vous n\'avez pas le droit d\'accéder à cette page');
            return $this->redirectToRoute('admin_home');
        }

        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('admin_membership_delete', $token))) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('admin_membership_fee_list', ['memberType' => $memberType->value, 'memberId' => $memberId]);
        }

        try {
            $membershipFee = $this->membershipFeeRepository->get($membershipFeeId);
            $this->membershipFeeRepository->delete($membershipFee);

            $this->log('Suppression de la cotisation ' . $membershipFeeId);
            $this->addFlash('notice', 'La cotisation a été supprimée');
        } catch (\Exception) {
            $this->addFlash('error', 'Une erreur est survenue lors de la cotisation');
        }

        return $this->redirectToRoute('admin_membership_fee_list', ['memberType' => $memberType->value, 'memberId' => $memberId]);
    }
}
