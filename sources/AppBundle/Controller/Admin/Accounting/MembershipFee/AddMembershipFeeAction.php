<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\MembershipFee;

use Afup\Site\Association\Cotisations;
use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Controller\Admin\Membership\MemberTypeEnum;
use AppBundle\MembershipFee\Form\MembershipFeeType;
use AppBundle\MembershipFee\Model\MembershipFee;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddMembershipFeeAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly UserRepository $userRepository,
        private readonly MembershipFeeRepository $membershipFeeRepository,
        private Cotisations $cotisations,
    ) {}

    public function __invoke(MemberTypeEnum $memberType, int $memberId, Request $request): Response
    {
        if ($this->isGranted('ROLE_ADMIN') === false) {
            $this->addFlash('error', 'Vous n\'avez pas le droit d\'accéder à cette page');
            return $this->redirectToRoute('admin_home');
        }

        $membershipFee = new MembershipFee();
        $member = match ($memberType) {
            MemberTypeEnum::MEMBER_COMPAGNY => $this->companyMemberRepository->get($memberId),
            MemberTypeEnum::MEMBER_PHYSICAL => $this->userRepository->get($memberId),
        };


        $startDate = $this->membershipFeeRepository->getMembershipStartingDate($memberType, $member->getId());
        $endDate = clone $startDate;
        $endDate->modify('+1 year');
        $membershipFee->setStartDate($startDate)
                      ->setEndDate($endDate)
                      ->setUserType($memberType)
                      ->setUserId($member->getId())
                      ->setToken(base64_encode(random_bytes(30)))
        ;

        $form = $this->createForm(MembershipFeeType::class, $membershipFee);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fmt = new \IntlDateFormatter(
                'fr_FR',
                \IntlDateFormatter::FULL,
                \IntlDateFormatter::FULL,
            );
            $fmt->setPattern('dd MMMM yyyy');
            try {
                $membershipFee->setInvoiceNumber($this->membershipFeeRepository->generateInvoiceNumber());
                $this->membershipFeeRepository->save($membershipFee);
                $name = $memberType->value === MemberTypeEnum::MEMBER_COMPAGNY->value ? $member->getCompanyName() : $member->getFirstName() . ' ' . $member->getLastName();
                $this->log("Ajout de la cotisation jusqu'au " . $fmt->format($membershipFee->getEndDate()) . ' pour ' . $name);
                $this->addFlash('notice', "La cotisation jusqu'au " . $fmt->format($membershipFee->getEndDate()) . ' pour ' . $name . ' a bien été ajoutée');
            } catch (\Exception) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de la cotisation jusqu\'au ' . $fmt->format($membershipFee->getEndDate()) . ' pour ' . $name);
            }
            return $this->redirectToRoute('admin_membership_fee_list', ['memberType' => $memberType->value, 'memberId' => $member->getId()]);
        }

        return $this->render('admin/accounting/membership/add.html.twig', [
            'memberType' => $memberType,
            'memberId' => $memberId,
            'member' => $member,
            'form' => $form->createView(),
            'submitLabel' => 'Ajouter',
        ]);
    }
}
