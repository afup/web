<?php

/**
 * Entité de stockage d'une erreur élémentaire.
 * 
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @since 2.0 Fri Oct 06 11:58:01 CEST 2006
 * @copyright 2006 Guillaume Ponçon - all rights reserved
 * @package fdap
 * @subpackage model
 */
class Fdap_Model_Error
{
    const SEVERITY_NORMAL = false;
    const SEVERITY_SEVERE = true;
    
    private $field;
    private $comment;
    private $severity;
    
    public function __construct($field, $comment, $severity = false)
    {
        $this->field    = (string)  $field;
        $this->comment  = (string)  $comment;
        $this->severity = (boolean) $severity;
    }
    
    public function getField()
    {
        return $this->field;
    }
    
    public function getComment()
    {
        return $this->comment;
    }
    
    public function getSeverity()
    {
        return $this->severity;
    }
    
}