<?php
/**
 * Fichier Feed ATOM site 'PlanetePHP'
 * 
 * @copyright 2010 Association Française des Utilisateurs de PHP
 * 
 * @category PlanetePHP
 * @package  PlanetePHP
 * @group    Pages
 */

use Afup\Site\Planete\Planete_Billet;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';


$feed = array(
	'title'  => 'planete php fr',
	'url'    => 'http://planete-php.fr/',
	'link'   => 'http://planete-php.fr/flux.php',
	'email'  => 'planetephpfr@afup.org',
	'author' => 'Perrick Penet / AFUP',
	'date'   => date(DATE_ATOM),
);

$billet                  = new Planete_Billet($bdd);
$derniersBilletsComplets = $billet->obtenirDerniersBilletsComplets(0, DATE_ATOM, 20);

$smarty->assign('feed', $feed);
$smarty->assign('billets', $derniersBilletsComplets);

header('Content-Type: application/atom+xml; charset=UTF-8');
$smarty->display('flux.xml');