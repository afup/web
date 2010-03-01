<?php
require_once 'afup/AFUP_iAuthentification.php';

/**
  * Gestion de l'authentification pour le Wiki de l'afup
  */
class AFUP_AuthentificationWiki implements AFUP_iAuthentification {
    /**
     * Connecte l'utilisateur
     * 
     * @param  Array   $event          Evenement de connection   
     * @access public
     * @return void
     */
    public function seConnecter($event) {
        $wikiUser = array();
        $wikiUser["show_comments"] = "Y";
        $wikiUser["name"]          = ucfirst(strtolower($event["prenom"])).ucfirst(strtolower($event["nom"]));
        $wikiUser["email"]         = $event["email"];
        $_SESSION["user"] = $wikiUser;
    }

    /**
     * Déconnecte l'utilisateur
     * 
     * @param  Array   $event          Evenement de déconnection   
     * @access public
     * @return void
     */
    public function seDeconnecter($event) {
        unset($_SESSION["user"]);
    }
}
?>