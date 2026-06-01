<?php

declare(strict_types=1);

namespace AppBundle\Security;

use Afup\Site\Droits;
use AppBundle\Association\MemberType;
use AppBundle\MembershipFee\Model\MembershipFee;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MembershipFeeVoter extends Voter
{
    public const string READ_INVOICE = 'membership_fee_read_invoice';

    public function __construct(
        private readonly Droits $droits,
        private readonly MembershipFeeRepository $membershipFeeRepository,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::READ_INVOICE && (is_int($subject) || is_string($subject));
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $cotisation = $this->membershipFeeRepository->get((int) $subject);
        if (!$cotisation instanceof MembershipFee) {
            return false;
        }

        if ($cotisation->getUserType() === MemberType::MemberPhysical) {
            return $cotisation->getUserId() === $this->droits->obtenirIdentifiant();
        }

        if ($cotisation->getUserType() === MemberType::MemberCompany) {
            return $this->droits->verifierDroitManagerPersonneMorale($cotisation->getUserId());
        }

        return false;
    }
}
