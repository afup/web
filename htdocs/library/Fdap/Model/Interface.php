<?php

/**
 * Cette interface doit être implémentée par chaque
 * modèle de données.
 * 
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @since 2.0 Fri Oct 06 11:40:55 CEST 2006
 * @copyright 2006 Guillaume Ponçon - all rights reserved
 * @package fdap
 * @subpackage model
 */
interface Fdap_Model_Interface
{
    /**
     * Validation des données
     *
     * @return Fdap_Model_Errors
     */
    public function validate();
        
    /**
     * Renvoit l'objet Model_Request correspondant au modèle pour la mise à jour des données
     *
     * @return Fdap_Model_Request
     */
    public function getModelRequest();
    
}