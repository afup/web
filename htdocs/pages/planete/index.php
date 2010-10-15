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

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
require_once dirname(__FILE__) .'/../../../sources/Afup/AFUP_Planete_Billet.php';
require_once dirname(__FILE__) .'/../../../sources/Afup/AFUP_Planete_Flux.php';

$billet                  = new AFUP_Planete_Billet($bdd);
$flux                    = new AFUP_Planete_Flux($bdd);
$page                    = isset($_GET['page']) ? abs((int)$_GET['page']) : 0;
$derniersBilletsComplets = $billet->obtenirDerniersBilletsTronques($page);
$listeFlux               = $flux->obtenirTousParDateDuDernierBillet();

$smarty->assign('billets',   $derniersBilletsComplets);
$smarty->assign('flux',      $listeFlux);
$smarty->assign('suivant',   count($derniersBilletsComplets) ? -1 : $page + 1);
$smarty->assign('precedant', $page - 1);

$smarty->display('index.html');