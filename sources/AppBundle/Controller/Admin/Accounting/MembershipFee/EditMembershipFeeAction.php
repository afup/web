<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\MembershipFee;

use Afup\Site\Association\Cotisations;
use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Controller\Admin\Membership\MemberTypeEnum;
use AppBundle\MembershipFee\Form\MembershipFeeType;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditMembershipFeeAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly UserRepository $userRepository,
        private readonly MembershipFeeRepository $membershipFeeRepository,
        private Cotisations $cotisations,
    ) {}

    public function __invoke(MemberTypeEnum $memberType, int $memberId, int $membershipFeeId, Request $request): Response
    {
        if ($this->isGranted('ROLE_ADMIN') === false) {
            $this->addFlash('error', 'Vous n\'avez pas le droit d\'accéder à cette page');
            return $this->redirectToRoute('admin_home');
        }

        $membershipFee = $this->membershipFeeRepository->get($membershipFeeId);
        $member = match ($memberType) {
            MemberTypeEnum::MEMBER_COMPAGNY => $this->companyMemberRepository->get($memberId),
            MemberTypeEnum::MEMBER_PHYSICAL => $this->userRepository->get($memberId),
        };

        $form = $this->createForm(MembershipFeeType::class, $membershipFee);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->membershipFeeRepository->save($membershipFee);
                $name = $memberType->value === MemberTypeEnum::MEMBER_COMPAGNY->value ? $member->getCompanyName() : ($member->getFirstName() . ' ' . $member->getLastName());
                $this->log('Modification de la cotisation ' . $membershipFeeId . ' pour ' . $name);
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
