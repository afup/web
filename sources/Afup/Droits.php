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
    /**
     * Liste structurée avec toutes les pages référencées dans l'application
     */
    private array $_pages = [];

    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    /**
     * Renvoit l'identifiant de l'utilisateur
     *
     * @return int|null
     */
    public function obtenirIdentifiant()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User || $user instanceof GithubUser) {
            return $user->getId();
        }

        return null;
    }

    public function chargerToutesLesPages($pages): void
    {
        if (is_array($pages)) {
            $this->_pages = $pages;
        }
    }

    /**
     * @param int|string $page
     */
    public function verifierDroitSurLaPage($page): bool
    {
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }
        foreach ($this->_pages as $_page => $_page_details) {
            if ($page == $_page && (isset($_page_details['niveau']) && $this->authorizationChecker->isGranted($_page_details['niveau']))) {
                return true;
            }
            if (isset($_page_details['elements']) && is_array($_page_details['elements'])) {
                foreach ($_page_details['elements'] as $_element => $_element_details) {
                    if ($page == $_element && (isset($_element_details['niveau']) && $this->authorizationChecker->isGranted($_element_details['niveau']))) {
                        return true;
                    }
                }
            }
        }

        return false;
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
