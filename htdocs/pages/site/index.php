<?php
require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Site.php';

$page = new AFUP_Site_Page($bdd);

$page->definirRoute(isset($_GET['route']) ? $_GET['route'] : '');

$smarty->assign('community', $page->community());
$smarty->assign('header', $page->header());
$smarty->assign('menu', $page->menu());
$smarty->assign('content', $page->content());
$smarty->assign('social', $page->social());
$smarty->assign('footer', $page->footer());

$smarty->display('index.html');