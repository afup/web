<?php

declare(strict_types=1);

namespace AppBundle\Security;

use Afup\Site\Droits;
use AppBundle\Association\MemberType;
use AppBundle\MembershipFee\Entity\Cotisation;
use AppBundle\MembershipFee\Entity\Repository\CotisationRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MembershipFeeVoter extends Voter
{
    public const string READ_INVOICE = 'membership_fee_read_invoice';

    public function __construct(
        private readonly Droits $droits,
        private readonly CotisationRepository $membershipFeeRepository,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::READ_INVOICE && (is_int($subject) || is_string($subject));
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $cotisation = $this->membershipFeeRepository->find((int) $subject);
        if (!$cotisation instanceof Cotisation) {
            return false;
        }

        if ($cotisation->typePersonne === MemberType::MemberPhysical) {
            return $cotisation->idPersonne === $this->droits->obtenirIdentifiant();
        }

        if ($cotisation->typePersonne === MemberType::MemberCompany) {
            return $this->droits->verifierDroitManagerPersonneMorale($cotisation->idPersonne);
        }

        return false;
    }
}
