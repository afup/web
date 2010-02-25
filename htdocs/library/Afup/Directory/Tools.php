<?php

/**
 * Liste de fonctions utiles pour l'annuaire. 
 * 
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @copyright 2006 Association Française des Utilisateurs de PHP
 * @since 1.0 - Thu Jul 13 14:07:03 CEST 2006
 * @package afup
 * @subpackage directory
 */
abstract class Afup_Directory_Tools
{

    /**
     * Interdit l'instanciation de la classe. 
     *
     * @return Afup_Directory_Tools
     */
    private function __construct()
    {}
    
    /**
     * Renvoit un tableau pour la pagination, facile à manipuler dans le template. 
     *
     * @param integer $nbPages
     * @param integer $currentPage
     * @return array
     */
    public static function getPaginationTable($nbPages, $currentPage)
    {
        $retVal = array();
        for ($i = 0; $i < $nbPages; $i++) {
            $retVal[$i + 1] = $i == $currentPage ? '1' : '0';
        }
        return $retVal;
    }
    
}