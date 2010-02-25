<?php
/**
 * Diverses méthodes permettant de se simplifier la vie
 */
class AFUP_Utils {
    /**
     * Recupere un objet de type AFUP_Droits
     * 
     * @param  object    $bdd   Instance de la couche d'abstraction à la base de données 
     * @access public
     * @return object    AFUP_Droits
     */
    public static function fabriqueDroits($bdd) {
        if (!defined('AFUP_CHEMIN_RACINE')) {
        	throw new Exception("AFUP_CHEMIN_RACINE doit être défini.");
        }
        require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Droits.php';
        require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_AuthentificationWiki.php';

        // Gestion de l'authentification spécifique au Wiki
        $authentificationWiki = new AFUP_AuthentificationWiki();
        
        $droits = new AFUP_Droits($bdd);
        $droits->enregistreAuthentification($authentificationWiki);
        
        return $droits;
    }
}
?>