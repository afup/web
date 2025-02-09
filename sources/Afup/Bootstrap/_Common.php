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
use Afup\Site\Utils\Configuration;

$root = dirname(__DIR__, 3);

require_once $root . '/vendor/autoload.php';

// définitions des constantes
define('AFUP_CHEMIN_RACINE', $root . '/htdocs/');

// Voir la classe Afup\Site\Association\Personnes_Morales
define('AFUP_PERSONNES_PHYSIQUES', 0);
define('AFUP_COTISATION_PERSONNE_PHYSIQUE', 30);

date_default_timezone_set('Europe/Paris');

/**
 * Ajout des répertoires contenant les différentes classes et script à inclure
 * dans l'include path pour éviter de les inclure avec chemin absolu
 * et pouvoir bénéficier prochainement du mécanisme d'autoloading de classe
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
set_include_path($root . '/dependencies' . PATH_SEPARATOR . $root . '/sources');

// préparation de la requête / session
require_once __DIR__ . '/../fonctions.php';

// chargement de la configuration
$conf = new Configuration();

// mets la configuration dans une 'clé de registre' accessible à tout moment
$GLOBALS['AFUP_CONF'] = $conf;

// initialisation de la couche d'abstraction de la base de données
$bdd = new _Site_Base_De_Donnees();

// mets la connexion db dans une 'clé de registre' accessible à tout moment
$GLOBALS['AFUP_DB'] = $bdd;
