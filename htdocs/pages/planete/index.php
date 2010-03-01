<?php
/**
 * Fichier principal site 'PlanetePHP'
 * 
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 * 
 * @category PlanetePHP
 * @package  PlanetePHP
 * @group    Pages
 */

// 0. initialisation (bootstrap) de l'application

require_once dirname(__FILE__) . '/../../include/prepend.inc.php';

// 1. chargement des classes nécessaires

require_once 'Afup/AFUP_Planete_Billet.php';
require_once 'Afup/AFUP_Planete_Flux.php';

// 2. récupération et filtrage des données

$billet                  = new AFUP_Planete_Billet($bdd);
$flux                    = new AFUP_Planete_Flux($bdd);
$page                    = isset($_GET['page']) ? abs((int)$_GET['page']) : 0;
$derniersBilletsComplets = $billet->obtenirDerniersBilletsTronques($page);
$listeFlux               = $flux->obtenirTousParDateDuDernierBillet();

// 3. assignations des variables du template

$smarty->assign('billets',   $derniersBilletsComplets);
$smarty->assign('flux',      $listeFlux);
$smarty->assign('suivant',   count($derniersBilletsComplets) ? -1 : $page + 1);
$smarty->assign('precedant', $page - 1);

// 4. affichage de la page en utilisant le modèle spécifié

$smarty->display('index.html');