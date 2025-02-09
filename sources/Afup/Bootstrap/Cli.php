<?php

declare(strict_types=1);
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

use Afup\Site\Utils\Logs;

$startMicrotime = microtime(true);

require_once __DIR__ . '/_Common.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

Logs::initialiser($bdd, 0);
