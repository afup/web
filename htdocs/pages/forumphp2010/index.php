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

$actualite['titre'] = "Gagnez des livres avec les éditions ENI !";
$actualite['contenu'] = "<p>15 ans de PHP, 10 ans d'existence pour l'AFUP, ca se fête : à cette occasion les éditions ENI, en collaboration avec l'AFUP, vous font <a href=\"http://www.editions-eni.fr/Livres/Offres-promotionnelles/.25_3a6222cf-b921-41f5-886c-c989f77ba994_90ea0075-9afb-45af-a8a8-0701438f66ae_1_0_d9bd8b5e-f324-473f-b1fc-b41b421c950f.html\">gagner des livres</a> !</p>";
$actualite['date'] = "11 octobre 2010";
$actualites[] = $actualite;

$actualite['titre'] = "Vous enseignez l'informatique dans le supérieur ?";
$actualite['contenu'] = "<p>L'AFUP a le plaisir de vous inviter gratuitement au Forum PHP 2010 ! Pour recevoir votre invitation, <a href=\"mailto:communication@afup.org\">contactez-nous dès maintenant par mail</a>. Nous vous donnerons alors la
marche à suivre pour bénéficier de votre invitation. Vous ne pouvez pas être présent lors de ces deux jours ? N'hésitez pas à faire circuler cette invitation : elle est valable pour tout professeur enseignant en informatique dans le supérieur !</p>";
$actualite['date'] = "04 octobre 2010";
$actualites[] = $actualite;

$actualite['titre'] = "Devenez Fan !";
$actualite['contenu'] = "<p>Suivez les préparatifs du Forum PHP en devenant Fan de l'AFUP sur Facebook ! News, programme définitif, et bien plus encore, <a href=\"http://www.facebook.com/pages/AFUP/148661101838283\">à suivre sur notre page Fan</a> !</p>";
$actualite['date'] = "01 octobre 2010";
$actualites[] = $actualite;

$actualite['titre'] = "Projets PHP Open Source";
$actualite['contenu'] = "<p>Vous avez un projet communautaire développé autour de PHP (frameworks, applicatifs, utilitaires, ...) ? Vous souhaitez le présenter à un public averti? L'AFUP vous propose un espace dédié lors du Forum PHP 2010 ! <a href=\"http://www.afup.org/pages/forumphp2010/projets-php-inscription.php\">Inscrivez-vous dès maintenant</a> !</p>";
$actualite['date'] = "01 octobre 2010";
$actualites[] = $actualite;

$actualite['titre'] = "15 ans de PHP, 10 ans d'AFUP : un programme riche pour cette année 2010";
$actualite['contenu'] = "<p><strong>Rasmus Lerdorf</strong>, créateur de PHP, sera l'invité d'honneur de cette édition anniversaire : les 9 et 10 novembre 2010, Cité des Sciences de La Villette.</p>
						<p>En ouvrant un cycle de conférences dédié à des profils fonctionnels, l'Association Française des Utilisateurs de PHP entend intégrer un public plus large, avec la perspective nouvelle d'initier les chefs de projets à cette plateforme de programmation.</p>
						<p>Parmi les thèmes abordés :</p>
						<ul>
							<li><strong>PHP de A à Z</strong> : Débuter en PHP, Réussir un projet avec PHP, Choisir son hébergement</li>
							<li><strong>Les outils basés sur PHP</strong> : Drupal , outils de e-commerce et de business, CRM et ERP</li>
							<li><strong>L'industrialisation de PHP</strong> : Performances, tests, authentification centralisée, frameworks</li>
							<li><strong>Technologies autour de PHP</strong> :  HTML 5, référencement...</li>
						</ul>
						<p>
							<a href=\"http://afup.org/pages/forumphp2010/sessions.php\">=> Les sessions</a><br />
							<a href=\"http://afup.org/pages/forumphp2010/conferenciers.php\">=> Les conférenciers</a><br />
							<a href=\"http://afup.org/pages/forumphp2010/deroulement.php\">=> Le déroulement</a>
						</p>
						<p>Pour vous inscrire, ne perdez pas de temps, <a href=\"http://afup.org/pages/forumphp2010/inscription.php\">réservez votre place au forum PHP</a> !</p>";
$actualite['date'] = "03 septembre 2010";
$actualites[] = $actualite;

$actualite['titre'] = "Fin de l'appel à conférenciers, pré-programme";
$actualite['contenu'] = "<p>Près de <strong>80 propositions de conférences</strong> nous sont parvenues, le choix promet d'être difficile...</p>
						<p>On annonce notamment la venue de <strong>Rasmus Lerdorf</strong>, à l'occasion du 15e anniversaire, mais de nombreux autres acteurs reconnus seront là pour vous concocter un programme de très haute qualité.</p>
						<p>Restez à l'écoute, nous publierons prochainement un pré-programme, pour vous donner un avant-goût de ce Forum 2010 !</p>";
$actualite['date'] = "05 juillet 2010";
$actualites[] = $actualite;

$actualite['titre'] = "Devenez sponsor du Forum PHP 2010";
$actualite['contenu'] = "<p>Vous souhaitez positionner votre entreprise sur PHP, atteindre un public ciblé, bénéficier de retombées presse, rencontrer des acteurs reconnus... devenez l'un des sponsors du Forum PHP 2010 !</p>".
"<p><a href=\"../../templates/forumphp2010/pdf/Forum-PHP-2010-dossier-sponsor.pdf\">Télécharger le dossier Sponsors</a> (PDF - 367 Ko).</p>";
$actualite['date'] = "30 juin 2010";
$actualites[] = $actualite;

$smarty->assign('actualites', $actualites);

$smarty->display('index.html');
