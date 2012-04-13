<?php
/**
 * Fichier d'initialisation de l'application (bootstrap), partie commune
 * à tous les contextes (points d'entrée) d'usage de l'application
 *
 * Ce fichier doit contenir l'ensemble des directives d'initialisation
 * nécessaire au chargement de toute l'application qu'il s'agisse
 * d'un contexte page web ou bien batch ligne de commande (ou autre).
 *
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 *
 * @category AFUP
 * @package  AFUP
 * @group    Bootstraps
 */

// racine de l'application (pas du document root !)

$root = realpath(dirname(__FILE__) . '/../../..');

// définitions des constantes

define('AFUP_CHEMIN_RACINE', $root . '/htdocs/');

date_default_timezone_set('Europe/Paris');

/**
 * Ajout des répertoires contenant les différentes classes et script à inclure
 * dans l'include path pour éviter de les inclure avec chemin absolu
 * et pouvoir bénéficier prochainement du mécanisme d'autoloading de classe
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
set_include_path(
                       $root . '/dependencies'
    . PATH_SEPARATOR . $root . '/sources'
    . PATH_SEPARATOR . get_include_path()
);

// préparation de la requête / session

require_once 'Afup/fonctions.php';

// chargement de la configuration

require_once 'Afup/AFUP_Configuration.php';

$conf = new AFUP_Configuration($root . '/configs/application/config.php');

// mets la configuration dans une 'clé de registre' accessible à tout moment

$GLOBALS['AFUP_CONF'] = $conf;

// initialisation de la couche d'abstraction de la base de données

require_once 'Afup/AFUP_Base_De_Donnees.php';

$bdd = new AFUP_Base_De_Donnees(
    $conf->obtenir('bdd|hote'),
    $conf->obtenir('bdd|base'),
    $conf->obtenir('bdd|utilisateur'),
    $conf->obtenir('bdd|mot_de_passe')
);

// mets la connexion db dans une 'clé de registre' accessible à tout moment

$GLOBALS['AFUP_DB'] = $bdd;