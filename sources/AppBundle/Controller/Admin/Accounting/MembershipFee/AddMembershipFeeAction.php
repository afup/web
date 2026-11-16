<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\MembershipFee;

use AppBundle\Association\Entity\CompanyMember;
use AppBundle\Association\MemberType;
use AppBundle\Association\Entity\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\AuditLog\Audit;
use AppBundle\MembershipFee\Entity\Cotisation;
use AppBundle\MembershipFee\Entity\Repository\CotisationRepository;
use AppBundle\MembershipFee\Form\MembershipFeeType;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddMembershipFeeAction extends AbstractController
{
    public function __construct(
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly UserRepository $userRepository,
        private readonly CotisationRepository $membershipFeeRepository,
        private readonly ClockInterface $clock,
        private readonly Audit $audit,
    ) {}

    public function __invoke(MemberType $memberType, int $memberId, Request $request): Response
    {
        $membershipFee = new Cotisation();
        $member = match ($memberType) {
            MemberType::MemberCompany => $this->companyMemberRepository->find($memberId),
            MemberType::MemberPhysical => $this->userRepository->get($memberId),
        };

        if ($member === null) {
            throw $this->createNotFoundException('Membre non trouvé');
        }

        $startDate = $this->membershipFeeRepository->getMembershipStartingDate($memberType, $memberId);
        $endDate = clone $startDate;
        $endDate->modify('+1 year');
        $membershipFee->dateDebut = $startDate;
        $membershipFee->dateFin = $endDate;
        $membershipFee->typePersonne = $memberType;
        $membershipFee->idPersonne = $memberId;
        $membershipFee->token = base64_encode(random_bytes(30));
        $membershipFee->dateFacture = $this->clock->now();

        $form = $this->createForm(MembershipFeeType::class, $membershipFee);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fmt = new \IntlDateFormatter(
                'fr_FR',
                \IntlDateFormatter::FULL,
                \IntlDateFormatter::FULL,
            );
            $fmt->setPattern('dd MMMM yyyy');

            $name = $member instanceof CompanyMember ? $member->companyName : $member->getFirstName() . ' ' . $member->getLastName();

            try {
                $membershipFee->numeroFacture = $this->membershipFeeRepository->generateInvoiceNumber();
                $this->membershipFeeRepository->save($membershipFee);
                $this->audit->log("Ajout de la cotisation jusqu'au " . $fmt->format($membershipFee->dateFin) . ' pour ' . $name);
                $this->addFlash('notice', "La cotisation jusqu'au " . $fmt->format($membershipFee->dateFin) . ' pour ' . $name . ' a bien été ajoutée');
            } catch (\Exception) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de la cotisation jusqu\'au ' . $fmt->format($membershipFee->dateFin) . ' pour ' . $name);
            }
            return $this->redirectToRoute('admin_membership_fee_list', ['memberType' => $memberType->value, 'memberId' => $memberId]);
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
