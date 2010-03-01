<?php

Afup::includeOnce('smarty/Smarty.class.php');

/**
 * Moteur de template de l'annuaire, basé sur smarty. 
 *
 * @todo baser le moteur sur Zend_View : plus souple, moins gourmand
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @copyright 2006 Association Française des Utilisateurs de PHP
 * @since 1.0 - Thu Jul 13 14:32:32 CEST 2006
 * @package afup
 * @subpackage directory
 */
class Afup_Directory_Template extends Smarty
{

    /**
     * Configuration de smarty, chargement d'une instance du moteur. 
     *
     * @todo caching conditionnel
     * @return Afup_Directory_Template
     */
    public function Afup_Directory_Template()
    {
        $this->Smarty();

        $this->template_dir = Afup::getLibraryPath() . '/../templates/directory/';
        $this->compile_dir = Afup::getLibraryPath() . '/../cache/templates/directory/';
        $this->config_dir = Afup::getLibraryPath() . '/';
        $this->cache_dir = Afup::getLibraryPath() . '/../cache/templates/directory/cache/';

        $this->caching = false;
    }
}