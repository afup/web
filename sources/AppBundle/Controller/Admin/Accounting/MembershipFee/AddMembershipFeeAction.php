<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\MembershipFee;

use AppBundle\Association\MemberType;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\AuditLog\Audit;
use AppBundle\MembershipFee\Form\MembershipFeeType;
use AppBundle\MembershipFee\Model\MembershipFee;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddMembershipFeeAction extends AbstractController
{
    public function __construct(
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly UserRepository $userRepository,
        private readonly MembershipFeeRepository $membershipFeeRepository,
        private readonly ClockInterface $clock,
        private readonly Audit $audit,
    ) {}

    public function __invoke(MemberType $memberType, int $memberId, Request $request): Response
    {
        $membershipFee = new MembershipFee();
        $member = match ($memberType) {
            MemberType::MemberCompany => $this->companyMemberRepository->get($memberId),
            MemberType::MemberPhysical => $this->userRepository->get($memberId),
        };

        $startDate = $this->membershipFeeRepository->getMembershipStartingDate($memberType, $member->getId());
        $endDate = clone $startDate;
        $endDate->modify('+1 year');
        $membershipFee->setStartDate($startDate)
                      ->setEndDate($endDate)
                      ->setUserType($memberType)
                      ->setUserId($member->getId())
                      ->setToken(base64_encode(random_bytes(30)))
                      ->setInvoiceDate($this->clock->now())
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

            $name = $memberType->value === MemberType::MemberCompany->value ? $member->getCompanyName() : $member->getFirstName() . ' ' . $member->getLastName();

            try {
                $membershipFee->setInvoiceNumber($this->membershipFeeRepository->generateInvoiceNumber());
                $this->membershipFeeRepository->save($membershipFee);
                $this->audit->log("Ajout de la cotisation jusqu'au " . $fmt->format($membershipFee->getEndDate()) . ' pour ' . $name);
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
