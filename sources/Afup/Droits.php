<?php

declare(strict_types=1);

namespace Afup\Site;

use AppBundle\Association\Model\User;
use AppBundle\Event\Model\GithubUser;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

define('AFUP_DROITS_NIVEAU_MEMBRE', 0);
define('AFUP_DROITS_NIVEAU_REDACTEUR', 1);
define('AFUP_DROITS_NIVEAU_ADMINISTRATEUR', 2);

define('AFUP_DROITS_ETAT_NON_FINALISE', -1);
define('AFUP_DROITS_ETAT_INACTIF', 0);
define('AFUP_DROITS_ETAT_ACTIF', 1);

/**
 * Classe de gestion des droits
 */
class Droits
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    /**
     * Renvoit l'identifiant de l'utilisateur
     */
    public function obtenirIdentifiant(): ?int
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User || $user instanceof GithubUser) {
            return $user->getId();
        }

        return null;
    }

    /**
     * @param int $compagnyId
     */
    public function verifierDroitManagerPersonneMorale($compagnyId): bool
    {
        $user = $this->tokenStorage->getToken()->getUser();
        if ($user instanceof User) {
            return $user->getCompanyId() == $compagnyId && $this->authorizationChecker->isGranted('ROLE_COMPANY_MANAGER');
        }

        return false;
    }
}
