<?php
require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Site_Lille.php';

$page = new AFUP_Site_Page_Lille($bdd);

$page->definirRoute(isset($_GET['route']) ? $_GET['route'] : 'lille/73');

$smarty->assign('header', $page->header());
$smarty->assign('menu', $page->menu());
$smarty->assign('content', $page->content());
$smarty->assign('footer', $page->footer());

$smarty->display('index.html');