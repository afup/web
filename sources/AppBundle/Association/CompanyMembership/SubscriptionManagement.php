<?php

declare(strict_types=1);

namespace AppBundle\Association\CompanyMembership;

use AppBundle\MembershipFee\MembershipFeeService;
use Afup\Site\Utils\Utils;
use AppBundle\Association\MemberType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\MembershipFee\Model\MembershipFee;

final readonly class SubscriptionManagement
{
    public function __construct(private MembershipFeeService $membershipFeeService) {}

    public function createInvoiceForInscription(CompanyMember $company, int $numberOfMembers): array
    {
        $endSubscription = $this->membershipFeeService->getNextSubscriptionExpiration(null);

        // Create the invoice
        $this->membershipFeeService->ajouter(
            MemberType::MemberCompany,
            $company->getId(),
            ceil($numberOfMembers / AFUP_PERSONNE_MORALE_SEUIL) * AFUP_COTISATION_PERSONNE_MORALE * (1 + Utils::MEMBERSHIP_FEE_VAT_RATE),
            null,
            null,
            new \DateTime()->getTimestamp(),
            $endSubscription->getTimestamp(),
            '',
        );
        $subscription = $this->membershipFeeService->getLatestByUserTypeAndId(MemberType::MemberCompany, $company->getId());

        if (!$subscription instanceof MembershipFee) {
            throw new \RuntimeException('An error occured');
        }

        return ['invoice' => $subscription->getInvoiceNumber(), 'token' => $subscription->getToken()];
    }
}
