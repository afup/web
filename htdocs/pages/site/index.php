<?php
/**
 * Fichier principal site 'AFUP'
 * 
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 * 
 * @category AFUP
 * @package  AFUP
 * @group    Pages
 */

// 0. initialisation (bootstrap) de l'application

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

// 1. chargement des classes nécessaires

require_once 'Afup/AFUP_Site.php';

// 2. récupération et filtrage des données

$page   = new AFUP_Site_Page($bdd);
$footer = new AFUP_Site_Footer($bdd);

$page->definirRoute(isset($_GET['route']) ? $_GET['route'] : '');

// 3. assignations des variables du template

$smarty->assign('header',    $page->header());
$smarty->assign('menu',      $page->menu());
$smarty->assign('content',   $page->content());
$smarty->assign('logos',     $footer->logos());
$smarty->assign('questions', $footer->questions());
$smarty->assign('articles',  $footer->articles());

// 4. affichage de la page en utilisant le modèle spécifié

$smarty->display('index.html');