<?php

namespace Afup\Site;
use Afup\Site\Utils\Base_De_Donnees;
use AppBundle\Association\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

define('AFUP_DROITS_NIVEAU_MEMBRE', 0);
define('AFUP_DROITS_NIVEAU_REDACTEUR', 1);
define('AFUP_DROITS_NIVEAU_ADMINISTRATEUR', 2);

define('AFUP_DROITS_ETAT_NON_FINALISE', -1);
define('AFUP_DROITS_ETAT_INACTIF', 0);
define('AFUP_DROITS_ETAT_ACTIF', 1);

define('AFUP_ANNUAIRE_ETAT_INACTIF', 0);
define('AFUP_ANNUAIRE_ETAT_ACTIF', 1);
define('AFUP_ANNUAIRE_ETAT_AMODERER', 9);

define('AFUP_CONNEXION_DECONNECTE', 0);
define('AFUP_CONNEXION_CONNECTE', 1);
define('AFUP_CONNEXION_ERROR', 2);
define('AFUP_CONNEXION_ERROR_LOGIN', 3);
define('AFUP_CONNEXION_ERROR_COTISATION', 4);

/**
 * Classe de gestion des droits
 */
class Droits
{
    /**
     * Instance de la couche d'abstraction à la base de données
     *
     * @var Base_De_Donnees
     * @access private
     */
    private $_bdd;

    /**
     * Quel est le statut de la dernière connexion ?
     *
     * @var bool
     * @access private
     */
    private $_statut_connexion = AFUP_CONNEXION_DECONNECTE;

    /**
     * Niveau de droits de l'utilisateur
     *
     * @var int
     * @access private
     */
    private $_niveau = AFUP_DROITS_NIVEAU_MEMBRE;

    /**
     * Liste structurée avec toutes les pages référencées dans l'application
     *
     * @var array
     * @access private
     */
    private $_pages = array();

    /**
     * Listener du type Afup\Site\AuthentificationInterface
     *
     * @var int
     * @access private
     */
    private $_listeners = array();

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * Constructeur. Vérifie si l'utilisateur est connecté
     *
     * @param object $bdd Instance de la couche d'abstraction à la base de données
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @access public
     */
    public function __construct(&$bdd, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->_bdd = $bdd;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Renvoit l'identifiant de l'utilisateur
     *
     * @access public
     * @return int
     */
    public function obtenirIdentifiant()
    {
        if($this->tokenStorage->getToken()->getUser() instanceof  UserInterface) {
            return $this->tokenStorage->getToken()->getUser()->getId();
        }
        return null;
    }

    /**
     * Renvoit le niveau de droits de l'utilisateur
     *
     * @access public
     * @return bool
     */
    public function obtenirNiveau()
    {
        if ($this->tokenStorage->getToken()->getUser() instanceof  UserInterface) {
            return $this->tokenStorage->getToken()->getUser()->getLevels();
        }
        return null;
    }

    /**
     * Vérifie que l'utilisateur a au moins le niveau de droits requis
     *
     * @access public
     * @return bool
     */
    public function verifierDroit($niveau_demande)
    {
        return $this->_niveau >= $niveau_demande;
    }

    /**
     * Indique si une connexion a échoué
     *
     * @access public
     * @return bool
     */
    public function obtenirStatutConnexion()
    {
        return $this->_statut_connexion;
    }

    public function chargerToutesLesPages($pages)
    {
        if (is_array($pages)) {
            $this->_pages = $pages;
            return true;
        }

        return false;
    }

    public function dechargerToutesLesPages()
    {
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->_pages;
        }

        $pages = array();
        foreach ($this->_pages as $_page => $_page_details) {
            if (isset($_page_details['elements'])) {
                foreach ($_page_details['elements'] as $_element => $_element_details) {
                    if ($this->verifierDroitSurLaPage($_element)) {
                        $pages[$_page]['nom'] = $_page_details['nom'];
                        $pages[$_page]['elements'][$_element] = $_element_details;
                    }
                }
            } else {
                if ($this->verifierDroitSurLaPage($_page)) {
                    $pages[$_page] = $_page_details;
                }
            }
        }

        return $pages;
    }

    public function verifierDroitSurLaPage($page)
    {
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }
        foreach ($this->_pages as $_page => $_page_details) {
            if ($page == $_page) {
                if (isset($_page_details['niveau']) && $this->authorizationChecker->isGranted($_page_details['niveau'])) {
                    return true;
                }
            }
            if (isset($_page_details['elements']) and is_array($_page_details['elements'])) {
                foreach ($_page_details['elements'] as $_element => $_element_details) {
                    if ($page == $_element) {
                        if (isset($_element_details['niveau']) && $this->authorizationChecker->isGranted($_element_details['niveau'])) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function obtenirEmail()
    {
        if ($this->tokenStorage->getToken()->getUser() instanceof  UserInterface) {
            return $this->tokenStorage->getToken()->getUser()->getEmail();
        }
        return null;
    }

    public function obtenirNomComplet()
    {
        if ($this->tokenStorage->getToken()->getUser() instanceof  UserInterface) {
            return $this->tokenStorage->getToken()->getUser()->getLabel();
        }
        return null;
    }

    public function verifierDroitManagerPersonneMorale($compagnyId)
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        if ($user instanceof  UserInterface) {
            return $user->getCompanyId() == $compagnyId && $this->authorizationChecker->isGranted('ROLE_COMPANY_MANAGER');
        }

        return false;
    }
}
