<?php

namespace Afup\Site;
use Afup\Site\Utils\Base_De_Donnees;

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
     * Identifiant de l'utilisateur
     *
     * @var int
     * @access private
     */
    private $_identifiant;
    private $_hash;
    private $_email;
    private $_nom;
    private $_prenom;

    /**
     * Niveau de droits de l'utilisateur
     *
     * @var int
     * @access private
     */
    private $_niveau = AFUP_DROITS_NIVEAU_MEMBRE;
    private $_niveau_modules = "";

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
     * Constructeur. Vérifie si l'utilisateur est connecté
     *
     * @param object $bdd Instance de la couche d'abstraction à la base de données
     * @access public
     * @return void
     */
    public function __construct(&$bdd)
    {
        $this->_bdd = $bdd;

        if (isset($_SESSION['afup_login']) && isset($_SESSION['afup_mot_de_passe'])) {
            $this->seConnecter($_SESSION['afup_login'], $_SESSION['afup_mot_de_passe'], false);
            $this->surchargerNiveau();
        }
    }

    /**
     * Essaie de connecter l'utilisateur
     *
     * @param string $login Login de l'utilisateur
     * @param string $mot_de_passe Mot de passe de l'utilisateur
     * @param string $encoder Faut-il encoder le mot de passe ?
     *                                     Le mot de passe est déjà encodé s'il vient du cookie mais il ne l'est pas
     *                                     si il vient de l'écran de connexion.
     * @access public
     * @return bool Succès de la connection
     */
    public function seConnecter($login, $mot_de_passe, $encoder = true)
    {
        if ($encoder) {
            $mot_de_passe = md5($mot_de_passe);
        }

        $requete = '
            SELECT
                id, niveau, niveau_modules, nom, prenom, email, etat, 
                CONCAT(id, \'_\', email, \'_\', login) AS hash
            FROM
                afup_personnes_physiques
            WHERE
                (
                  login=' . $this->_bdd->echapper($login) . '
                  OR email=' . $this->_bdd->echapper($login) . '
                )
                AND mot_de_passe=' . $this->_bdd->echapper($mot_de_passe)
        ;
        $resultat = $this->_bdd->obtenirEnregistrement($requete);
        if ($resultat === false) {
            $this->_statut_connexion = AFUP_CONNEXION_ERROR_LOGIN;
        } elseif ($resultat['etat'] == AFUP_DROITS_ETAT_INACTIF) {
            $this->_statut_connexion = AFUP_CONNEXION_ERROR_COTISATION;
        } elseif ($resultat['etat'] == AFUP_DROITS_ETAT_ACTIF) {
            $this->_identifiant = $resultat['id'];
            $this->_hash = md5($resultat['hash']);
            $this->_niveau = $resultat['niveau'];
            $this->_niveau_modules = $resultat['niveau_modules'];
            $this->_email = $resultat['email'];
            $this->_nom = $resultat['nom'];
            $this->_prenom = $resultat['prenom'];

            $_SESSION['afup_login'] = $login;
            $_SESSION['afup_mot_de_passe'] = $mot_de_passe;
            // Envoi la demande de connection aux listeners
            $event = $resultat;
            foreach ($this->_listeners as $el) {
                $el->seConnecter($event);
            }

            $this->_statut_connexion = AFUP_CONNEXION_CONNECTE;
        } else {
            $this->_statut_connexion = AFUP_CONNEXION_ERROR;
        }

        return $this->_statut_connexion === AFUP_CONNEXION_CONNECTE;
    }

    public function obtenirHash()
    {
        return $this->_hash;
    }

    public function seConnecterEnAutomatique($hash)
    {
        $personne_physique_id = false;

        $requete = 'SELECT';
        $requete .= '  id, niveau, niveau_modules, nom, prenom, email, ';
        $requete .= '  login, ';
        $requete .= '  mot_de_passe, ';
        $requete .= '  CONCAT(id, \'_\', email, \'_\', login) as hash ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE';
        $requete .= '  etat=' . AFUP_DROITS_ETAT_ACTIF . ' ';
        /**
         * Interdit à un admin de se connecter par un hash
         * si son hash est dévoilé, qu'une personne se connecte via ce hash,
         * change le mot de passe, se déconnecte et se reconnecte => il devient admin
         */
        $requete .= 'AND';
        $requete .= '  niveau < 2';

        $personnes_physiques = $this->_bdd->obtenirTous($requete);

        foreach ($personnes_physiques as $personne_physique) {
            if (md5($personne_physique['hash']) === $hash) {
                $personne_physique_id = $personne_physique['id'];
                break;
            }
        }

        if ($personne_physique_id !== false) {
            $this->_identifiant = $personne_physique['id'];
            $this->_niveau = AFUP_DROITS_NIVEAU_MEMBRE;
            $this->_email = $personne_physique['email'];
            $this->_nom = $personne_physique['nom'];
            $this->_prenom = $personne_physique['prenom'];

            $_SESSION['afup_login'] = $personne_physique['login'];
            $_SESSION['afup_mot_de_passe'] = $personne_physique['mot_de_passe'];
            $_SESSION['afup_niveau'] = AFUP_DROITS_NIVEAU_MEMBRE;
            $_SESSION['afup_niveau_modules'] = "";
            // Envoi la demande de connection aux listeners
            $event = $personne_physique;
            foreach ($this->_listeners as $listener) {
                $listener->seConnecter($event);
            }

            $this->_statut_connexion = AFUP_CONNEXION_CONNECTE;
        } else {
            $this->_statut_connexion = AFUP_CONNEXION_ERROR;
        }

        return $this->_statut_connexion === AFUP_CONNEXION_CONNECTE;
    }

    /**
     * Essaie de déconnecter l'utilisateur
     *
     * @access public
     * @return void
     */
    public function seDeconnecter()
    {
        unset($_SESSION['afup_login']);
        unset($_SESSION['afup_mot_de_passe']);
        unset($_SESSION['afup_niveau']);
        $this->_statut_connexion = AFUP_CONNEXION_DECONNECTE;
        $this->_niveau = AFUP_DROITS_NIVEAU_MEMBRE;

        if (isset($event)) {
            // Envoi la demande de deconnection aux listeners
            foreach ($this->_listeners as $listener) {
                $listener->seDeconnecter($event);
            }
        }
    }

    /**
     * Indique si l'utilisateur est connecté
     *
     * @access public
     * @return bool
     */
    public function estConnecte()
    {
        return $this->_statut_connexion === AFUP_CONNEXION_CONNECTE;
    }

    private function surchargerNiveau()
    {
        if (isset($_SESSION['afup_niveau'])) {
            $this->_niveau = $_SESSION['afup_niveau'];
        }
        if (isset($_SESSION['afup_niveau_modules'])) {
            $this->_niveau_modules = $_SESSION['afup_niveau_modules'];
        }
    }

    /**
     * Renvoit l'identifiant de l'utilisateur
     *
     * @access public
     * @return int
     */
    public function obtenirIdentifiant()
    {
        return $this->_identifiant;
    }

    /**
     * Renvoit le niveau de droits de l'utilisateur
     *
     * @access public
     * @return bool
     */
    public function obtenirNiveau()
    {
        return $this->_niveau;
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

    /**
     * Indique si une connexion a échoué
     *
     * @access public
     * @return void
     */
    public function enregistreAuthentification(AuthentificationInterface $newListener)
    {
        foreach ($this->_listeners as $listener) {
            if ($listener === $newListener) return;
        }

        $this->_listeners[] = &$newListener;
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
        if ($this->_niveau == AFUP_DROITS_NIVEAU_ADMINISTRATEUR) {
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
        if ($this->_niveau == AFUP_DROITS_NIVEAU_ADMINISTRATEUR) {
            return true;
        }
        foreach ($this->_pages as $_page => $_page_details) {
            if ($page == $_page) {
                if (isset($_page_details['niveau']) and $_page_details['niveau'] <= $this->_niveau) {
                    return true;
                }
            }
            if (isset($_page_details['elements']) and is_array($_page_details['elements'])) {
                foreach ($_page_details['elements'] as $_element => $_element_details) {
                    if ($page == $_element) {
                        if (isset($_element_details['niveau']) and $_element_details['niveau'] <= $this->_niveau) {
                            return true;
                        }
                        if (isset($_element_details['module']) and $_element_details['niveau'] <= substr($this->_niveau_modules, $_element_details['module'], 1)) {
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
        return $this->_email;
    }

    public function obtenirNomComplet()
    {
        return ($this->_prenom . " " . $this->_nom);
    }
}
