<?php

/**
 * Conteneur de la classe ProfileManager. 
 * 
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @copyright 2006 Guillaume Ponçon
 * @package afup_rdv
 */

if (!class_exists('RdvConfig')) {
    trigger_error('Class RdvConfig required by Afup_Meeting.', E_USER_ERROR);
}

/**
 * Gestion des profils d'inscription. 
 *
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @copyright 2006 Guillaume Ponçon
 * @package afup_rdv
 * @todo mettre les profils en contexte de classe pour plus d'efficacité.
 * @todo sécuriser l'accès au fichier de profiles
 */
class Afup_Meeting
{
    /**
     * Fichier ou sont stockés les profils. 
     */
    const PROFILE_FILE = 'profiles.txt';

    /**
     * Nombre max de profils
     */
    const NBR_PROFILES_MAX = RdvConfig::RDV_NB_PLACES;

    /**
     * Teste si un profil existe dans la liste. 
     *
     * @param array $profiles
     * @param string $key
     * @return boolean
     */
    private function profileExists($profiles, $key) {
        $key1 = '0' . substr($key, 1);
        $key2 = '1' . substr($key, 1);
        return $profiles[$key1] || $profiles[$key2];
    }

    /**
     * Ecrit la liste des profils dans le fichier de profils. 
     *
     * @param array $profiles
     * @param string $file
     * @return array
     */
    private function registerProfiles($profiles, $file)
    {
        $fileContent = '';
        foreach ($profiles as $key => $serializedProfile) {
            if ($key) {
                $fileContent .= $key . '|' . $serializedProfile . "\n";
            }
        }
        if (file_put_contents($file, $fileContent) === false) {
            return array('Erreur interne, impossible d\'enregistrer votre requête.');
        }
        return array();
    }

    /**
     * Renvoit la liste des profils dans un tableau. 
     *
     * @param string $file
     * @return array
     */
    public function getProfiles($file = null)
    {
        if ($file == null) {
            $file = dirname(__FILE__) . '/' . self::PROFILE_FILE;
        }
        $profiles = array();
        if (file_exists($file)) {
            $lines = file($file);
            foreach ($lines as $line) {
                if ($line) {
                    $tab = explode('|', $line);
                    $profiles[$tab[0]] = trim($tab[1]);
                }
            }
        }
        return $profiles;
    }

    /**
     * AJoute un profile à la liste des profils. 
     *
     * @param Afup_Meeting_Profile $profile
     * @return array
     */
    public function addProfile(Afup_Meeting_Profile $profile)
    {
        $errors = $profile->validate();
        if ($errors !== false) {
            return $errors;
        }
        $current_dir = dirname(__FILE__);
        $profiles = $this->getProfiles();
        if ($this->profileExists($profiles, $profile->key)) {
            return array('Ce profil est déjà enregistré.');
        }
        $profiles[$profile->key] = serialize($profile);
        return $this->registerProfiles($profiles, $current_dir . '/' . self::PROFILE_FILE);
    }

    /**
     * Valide un profil. 
     *
     * @param string $key
     * @param boolean $validationStatus
     * @return array
     */
    public function validateProfile($key, $validationStatus = true)
    {
        $current_dir = dirname(__FILE__);
        $profiles = $this->getProfiles();
        if (!$this->profileExists($profiles, $key)) {
            return array('Ce profil n\'existe pas.');
        }
        $profile = new Afup_Meeting_Profile();
        $profile = unserialize($profiles[$key]);
        $profile->validated = $validationStatus ? '1' : '0';
        unset($profiles[$key]);
        $profiles[$profile->key] = serialize($profile);
        return $this->registerProfiles($profiles, $current_dir . '/' . self::PROFILE_FILE);
    }

    /**
     * Retire un profil définitivement de la liste. 
     *
     * @param string $key
     * @return array
     */
    public function removeProfile($key)
    {
        $current_dir = dirname(__FILE__);
        $profiles = $this->getProfiles();
        if (!$this->profileExists($profiles, $key)) {
            return array('Ce profil n\'existe pas.');
        }
        unset($profiles['0' . substr($key, 1)]);
        unset($profiles['1' . substr($key, 1)]);
        return $this->registerProfiles($profiles, $current_dir . '/' . self::PROFILE_FILE);
    }

    /**
     * Renvoit le nombre de profils validés. 
     *
     * @return integer
     */
    public function getNbrValidatedProfiles()
    {
        $file = dirname(__FILE__) . '/' . self::PROFILE_FILE;
        if (!file_exists($file)) {
            return 0;
        }
        $profiles = file($file);
        $cpt = 0;
        foreach ($profiles as $value) {
            if ($value[0] == '1') {
                $cpt++;
            }
        }
        return $cpt;
    }

    /**
     * Renvoit le timestamp de la date limite précisée dans la configuration
     *
     * @return integer
     */
    public function getDateLimitTimestamp()
    {
        $tabTime = explode('/', RdvConfig::RDV_DATE_LIMIT);
        return mktime(0, 0, 0, $tabTime[1], $tabTime[0], $tabTime[2]);
    }

    /**
     * Retourne une valeur de hachage pour le rendez-vous en cours
     *
     * @return string
     */
    public static function getHashKey()
    {
        static $hashKey = null;

        if ($hashKey === null) {
            $hashKey = md5(RdvConfig::RDV_PAGE . '_hash');
        }
        return $hashKey;
    }

}