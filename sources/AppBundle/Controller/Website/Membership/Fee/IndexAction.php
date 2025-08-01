<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\Fee;

use Afup\Site\Association\Cotisations;
use Afup\Site\Droits;
use Afup\Site\Utils\Utils;
use Afup\Site\Utils\Vat;
use AppBundle\Association\MembershipFeeReferenceGenerator;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\UserMembership\UserService;
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

        if (!$cotisation) {
            $message = '';
        } else {
            $endSubscription = $this->cotisations->finProchaineCotisation($cotisation);
            $message = sprintf(
                'Votre dernière cotisation -- %s %s -- est valable jusqu\'au %s. <br />
        Si vous renouvelez votre cotisation maintenant, celle-ci sera valable jusqu\'au %s.',
                number_format((float) $cotisation['montant'], 2, ',', ' '),
                EURO,
                date("d/m/Y", (int) $cotisation['date_fin']),
                $endSubscription->format('d/m/Y'),
            );
        }

        $cotisations_physique = $this->cotisations->obtenirListe(0, $user->getId());
        $cotisations_morale = $this->cotisations->obtenirListe(1, $user->getCompanyId());

        if (is_array($cotisations_morale) && is_array($cotisations_physique)) {
            $liste_cotisations = array_merge($cotisations_physique, $cotisations_morale);
        } elseif (is_array($cotisations_morale)) {
            $liste_cotisations = $cotisations_morale;
        } elseif (is_array($cotisations_physique)) {
            $liste_cotisations = $cotisations_physique;
        } else {
            $liste_cotisations = [];
        }

        foreach ($liste_cotisations as $k => $cotisation) {
            $liste_cotisations[$k]['telecharger_facture'] = $this->cotisations->isCurrentUserAllowedToReadInvoice($cotisation['id']);
        }

        if ($user->getCompanyId() > 0) {
            $id_personne = $user->getCompanyId();
            $type_personne = AFUP_PERSONNES_MORALES;
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
            $type_personne = AFUP_PERSONNES_PHYSIQUES;
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
            'time' => time(),
            'montant' => $montant,
            'libelle' => $libelle,
            'paybox' => $paybox,
            'message' => $message,
        ]);
    }
}
