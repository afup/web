<?php

/**
 * Classe de base des librairies AFUP compatibles Zend Framework. 
 *
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @copyright 2006 Association Française des Utilisateurs de PHP
 * @since 1.0 - Fri Jun 02 18:15:12 CEST 2006
 * @package afup
 */
abstract class Afup
{
    /**
     * Interdit l'instanciation de la classe en cas d'héritage.
     * 
     * @access private
     * @todo rendre véritablement privé avec PHP 5
     * @todo rendre compatible PHP 5
     */
    private function __construct()
    {}

    /**
     * Renvoit le chemin vers les librairies. 
     *
     * @return string
     */
    public static function getLibraryPath()
    {
        static $libPath = null;

        if ($libPath === null) {
            $libPath = dirname(__FILE__);
        }
        return $libPath;
    }

    /**
     * Inclusion d'un fichier contenu dans les librairies. 
     *
     * @param string $file
     */
    public static function includeOnce($file)
    {
        include_once(Afup::getLibraryPath() . '/' . $file);
    }

    /**
     * Inclusion d'une classe.
     *
     * @param string $class
     */
    public static function includeClass($class)
    {
        $classPath = strtr($class, '_', '/') . '.php';
        Afup::includeOnce($classPath);
    }

}

if (!function_exists('__autoload')) {
    function __autoload($class) {
        include Afup::getLibraryPath() . '/' . strtr($class, '_', '/') . '.php';
    }
}