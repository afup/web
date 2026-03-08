<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\MembershipFee;

use AppBundle\Association\MemberType;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ListMembershipFeeAction extends AbstractController
{
    public function __construct(
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly UserRepository $userRepository,
        private readonly MembershipFeeRepository $membershipFeeRepository,
    ) {}

    public function __invoke(#[CurrentUser] UserInterface $user, MemberType $memberType, int $memberId): Response
    {
         $member = match ($memberType) {
            MemberType::MemberCompany => $this->companyMemberRepository->get($memberId),
            MemberType::MemberPhysical => $this->userRepository->get($memberId),
        };

        $memberships = $this->membershipFeeRepository->getBy(['userType' => $memberType->value, 'userId' => $memberId]);

        return $this->render('admin/accounting/membership/list.html.twig', [
            'memberType' => $memberType,
            'memberId' => $memberId,
            'member' => $member,
            'memberships' => $memberships,
        ]);
    }
}
