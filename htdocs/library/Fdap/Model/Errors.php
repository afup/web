<?php

/**
 * Cette classe sert à gérer une collection d'erreurs dans un modèle de structuration MVC
 * 
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @since 2.0 Mon Dec 18 21:21:49 CET 2006
 * @copyright 2006 Guillaume Ponçon - all rights reserved
 * @package fdap
 * @subpackage model
 */
class Fdap_Model_Errors
{
    private $errors = array();
    
    public function addNewError($field, $comment, $severity = false)
    {
        $error = new Fdap_Model_Error($field, $comment, $severity);
        $this->addError($error);
    }
    
    public function addError(Fdap_Model_Error $error)
    {
        $this->errors[] = $error;
    }
    
    public function getNbErrors()
    {
        return count($this->errors);
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
}

