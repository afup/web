<?php
use Afup\Site\Corporate\Lille\_Page_Lille;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';


$page = new _Page_Lille($bdd);

$page->definirRoute(isset($_GET['route']) ? $_GET['route'] : 'lille/74');

$smarty->assign('header', $page->header());
$smarty->assign('menu', $page->menu());
$smarty->assign('content', $page->content());
$smarty->assign('footer', $page->footer());

$smarty->display('index.html');