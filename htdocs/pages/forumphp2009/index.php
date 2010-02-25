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

$infos_importantes = array();

$info_importante['titre'] = "Les projets PHP au Forum";
$info_importante['contenu'] = "A l'occasion de ce forum PHP, l'AFUP va mettre en avant les <a href='projets-php.php'>projets Open Source français développés autour de PHP </a>
(frameworks, applicatifs, utilitaires, ...).</p>
<p> Nous allons donc mettre à disposition des personnes qui portent ces projets un espace salle  à proximité des amphithéâtres et salles où se dérouleront les conférences. </p>
<p>Vous participez à un projet PHP Opensource et vous souhaitez être présent contactez-nous à <a href='mailto:forum-projet@afup.org'>forum-projet@afup.org</a></p>";
$infos_importantes[] = $info_importante;



$info_importante['titre'] = "Participation de l’association LeMug.fr (MySQL User Group).";
$info_importante['contenu'] = "<p>Le Forum PHP 2009 accueillera comme partenaire aux cotés de l’AFUP (Association Française des Utilisateurs PHP) l’association LeMug.fr (MySQL User Group).
<br>
Plusieurs conférences sont prévues sur la base de données MySQL avec des intervenants de renom.</p>";
$infos_importantes[] = $info_importante;



$smarty->assign('infos_importantes', $infos_importantes);

$actualites = array();


$actualite['titre'] = "Les sessions sont en lignes";
$actualite['contenu'] = "<p>Cette 9ème édition sera axée sur le couple PHP/MySQL, avec 8 conférences dédiées.
LEMUG.fr, l'association francophone des utilisateurs de MySQL et partenaire de l'événement
animera 3 conférences</p>";
$actualite['date'] = "15 septembre 2009";
//$actualites[] = $actualite;

$actualites[] = $actualite;

$smarty->assign('actualites', $actualites);

$smarty->display('index.html');
