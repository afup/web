<?php

/**
 * Conteneur de la classe Profile. 
 * 
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @copyright 2006 Guillaume Ponçon
 * @package afup_rdv
 */

/**
 * Entité de stockage et manipulation des profils. 
 *
 * - Creation date : Sat Jun 10 01:27:32 CEST 2006
 * - File : Profile.php
 * 
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @copyright 2006 Guillaume Ponçon
 * @package afup_rdv
 */
class Afup_Meeting_Profile {

    /**
     * Code de sécurité pour l'identifiant crypté du profil. 
     */
    const SECURITY_KEY = 'cuicui';

    private $firstname = '';
    private $lastname  = '';
    private $company   = '';
    private $email     = '';
    private $tel       = '';
    private $validated = '0';

    /**
     * Accès filtré aux propriétés (écriture).
     *
     * @param string $key
     * @param string $value
     * @throws Exception
     */
    public function __set($key, $value)
    {
        if (isset($this->$key)) {
            $this->$key = (string) $value;
        } else {
            throw new Exception('Requête incorrecte.');
        }
    }

    /**
     * Accès filtré aux propriétés (lecture).
     *
     * @param string $key
     * @return bool|string
     */
    public function __get($key)
    {
        if (isset($this->$key)) {
            return $this->$key;
        }
        if ($key == 'key') {
            return $this->validated . 'g' . md5($this->email . self::SECURITY_KEY);
        }
        return false;
    }

    /**
     * Retourne le contenu de l'objet formatté en mode texte. 
     *
     * @return string
     */
    public function __toString()
    {
        $retVal  = 'Prénom    : ' . $this->firstname . "\n";
        $retVal .= 'Nom       : ' . $this->lastname  . "\n";
        $retVal .= 'Société   : ' . $this->company   . "\n";
        $retVal .= 'Email     : ' . $this->email     . "\n";
        $retVal .= 'Téléphone : ' . $this->tel       . "\n";
        return $retVal;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return true;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return true;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany($company)
    {
        $this->company = $company;
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

    public function getTel()
    {
        return $this->tel;
    }

    public function setTel($tel)
    {
        $this->tel = $tel;
        return true;
    }

    public function getValidated()
    {
        return $this->validated;
    }

    public function setValidated($validated)
    {
        $this->validated = $validated;
        return true;
    }

    public function validate()
    {
        $errors = array();
        $lenFirstname = strlen($this->firstname);
        $lenLastname = strlen($this->lastname);
        $lenCompany = strlen($this->company);
        if ($lenFirstname < 2 || $lenFirstname > 40) {
            $errors[] = "Votre prénom est obligatoire et doit comporter entre 2 et 40 caractères.";
        }
        if ($lenLastname < 2 || $lenLastname > 40) {
            $errors[] = "Votre nom est obligatoire et doit comporter entre 2 et 40 caractères.";
        }
        if ($lenCompany < 2 || $lenCompany > 40) {
            $errors[] = "Votre société est obligatoire et doit comporter entre 2 et 40 caractères.";
        }
        if (!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z]{2,5}$/', $this->email)) {
            $errors[] = "Votre e-mail n'est pas valide.";
        }
        if (!preg_match('/^[0-9() +]*$/', $this->tel)) {
            $errors[] = "Votre numéro de téléphone n'est pas valide.";
        }
        return count($errors) ? $errors : false;
    }

}
