<?php
require_once '../../include/prepend.inc.php';

require_once dirname(__FILE__) . '/../../classes/afup/AFUP_Site.php';

$route = "";
if (isset($_GET['route'])) {
	$route = $_GET['route'];
}

$page = new AFUP_Site_Page($bdd);
$page->definirRoute($route);

$smarty->assign('header', $page->header());
$smarty->assign('menu', $page->menu());
$smarty->assign('content', $page->content());

$footer = new AFUP_Site_Footer($bdd);
$smarty->assign('logos', $footer->logos());
$smarty->assign('questions', $footer->questions());
$smarty->assign('articles', $footer->articles());

$smarty->display('index.html');
