<?php

define('AFUP_DROITS_NIVEAU_MEMBRE' , 0);
define('AFUP_DROITS_NIVEAU_REDACTEUR' , 1);
define('AFUP_DROITS_NIVEAU_ADMINISTRATEUR', 2);

define('AFUP_DROITS_ETAT_INACTIF', 0);
define('AFUP_DROITS_ETAT_ACTIF' , 1);

define('AFUP_ANNUAIRE_ETAT_INACTIF', 0);
define('AFUP_ANNUAIRE_ETAT_ACTIF' , 1);
define('AFUP_ANNUAIRE_ETAT_AMODERER', 9);

/**
* Classe de gestion des droits
*/
class AFUP_Droits {
    /**
    * Instance de la couche d'abstraction à la base de données
    *
    * @var object
    * @access private
    */
    private $_bdd;

    /**
    * Indique si l'utilisateur est connecté ou non
    *
    * @var bool
    * @access private
    */
    private $_est_connecte = false;

    /**
    * Une connexion a-t-elle échouée ?
    *
    * @var bool
    * @access private
    */
    private $_echec_connexion = false;

    /**
    * Identifiant de l'utilisateur
    *
    * @var int
    * @access private
    */
    private $_identifiant;
	private $_hash;

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
    * Listener du type AFUP_iAuthentification
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
    public function AFUP_Droits(&$bdd)
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

        $requete = ' SELECT ';
        $requete .= '  id, niveau, niveau_modules, nom, prenom, email, ';
		$requete .= '  CONCAT(id, \'_\', email, \'_\', login) as hash ';
		$requete .= ' FROM ';
        $requete .= '  afup_personnes_physiques ';
        $requete .= ' WHERE ';
        $requete .= '  login=' . $this->_bdd->echapper($login);
        $requete .= '  AND mot_de_passe=' . $this->_bdd->echapper($mot_de_passe);
        $requete .= '  AND etat=' . AFUP_DROITS_ETAT_ACTIF;

        $resultat = $this->_bdd->obtenirEnregistrement($requete);
        if ($resultat !== false) {
            $this->_identifiant = $resultat['id'];
			$this->_hash = md5($resultat['hash']);
			$this->_niveau = $resultat['niveau'];
            $this->_niveau_modules = $resultat['niveau_modules'];
        }
        $this->_est_connecte = ($resultat !== false);
        $this->_echec_connexion = !$this->_est_connecte;

        if ($this->_est_connecte) {
            $_SESSION['afup_login'] = $login;
            $_SESSION['afup_mot_de_passe'] = $mot_de_passe;
            // Envoi la demande de connection aux listeners
            $event = $resultat;
            foreach ($this->_listeners as $el) {
                $el->seConnecter($event);
            }
        }

        return $this->_est_connecte;
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
        }
        $this->_est_connecte = ($personne_physique_id !== false);
        $this->_echec_connexion = !$this->_est_connecte;

        if ($this->_est_connecte) {
            $_SESSION['afup_login'] = $personne_physique['login'];
            $_SESSION['afup_mot_de_passe'] = $personne_physique['mot_de_passe'];
            $_SESSION['afup_niveau'] = AFUP_DROITS_NIVEAU_MEMBRE;
            $_SESSION['afup_niveau_modules'] = "";
            // Envoi la demande de connection aux listeners
            $event = $personne_physique;
            foreach ($this->_listeners as $listener) {
                $listener->seConnecter($event);
            }
        }

        return $this->_est_connecte;
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
        $this->_est_connecte = false;
        $this->_echec_connexion = false;
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
        return $this->_est_connecte;
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
    public function verifierEchecConnexion()
    {
        return $this->_echec_connexion;
    }

    /**
    * Indique si une connexion a échoué
    *
    * @access public
    * @return void
    */
    public function enregistreAuthentification(AFUP_iAuthentification $newListener)
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
}

?>