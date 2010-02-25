<?php
require_once '../../include/prepend.inc.php';

header('Content-Type: text/xml; charset=UTF-8');

$feed = array();
$feed['title'] = "planete php fr";
$feed['url'] = 'http://planete-php.fr/';
$feed['link'] = $feed['url'].'rss.php';
$feed['email'] = "planetephpfr@afup.org";
$feed['author'] = "Perrick Penet / AFUP";
$feed['date'] = date(DATE_RSS);

$smarty->assign('feed', $feed);

require_once dirname(__FILE__) . '/../../classes/afup/AFUP_Planete_Billet.php';

$planete_billet = new AFUP_Planete_Billet($bdd);
$derniers_billets_complets = $planete_billet->obtenirDerniersBilletsComplets(0, DATE_RSS, 20);
$smarty->assign('billets', $derniers_billets_complets);

$smarty->display('rss.xml');

?>