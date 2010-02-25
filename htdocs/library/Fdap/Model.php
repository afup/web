<?php

/**
 * Classe mère de tout modèle de données MVC
 * 
 * @copyright  2007 Guillaume Ponçon - all rights reserved
 * @license    http://www.zend.com/license/3_0.txt   PHP License 3.0
 * @version    Release: @1.0.0@
 * @since      Class available since Release 1.0.0
 * @author     Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @package    fdap
 */
abstract class Fdap_Model implements Fdap_Model_Interface
{

    /**
     * DSN PDO pour la manipulation des données du modèle
     */
    private static $dsn = null;

    /**
     * Renvoit true si c'est valide, un tableau d'erreurs sinon
     *
     * @return boolean|array
     * @throws Fdap_Model_Exception
     */
    public function validate()
    {
        throw new Fdap_Model_Exception("This function must be declared and completed in the concrete model class.");
    }

    /**
     * Renvoit l'objet Model_Request correspondant au modèle
     *
     * @return Fdap_Model_Request
     * @throws Fdap_Model_Exception
     */
    public function getModelRequest()
    {
        throw new Fdap_Model_Exception("This function must be declared and completed in the concrete model class.");
    }

    /**
     * Retourne un objet PDO persistant
     *
     * @return PDO
     * @throws Fdap_Model_Exception
     * @throws PDOException
     */
    private static function getPdo($dsn = null)
    {
        static $pdo = null;

        self::$dsn = $dsn === null ? self::$dsn : $dsn;
        if (self::$dsn === null) {
            throw new Fdap_Model_Exception("No dsn found to get PDO object.");
        }
        if ($pdo === null) {
            $pdo = new PDO(self::$dsn);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $pdo;
    }

    /**
     * Renvoit un objet PDOStatement pour itération sur les données du modèle (recommandé)
     *
     * @param Fdap_Model $model
     * @return PDOStatement
     */
    public static function getPdoStatement(Fdap_Model $model)
    {
        $pdo = self::getPdo();
        return $pdo->query($model->getModelRequest()->getSelectQuery(), PDO::FETCH_ASSOC);
    }

    /**
     * Renvoit un tableau à deux dimensions contenant tous les résultats de la requête (déconseillé)
     *
     * @param Fdap_Model $model
     * @deprecated le fetchAll, c'est pas bien !
     * @return array
     */
    public static function fetchAll(Fdap_Model $model)
    {
        $stmt = self::getPdoStatement($model);
        return $stmt->fetchAll();
    }

    /**
     * Exécute la requête d'ajout
     *
     * @param Fdap_Model $model
     * @return PDOStatement
     */
    public static function add(Fdap_Model $model)
    {
        return self::getPdo()->query($model->getModelRequest()->getAddQuery());
    }

    /**
     * Retourne un objet modèle
     *
     * @param string $modelName
     * @return boolean
     */
    public static function includeModel($modelName)
    {
        include '../application/models/' . $modelName . '.php';
        return true;
    }

}