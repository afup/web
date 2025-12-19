<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\MembershipFee;

use AppBundle\Association\MemberType;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\AuditLog\Audit;
use AppBundle\MembershipFee\Form\MembershipFeeType;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditMembershipFeeAction extends AbstractController
{
    public function __construct(
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly UserRepository $userRepository,
        private readonly MembershipFeeRepository $membershipFeeRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(MemberType $memberType, int $memberId, int $membershipFeeId, Request $request): Response
    {
        $membershipFee = $this->membershipFeeRepository->get($membershipFeeId);
        $member = match ($memberType) {
            MemberType::MemberCompany => $this->companyMemberRepository->get($memberId),
            MemberType::MemberPhysical => $this->userRepository->get($memberId),
        };

        $form = $this->createForm(MembershipFeeType::class, $membershipFee);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->membershipFeeRepository->save($membershipFee);
                $name = $memberType->value === MemberType::MemberCompany->value ? $member->getCompanyName() : ($member->getFirstName() . ' ' . $member->getLastName());
                $this->audit->log('Modification de la cotisation ' . $membershipFeeId . ' pour ' . $name);
                $this->addFlash('notice', 'La cotisation pour ' . $name . ' a bien été modifiée');
            } catch (\Exception) {
                $this->addFlash('error', 'Une erreur est survenue lors de la modification de la cotisation (' . $membershipFeeId . ') pour ' . $name);
            }

            return $this->redirectToRoute('admin_membership_fee_list', ['memberType' => $memberType->value, 'memberId' => $member->getId()]);
        }

        return $this->render('admin/accounting/membership/edit.html.twig', [
            'memberType' => $memberType,
            'memberId' => $memberId,
            'member' => $member,
            'form' => $form->createView(),
            'submitLabel' => 'Modifier',
        ]);
    }
}
