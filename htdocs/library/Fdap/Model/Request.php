<?php

/**
 * Conventions pour l'utilisation des fonctionnalités automatiques
 * 
 * L'objet modèle porte le nom de la table ou de la vue (qui doit être en minuscules)
 * 
 * Toutes les propriétés du modèle portent le nom des champs (qui doivent être en minuscules)
 */

/**
 * Classe permettant de construire les requêtes de gestion d'un modèle de données
 * 
 * @copyright  2006 Guillaume Ponçon - all rights reserved
 * @license    http://www.zend.com/license/3_0.txt   PHP License 3.0
 * @version    Release: @1.0.0@
 * @since      Class available since Release 1.0.0
 * @author     Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @package    fdap
 * @subpackage model
 */
class Fdap_Model_Request
{
    
    /**
     * Objet modèle courant
     *
     * @var Fdap_Model
     */
    private $model = null;
    
    /**
     * Charge le modèle $model
     *
     * @param Fdap_Model $model
     */
    public function __construct(Fdap_Model $model)
    {
        $this->model = $model;
    }
    
    /**
     * Renvoit la requête select associée au modèle
     *
     * @return string
     */
    public function getSelectQuery($fields = null)
    {
        $table = strtolower(get_class($this->model));
        $fields = $fields ? $fiels : '*';
        $query = 'SELECT ' . $fields . ' FROM ' . $table;
        return $query;
    }
    
    /**
     * Renvoit l'insert associé au modèle
     *
     * @throws Fdap_Model_Exception
     * @return string
     */
    public function getAddQuery()
    {
        $validation = $this->model->validate();
        if ($validation !== true) {
            throw new Fdap_Model_Exception("The add query can't be transmited if the model is not valid", $validation);
        }
    }
    
    /**
     * Renvoit l'update associé au modèle
     *
     * @throws Fdap_Model_Exception
     * @return string
     */
    public function getUpdateQuery()
    {
        $validation = $this->model->validate();
        if ($validation !== true) {
            throw new Fdap_Model_Exception("The update query can't be transmited if the model is not valid", $validation);
        }
    }
    
}