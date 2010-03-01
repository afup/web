<?php
/**
 * Fichier (bootstrap) du contexte Ligne de Commande (CLI)
 * 
 * Ce fichier doit contenir l'ensemble des directives d'initialisation
 * nécessaire au chargement de toute l'application pour une exécution
 * d'un script en ligne de commande.
 * 
 * Ce fichier est systématiquement à inclure en haut de chaque
 * script batch.
 * 
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 * 
 * @category AFUP
 * @package  AFUP
 * @group    Bootstraps
 */

// enregistrement du timestamp à des fins de logging

$startMicrotime = microtime(true);

// chargement des paramétrages génériques / multi-contextuels de l'application

require_once dirname(__FILE__) . '/_Common.php';

// initialisation du mécanisme de logging

require 'afup/AFUP_Logs.php';

AFUP_Logs::initialiser($bdd, 0);