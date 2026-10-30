<?php

declare(strict_types=1);

namespace AppBundle\Security;

use AppBundle\Association\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Empêche un gestionnaire du module Membres de modifier, supprimer ou réinitialiser
 * le mot de passe d'un compte super admin (prise de contrôle du compte).
 *
 * @extends Voter<string, User>
 */
class ManageUserVoter extends Voter
{
    public const string MANAGE_USER = 'MANAGE_USER';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::MANAGE_USER && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        if (!$subject->hasRole('ROLE_SUPER_ADMIN')) {
            return true;
        }

        return $this->accessDecisionManager->decide($token, ['ROLE_SUPER_ADMIN']);
    }
}
