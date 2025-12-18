<?php

declare(strict_types=1);

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
use Afup\Site\Corporate\_Site_Base_De_Donnees;
use Symfony\Component\Dotenv\Dotenv;

$root = dirname(__DIR__, 3);

require_once $root . '/vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__ . '/../../../.env');

// définitions des constantes
if (!defined('AFUP_CHEMIN_RACINE')) {
    define('AFUP_CHEMIN_RACINE', $root . '/htdocs/');
}
date_default_timezone_set('Europe/Paris');

// préparation de la requête / session
require_once __DIR__ . '/../fonctions.php';

// initialisation de la couche d'abstraction de la base de données
$bdd = new _Site_Base_De_Donnees();

// mets la connexion db dans une 'clé de registre' accessible à tout moment
$GLOBALS['AFUP_DB'] = $bdd;
