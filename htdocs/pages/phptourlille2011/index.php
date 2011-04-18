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

//$actualite['titre'] = "15 ans de PHP, 10 ans d'AFUP : un programme riche pour cette année 2010";
//$actualite['contenu'] = "<p><strong>Rasmus Lerdorf</strong>, créateur de PHP, sera l'invité d'honneur de cette édition anniversaire : les 9 et 10 novembre 2010, Cité des Sciences de La Villette.</p>
//						<p>En ouvrant un cycle de conférences dédié à des profils fonctionnels, l'Association Française des Utilisateurs de PHP entend intégrer un public plus large, avec la perspective nouvelle d'initier les chefs de projets à cette plateforme de programmation.</p>
//						<p>Parmi les thèmes abordés :</p>
//						<ul>
//							<li><strong>PHP de A à Z</strong> : Débuter en PHP, Réussir un projet avec PHP, Choisir son hébergement</li>
//							<li><strong>Les outils basés sur PHP</strong> : Drupal , outils de e-commerce et de business, CRM et ERP</li>
//							<li><strong>L'industrialisation de PHP</strong> : Performances, tests, authentification centralisée, frameworks</li>
//							<li><strong>Technologies autour de PHP</strong> :  HTML 5, référencement...</li>
//						</ul>
//						<p>
//							<a href=\"http://afup.org/pages/forumphp2010/sessions.php\">=> Les sessions</a><br />
//							<a href=\"http://afup.org/pages/forumphp2010/conferenciers.php\">=> Les conférenciers</a><br />
//							<a href=\"http://afup.org/pages/forumphp2010/deroulement.php\">=> Le déroulement</a>
//						</p>
//						<p>Pour vous inscrire, ne perdez pas de temps, <a href=\"http://afup.org/pages/forumphp2010/inscription.php\">réservez votre place au forum PHP</a> !</p>";
//$actualite['date'] = "03 septembre 2010";
//$actualites[] = $actualite;

$smarty->assign('actualites', $actualites);

$smarty->display('index.html');
