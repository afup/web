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

use Afup\Site\Corporate\_Site_Base_De_Donnees;
use Afup\Site\Utils\Configuration;

// chargement de la configuration
$conf = new Configuration();

// mets la configuration dans une 'clé de registre' accessible à tout moment
$GLOBALS['AFUP_CONF'] = $conf;

// initialisation de la couche d'abstraction de la base de données
$bdd = new _Site_Base_De_Donnees();

// mets la connexion db dans une 'clé de registre' accessible à tout moment
$GLOBALS['AFUP_DB'] = $bdd;
