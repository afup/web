<?php

/**
 * Classe d'exceptions de la librairie Model. 
 * 
 * Cette classe transmet le tableau d'erreurs de saisie des formulaire s'il y en a
 * 
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @since 2.0 Mon Dec 18 21:21:32 CET 2006
 * @copyright 2006 Guillaume Ponçon - all rights reserved
 * @package fdap
 * @subpackage model
 */
class Fdap_Model_Exception extends Fdap_Exception
{

    private $errors = array();

    /**
     * Cette exception prend en paramètre un message (pour débogage) et des erreurs à afficher.
     *
     * @param string $message
     * @param array $errors
     */
    public function __construct($message, $errors)
    {
        parent::__construct($message);
        $this->setErrors($errors);
    }
    
    /**
     * Renvoit le tableau d'erreurs
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Ajout d'une nouvelle erreur
     *
     * @param string $error
     */
    public function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Enregistre un tableau d'erreurs
     *
     * @throws Fdap_Model_Exception
     * @param array $errors
     */
    public function setErrors($errors)
    {
        if (is_array($errors)) {
            $this->errors = $errors;
        } else {
            throw new Fdap_Model_Exception("A table containing errors is required");
        }
    }

}
