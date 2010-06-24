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

$info_importante['titre'] = "Forum PHP 2010 <br> les 09 et 10 novembre 2010";
$info_importante['contenu'] =
"<p>Le <strong>Forum PHP 2010</strong> est officiellement annoncé pour les <strong>09 et 10 novembre 2010</strong> à la <strong>Cité des Sciences de Paris la Villette</strong> (France).</p>".
"<p>C'est le seul événement professionnel en France consacré à la plate-forme PHP et aux technologies Web.
Il rassemble les différents acteurs de la profession et valorise le dynamisme français en terme de technologies
de pointe sur Internet.</p>".
"<p>Il sera placé sous le signe des <strong>15 ans de PHP</strong> et des <strong>10 ans de l'Afup</strong>.</p>".
"<p>A cette occasion, l'Afup organise un Forum plus ambitieux que jamais, prévoyant de multiples conférences, des ateliers et débats, des invités renommés, mais aussi des espaces d'intervention et d'exposition plus nombreux !</p>".
"<p>Venez à la rencontre des professionnels du monde PHP : développeurs, décideurs, presse...</p>";
$infos_importantes[] = $info_importante;

$smarty->assign('infos_importantes', $infos_importantes);

$actualites = array();

$actualite['titre'] = "Prolongation des tarifs prévente !";
$actualite['contenu'] = "<p><strong>Jusqu'au 15 juillet 2010, bénéficiez de 20 € de réduction sur le pass 2 jours</strong>.<br />
La réduction s'applique aussi aux tarifs étudiants/demandeurs d'emploi et membres Afup, profitez-en !</p>".
"<p><a href=\"inscription.php\">Inscription au tarif prévente pour le Forum PHP 2010</a>.</p>";
$actualite['date'] = "24 juin 2010";
$actualites[] = $actualite;

$actualite['titre'] = "Appel à conférenciers !";
$actualite['contenu'] = "<p>Experts francophones de PHP, l'AFUP vous invite à
							<a href=\"appel-a-conferenciers.php\">envoyer vos propositions de sessions</a>
							pour l'édition 2010 de son Forum PHP à Paris.</p>";
$actualite['date'] = "02 juin 2010";
$actualites[] = $actualite;

$smarty->assign('actualites', $actualites);

$smarty->display('index.html');
