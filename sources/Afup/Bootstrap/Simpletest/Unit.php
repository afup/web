<?php
/**
 * Fichier (bootstrap) du contexte tests unitaires avec SimpleTest
 * 
 * Ce fichier doit contenir l'ensemble des directives d'initialisation
 * nécessaire au chargement de toute l'application pour une exécution
 * de script de tests unitaires
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

require_once dirname(__FILE__)."/../simpletest/autorun.php";