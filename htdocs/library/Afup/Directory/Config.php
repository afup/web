<?php

/**
 * Configuration de l'annuaire et données statiques. 
 *
 * @todo rendre moins bricolage lors du passage en PHP 5.
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @copyright 2006 Association Française des Utilisateurs de PHP
 * @since 1.0 - Fri Jun 02 20:29:33 CEST 2006
 * @package afup
 * @subpackage directory
 */
abstract class Afup_Directory_Config
{

    /**
     * Interdit l'instanciation de la classe 
     *
     * @return Afup_Directory_Config
     */
    private function __construct()
    {}

    /**
     * Nombre de lignes par page.
     *
     * @return integer
     */
    public static function getLinesByPage()
    {
        return 10;
    }

    /**
     * Activités des entreprises. 
     *
     * @return mixed
     */
    public static function getActivities($id = null)
    {
        $activities = array(
        1 => 'Hébergement',
        2 => 'Développement au forfait',
        3 => 'Développement en régie',
        4 => 'Conseil / Architecture',
        5 => 'Formation',
        6 => 'Editeurs (logiciels PHP et pour PHP)'
        );
        
        if ($id != null) {
            if (!isset($activities[$id])) {
                return false;
            }
            return $activities[$id];
        }
        return $activities;
    }

    /**
     * Forme juridique.
     *
     * @return mixed
     */
    public static function getLegalStatus($id = null)
    {
        $legalStatus = array(
        1 => 'Entreprise individuelle',
        2 => 'Profession libérale',
        3 => 'EURL/SARL',
        4 => 'SA/SAS',
        5 => 'Association'
        );
        if ($id != null) {
            if (!isset($legalStatus[$id])) {
                return false;
            }
            return $legalStatus[$id];
        }
        return $legalStatus;
    }

    /**
     * Taille de l'entreprise.
     *
     * @return mixed
     */
    public static function getCompanySize($id = null)
    {
        $size = array(
        1 => 'Une personne',
        2 => 'Entre 2 et 5 personnes',
        3 => 'Entre 6 et 10 personnes',
        4 => 'Plus de 10 personnes'
        );
        if ($id != null) {
            if (!isset($size[$id])) {
                return false;
            }
            return $size[$id];
        }
        return $size;
    }

    /**
     * Zones géographiques
     *
     * @return mixed
     */
    public static function getZone($id = null)
    {
        $zones = array(
        1 => '01 - Ile de France',
        2 => '02 - Nord Ouest',
        3 => '03 - Nord Est',
        4 => '04 - Sud Est',
        5 => '05 - Sud Ouest'
        );
        if ($id != null) {
            if (!isset($zones[$id])) {
                return false;
            }
            return $zones[$id];
        }
        return $zones;
    }
}