<?php
/**
 * Fichier Feed ATOM site 'PlanetePHP'
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

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

// 1. chargement des classes nécessaires

require_once 'Afup/AFUP_Planete_Billet.php';

// 2. récupération et filtrage des données

$feed = array(
	'title'  => 'planete php fr',
	'url'    => 'http://planete-php.fr/',
	'link'   => 'http://planete-php.fr/flux.php',
	'email'  => 'planetephpfr@afup.org',
	'author' => 'Perrick Penet / AFUP',
	'date'   => date(DATE_ATOM),
);

$billet                  = new AFUP_Planete_Billet($bdd);
$derniersBilletsComplets = $billet->obtenirDerniersBilletsComplets(0, DATE_ATOM, 20);

// 3. assignations des variables du template

$smarty->assign('feed', $feed);
$smarty->assign('billets', $derniersBilletsComplets);

// 4. affichage de la page en utilisant le modèle spécifié

header('Content-Type: application/atom+xml; charset=UTF-8');
$smarty->display('flux.xml');