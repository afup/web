<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\Fee;

use Afup\Site\Association\Cotisations;
use Afup\Site\Droits;
use Afup\Site\Utils\Utils;
use Afup\Site\Utils\Vat;
use AppBundle\Association\MembershipFeeReferenceGenerator;
use AppBundle\Association\MemberType;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\UserMembership\UserService;
use AppBundle\MembershipFee\Model\MembershipFee;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use AppBundle\Payment\PayboxBilling;
use AppBundle\Payment\PayboxFactory;
use AppBundle\Twig\ViewRenderer;
use Assert\Assertion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly UserRepository $userRepository,
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly UserService $userService,
        private readonly PayboxFactory $payboxFactory,
        private readonly Cotisations $cotisations,
        private readonly MembershipFeeRepository $membershipFeeRepository,
        private readonly Droits $droits,
    ) {}

    public function __invoke(): Response
    {
        $userService = $this->userService;

        $identifiant = $this->droits->obtenirIdentifiant();
        $user = $this->userRepository->get($identifiant);
        Assertion::notNull($user);
        $cotisation = $userService->getLastSubscription($user);
        $now = new \DateTime('now');
        $isSubjectedToVat = Vat::isSubjectedToVat($now);

        if (!$cotisation instanceof MembershipFee) {
            $message = '';
        } else {
            $endSubscription = $this->cotisations->getNextSubscriptionExpiration($cotisation);
            $message = sprintf(
                'Votre dernière cotisation -- %s € -- est valable jusqu\'au %s. <br />
        Si vous renouvelez votre cotisation maintenant, celle-ci sera valable jusqu\'au %s.',
                number_format((float) $cotisation->getAmount(), 2, ',', ' '),
                $cotisation->getEndDate()->format('d/m/Y'),
                $endSubscription->format('d/m/Y'),
            );
        }

        $cotisations_physique = $this->membershipFeeRepository->getListByUserTypeAndId(MemberType::MemberPhysical, $user->getId());
        $cotisations_morale = $this->membershipFeeRepository->getListByUserTypeAndId(MemberType::MemberCompany, $user->getCompanyId());

        /** @var array<int, MembershipFee> $liste_cotisations */
        $liste_cotisations = array_merge(iterator_to_array($cotisations_physique), iterator_to_array($cotisations_morale));

        foreach ($liste_cotisations as $k => $cotisation) {
            $cotisation->setDownloadInvoice($this->cotisations->isCurrentUserAllowedToReadInvoice((string) $cotisation->getId()));
        }

        if ($user->getCompanyId() > 0) {
            $id_personne = $user->getCompanyId();
            $type_personne = MemberType::MemberCompany;
            $prefixe = 'Personne morale';

            if (!$company = $this->companyMemberRepository->findById($id_personne)) {
                throw $this->createNotFoundException('La personne morale n\'existe pas');
            }
            $montant = $company->getMembershipFee();
            if ($isSubjectedToVat) {
                $montant *= 1 + Utils::MEMBERSHIP_FEE_VAT_RATE;
            }
        } else {
            $id_personne = $identifiant;
            $type_personne = MemberType::MemberPhysical;
            $prefixe = 'Personne physique';
            $montant = AFUP_COTISATION_PERSONNE_PHYSIQUE;
        }

        $formattedMontant = number_format($montant, 2, ',', ' ');
        $libelle = sprintf("%s : <strong>%s€</strong>", $prefixe, $formattedMontant);

        $reference = (new MembershipFeeReferenceGenerator())->generate(new \DateTimeImmutable('now'), $type_personne, $id_personne, $user->getLastName());

        $payboxBilling = new PayboxBilling($user->getFirstName(), $user->getLastName(), $user->getAddress(), $user->getZipCode(), $user->getCity(), $user->getCountry());

        $paybox = $this->payboxFactory->createPayboxForSubscription(
            $reference,
            (float) $montant,
            $user->getEmail(),
            $payboxBilling,
        );

        $paybox = str_replace('INPUT TYPE=SUBMIT', 'INPUT TYPE=SUBMIT class="button button--call-to-action"', $paybox);

        return $this->view->render('admin/association/membership/membershipfee.html.twig', [
            'isSubjectedToVat' => $isSubjectedToVat,
            'title' => 'Ma cotisation',
            'cotisations' => $liste_cotisations,
            'time' => new \DateTime(),
            'montant' => $montant,
            'libelle' => $libelle,
            'paybox' => $paybox,
            'message' => $message,
        ]);
    }
}
