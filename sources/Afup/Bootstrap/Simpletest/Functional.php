<?php
/**
 * Fichier (bootstrap) du contexte tests fonctionnels avec SimpleTest
 * 
 * Ce fichier doit contenir l'ensemble des directives d'initialisation
 * nécessaire au chargement de toute l'application pour une exécution
 * de script de tests fonctionnels
 * 
 * Ce fichier est systématiquement à inclure en haut de chaque
 * script de test.
 * 
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 * 
 * @category AFUP
 * @package  AFUP
 * @group    Bootstraps
 */

// chargement des paramétrages génériques / multi-contextuels de l'application

require_once dirname(__FILE__) . '/_Common.php';

define('CHEMIN_APPLICATION', 'http://localhost/gestafup/trunk/site/');

require_once 'afup/AFUP_Base_De_Donnees.php';

require_once 'simpletest/web_tester.php';
require_once 'simpletest/reporter.php';
require_once 'simpletest/browser.php';