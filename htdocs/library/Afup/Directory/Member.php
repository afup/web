<?php

/**
 * Entité représentant un membre de l'annuaire.
 * 
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @since 2.0 Fri Dec 15 22:42:20 CET 2006
 * @copyright 2006 Guillaume Ponçon - all rights reserved
 * @package afup
 * @subpackage directory
 * @todo déplacer dans les modèles de données lors d'un refactoring MVC
 */
class Afup_Directory_Member extends Fdap_Model {
    
    private $id;
    private $formeJuridique;
    private $raisonSociale;
    private $siren;
    private $email;
    private $siteWeb;
    private $telephone;
    private $fax;
    private $adresse;
    private $codePostal;
    private $ville;
    private $zone;
    private $numeroFormateur;
    private $membreAfup;
    private $valide;
    private $dateCreation;
    private $tailleSociete;
    private $password;

    /**
     * Auto-chargement des données du formulaire ou de la table de la BD
     *
     * @param array $table
     */
    public function __construct($table = null)
    {
        if ($table !== null) {
            $this->fromAfupDb($table);
        }
    }
    
    /**
     * Remplit l'objet depuis la base de données Afup
     *
     * @param array $table
     */
    public function fromAfupDb($table)
    {
        $this->setId($table['ID']);
        $this->setFormeJuridique(stripslashes($table['FormeJuridique']));
        $this->setRaisonSociale(stripslashes($table['RaisonSociale']));
        $this->setSiren($table['SIREN']);
        $this->setEmail($table['Email']);
        $this->setSiteWeb($table['SiteWeb']);
        $this->setTelephone($table['Telephone']);
        $this->setAdresse($table['Adresse']);
        $this->setFax($table['Fax']);
        $this->setCodePostal($table['CodePostal']);
        $this->setVille(stripslashes($table['Ville']));
        $this->setZone(stripslashes($table['Zone']));
        $this->setNumeroFormateur($table['NumeroFormateur']);
        $this->setMembreAfup($table['MembreAFUP']);
        $this->setValide($table['Valide']);
        $this->setDateCreation($table['DateCreation']);
        $this->setTailleSociete($table['TailleSociete']);
        $this->setPassword(stripslashes($table['Password']));
    }
    
    /*
     * Accesseurs / filtres
     *
     * Note : ces accesseurs filtrent les données pour les stocker selon le bon format.
     * Le contrôle d'intégrité se fait plus loin dans la méthode "validate"
     */

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return true;
    }   
    
    public function getFormeJuridique()
    {
        return $this->formeJuridique;
    }
    
    public function setFormeJuridique($formeJuridique)
    {
        $this->formeJuridique = $formeJuridique;
        return true;
    }   
    
    public function getRaisonSociale()
    {
        return $this->raisonSociale;
    }
    
    public function setRaisonSociale($raisonSociale)
    {
        $this->raisonSociale = $raisonSociale;
        return true;
    }   
    
    public function getSiren()
    {
        return $this->siren;
    }
    
    public function setSiren($siren)
    {
        $this->siren = $siren;
        return true;
    }   
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
        return true;
    }   
    
    public function getSiteWeb()
    {
        return $this->siteWeb;
    }
    
    public function setSiteWeb($siteWeb)
    {
        $this->siteWeb = $siteWeb;
        return true;
    }   
    
    public function getTelephone()
    {
        return $this->telephone;
    }
    
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
        return true;
    }
    
    public function getAdresse()
    {
        return $this->adresse;
    }
    
    public function setAdresse($adresse)
    {
        $this->adresse = (string) $adresse;
        return true;
    }
    
    public function getFax()
    {
        return $this->fax;
    }
    
    public function setFax($fax)
    {
        $this->fax = $fax;
        return true;
    }   
    
    public function getCodePostal()
    {
        return $this->codePostal;
    }
    
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;
        return true;
    }   
    
    public function getVille()
    {
        return $this->ville;
    }
    
    public function setVille($ville)
    {
        $this->ville = $ville;
        return true;
    }   
    
    public function getZone()
    {
        return $this->zone;
    }
    
    public function setZone($zone)
    {
        $this->zone = $zone;
        return true;
    }   
    
    public function getNumeroFormateur()
    {
        return $this->numeroFormateur;
    }
    
    public function setNumeroFormateur($numeroFormateur)
    {
        $this->numeroFormateur = $numeroFormateur;
        return true;
    }   
    
    public function getMembreAfup()
    {
        return $this->membreAfup;
    }
    
    public function setMembreAfup($membreAfup)
    {
        $this->membreAfup = $membreAfup;
        return true;
    }   
    
    public function getValide()
    {
        return $this->valide;
    }
    
    public function setValide($valide)
    {
        $this->valide = $valide;
        return true;
    }   
    
    public function getDateCreation()
    {
        return $this->dateCreation;
    }
    
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
        return true;
    }   
    
    public function getTailleSociete()
    {
        return $this->tailleSociete;
    }
    
    public function setTailleSociete($tailleSociete)
    {
        $this->tailleSociete = $tailleSociete;
        return true;
    }   
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function setPassword($password)
    {
        $this->password = $password;
        return true;
    }
    
    /**
     * Renvoit l'objet de manipulation des données du modèle
     *
     * @return Fdap_Model_Request
     */
    public function isUpdate()
    {
        return new Fdap_Model_Request($this);
    }
    
    /**
     * Renvoit la liste des erreurs détectées dans ce modèle de données
     *
     * @return Fdap_Model_Errors
     */
    public function validate()
    {
        $errors = new Fdap_Model_Errors();
        if (!is_numeric($id) || $id <= 0) {
            $errors->addNewError('ID', 'Cet identifiant est incorrect', true);
        }
        if (!is_string($raisonSociale)) {
            $errors->addNewError('RaisonSociale', "Cette raison sociale est incorrecte", false);
        }
        if (!in_array($formeJuridique, Afup_Directory_Config::getLegalStatus())) {
            $errors->addNewError('FormeJuridique', "Cette forme juridique est inconnue", true);
        }
        return $errors;
    }

}
