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

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Site.php';

$page   = new AFUP_Site_Page($bdd);
$footer = new AFUP_Site_Footer($bdd);

$page->definirRoute(isset($_GET['route']) ? $_GET['route'] : '');

$smarty->assign('header',    $page->header());
$smarty->assign('menu',      $page->menu());
$smarty->assign('content',   $page->content());
$smarty->assign('logos',     $footer->logos());
$smarty->assign('questions', $footer->questions());
$smarty->assign('articles',  $footer->articles());

$smarty->display('index.html');