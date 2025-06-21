<?php

declare(strict_types=1);

namespace AppBundle\Association\CompanyMembership;

use Afup\Site\Association\Cotisations;
use Afup\Site\Utils\Utils;
use AppBundle\Association\Model\CompanyMember;

final readonly class SubscriptionManagement
{
    public function __construct(private Cotisations $cotisations) {}

    public function createInvoiceForInscription(CompanyMember $company, $numberOfMembers): array
    {
        $endSubscription = $this->cotisations->finProchaineCotisation(false);

        // Create the invoice
        $this->cotisations->ajouter(
            AFUP_PERSONNES_MORALES,
            $company->getId(),
            ceil($numberOfMembers / AFUP_PERSONNE_MORALE_SEUIL) * AFUP_COTISATION_PERSONNE_MORALE * (1 + Utils::MEMBERSHIP_FEE_VAT_RATE),
            null,
            null,
            (new \DateTime())->format('U'),
            $endSubscription->format('U'),
            '',
        );
        $subscriptionArray = $this->cotisations->obtenirDerniere(AFUP_PERSONNES_MORALES, $company->getId());

        if ($subscriptionArray === false) {
            throw new \RuntimeException('An error occured');
        }

        return ['invoice' => $subscriptionArray['numero_facture'], 'token' => $subscriptionArray['token']];
    }
}
