<?php
/**
 * Fichier Feed RSS site 'PlanetePHP'
 * 
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 * 
 * @category PlanetePHP
 * @package  PlanetePHP
 * @group    Pages
 */

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
require_once dirname(__FILE__) .'/../../../sources/Afup/AFUP_Planete_Billet.php';

$feed = array(
	'title'  => 'planete php fr',
	'url'    => 'http://planete-php.fr/',
	'link'   => 'http://planete-php.fr/rss.php',
	'email'  => 'planetephpfr@afup.org',
	'author' => 'Perrick Penet / AFUP',
	'date'   => date(DATE_RSS),
);

$billet = new AFUP_Planete_Billet($bdd);
$derniersBilletsComplets = $billet->obtenirDerniersBilletsComplets(0, DATE_RSS, 20);

$smarty->assign('feed',    $feed);
$smarty->assign('billets', $derniersBilletsComplets);

header('Content-Type: text/xml; charset=UTF-8');
$smarty->display('rss.xml');