<?php
interface AFUP_iAuthentification {
    /**
     * Connecte l'utilisateur
     * 
     * @param  Array   $event          Evenement de connection   
     * @access public
     * @return void
     */
    function seConnecter($event);

    /**
     * Deconnecte l'utilisateur
     * 
     * @param  Array   $event          Evenement de déconnection   
     * @access public
     * @return void
     */
    function seDeconnecter($event);
}
?>