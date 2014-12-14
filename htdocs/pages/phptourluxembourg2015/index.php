<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Site.php';
require_once dirname(__FILE__) . '/_config.inc.php';

setlocale (LC_TIME, 'fr_FR.utf8','fra');
$smarty->caching = false;

$aujourdhui = time();
$date_forum = $config_forum['date_fin'];
$jours_avant_forum = $date_forum - $aujourdhui;

if ($jours_avant_forum < 0) {
	$alerte_avant_forum = "<fieldset>";
	$alerte_avant_forum .= "<legend>&nbsp;Inscriptions fermées !&nbsp;</legend>";
	$alerte_avant_forum .= "<h3>Les inscriptions sont désormais fermées.<br /> Rendez-vous l'année prochaine.</h3>";
	$alerte_avant_forum .= "</fieldset>";
} else {
	$alerte_avant_forum = "";
}
$smarty->assign('alerte_avant_forum', $alerte_avant_forum);

$articles = new AFUP_Site_Articles($bdd);
$actualites = $articles->chargerArticlesDeRubrique(78); // 78 = rubrique PHP Tour Luxembourg 2015
$smarty->assign('actualites', $actualites);

$smarty->display('index.html');
