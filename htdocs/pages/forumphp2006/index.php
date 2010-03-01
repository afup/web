<?php
require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
$smarty->caching = false;

$aujourdhui = time();
$date_forum = mktime(0,0,0,11,9,2006);
$jours_avant_forum = ceil(($date_forum - $aujourdhui) / 86400);

if ($jours_avant_forum > 0) {
	$alerte_avant_forum = "<fieldset>";
	$alerte_avant_forum .= "<legend>&nbsp;Inscriptions fermées !&nbsp;</legend>";
	$alerte_avant_forum .= "<h3>Les inscriptions sont désormais fermées.<br /> Rendez-vous l'année prochaine.</h3>";
	$alerte_avant_forum .= "</fieldset>";
} else {
	$alerte_avant_forum = "";
}
$smarty->assign('alerte_avant_forum', $alerte_avant_forum);

$smarty->display('index.html');
