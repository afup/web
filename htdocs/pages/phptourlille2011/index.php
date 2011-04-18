<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';
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

$info_importante['titre'] = "PHP Tour Lille 2011<br>les 24 et 25 novembre 2011";
$info_importante['contenu'] = "<p>Le <strong>PHP Tour Lille 2011</strong> est officiellement annoncé pour les <strong>24 et 25 novembre 2011</strong> à <strong>Euratechnologies / Lille</strong> (France).</p>";
$infos_importantes[] = $info_importante;

$smarty->assign('infos_importantes', $infos_importantes);

$actualites = array();

$actualite['titre'] = "15 ans de PHP, 10 ans d'AFUP : un programme riche pour cette année 2010";
$actualite['contenu'] = "<p></p>";
$actualite['date'] = "03 septembre 2010";
$actualites[] = $actualite;

$smarty->assign('actualites', $actualites);

$smarty->display('index.html');
