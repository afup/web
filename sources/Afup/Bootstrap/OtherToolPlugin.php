<?php

declare(strict_types=1);
/**
 * Fichier (bootstrap) du contexte Plugin d'un autre outils (ex: Wakka)
 *
 * Ce fichier doit contenir l'ensemble des directives d'initialisation
 * nécessaire au chargement de toute l'application pour une exécution
 * d'un plugin d'un autre outil (ex: utiliser les classes AFUP dans wakka)
 *
 * Ce fichier est systématiquement à inclure en haut de chaque
 * plugin.
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

require_once __DIR__ . '/_Common.php';
