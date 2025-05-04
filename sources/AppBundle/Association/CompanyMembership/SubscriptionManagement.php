<?php

declare(strict_types=1);


namespace AppBundle\Association\CompanyMembership;

use Afup\Site\Association\Cotisations;
use Afup\Site\Utils\Utils;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\LegacyModelFactory;

class SubscriptionManagement
{
    public function __construct(private readonly LegacyModelFactory $legacyModelFactory)
    {
    }

    public function createInvoiceForInscription(CompanyMember $company, $numberOfMembers): array
    {
        $subscription = $this->legacyModelFactory->createObject(Cotisations::class);

        $endSubscription = $subscription->finProchaineCotisation(false);

        // Create the invoice
        $subscription->ajouter(
            AFUP_PERSONNES_MORALES,
            $company->getId(),
            ceil($numberOfMembers / AFUP_PERSONNE_MORALE_SEUIL) * AFUP_COTISATION_PERSONNE_MORALE * (1 + Utils::MEMBERSHIP_FEE_VAT_RATE),
            null,
            null,
            (new \DateTime())->format('U'),
            $endSubscription->format('U'),
            ''
        );
        $subscriptionArray = $subscription->obtenirDerniere(AFUP_PERSONNES_MORALES, $company->getId());

        if ($subscriptionArray === false) {
            throw new \RuntimeException('An error occured');
        }

        return ['invoice' => $subscriptionArray['numero_facture'], 'token' => $subscriptionArray['token']];
    }
}
