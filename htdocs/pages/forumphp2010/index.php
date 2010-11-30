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

$actualite['titre'] = "Résultat mots-melés";
$actualite['contenu'] = "<p>Participants au Forum PHP, les 71 réponses de la grille de mots mêlés!
A vous qui vous vous usez encore les yeux sur les mots mêlés diffusés par
AlterWay lors du Forum PHP, à vous qui tournez et retournez entre vos mains
la grille, à la recherche de tous les mots cachés en rapport avec PHP, à
vous qui l'avez tellement épluchée que vous avez trouvé plus de réponses
que les concepteurs avaient conscience d'en avoir dissimulées, à vous qui
bloquez sur le 71ème mot après avoir trouvé facilement les 70 premiers...
Nous mettons fin à vos souffrances! Retrouvez ici toutes les réponses à ce
jeu diabolique.
<br /><br />
<a href='http://www.afup.org/templates/forumphp2010/pdf/Jeu-Concours-Forum-PHP.png'>Télécharger la grille</a> (PNG - 206 Ko).</p><br />";
$actualite['date'] = "29 novembre 2010";
$actualites[] = $actualite;



$actualite['titre'] = "Communiqué de presse : L'AFUP propulse le Forum PHP au sommet pour sa 10ème édition";
$actualite['contenu'] = "<p>2010 est l'année de tous les records : espace d'échanges et de mutualisation des compétences, le Forum PHP, via le soutien sans faille d'une équipe d'experts passionnés, a réuni les 9 et 10 novembre derniers plus de 500 visiteurs par jour, soit 35% de plus qu'en 2009.
<br /><br />
<a href='http://www.afup.org/templates/forumphp2010/pdf/bilan_forum_php_2010.pdf'>Télécharger le Communiqué de presse</a> (PDF - 85 Ko).</p><br />";
$actualite['date'] = "18 novembre 2010";
$actualites[] = $actualite;

$actualite['titre'] = "Le Forum PHP 2010 est COMPLET !";
$actualite['contenu'] = "<p>Encore une fois, le Forum PHP clôture ses inscriptions quelques jours avant l'évènement! Vous serez plus de 450 à nous rejoindre pour cette édition exceptionnelle. Rendez-vous mardi 9 et mercredi 10 novembre pour célébrer avec nous les 15 ans du PHP en compagnie des meilleurs experts mondiaux ! Et merci à vous !
</p>";
$actualite['date'] = "4 novembre 2010";
$actualites[] = $actualite;

$actualite['titre'] = "Weka complète notre thématique sur les performances du PHP!";
$actualite['contenu'] = "<p>Cette année, l'AFUP souhaite notamment mettre l'accent sur l'optimisation des performances des sites. Qui de mieux pour l'illustrer que Weka, leader du marché français du social gaming, accueillant tous les jours plus de 600
000 visiteurs uniques et délivrant plus de 30 millions de pages vues par jour sur des applications sociales et interactives? Comment faire face à une telle problématique de très forte volumétrie? Weka nous fera bénéficier de son expérience lors de la conférence 'Jeux sociaux & Cloud Computing : une histoire de scalabilité'.</p>";
$actualite['date'] = "3 novembre 2010";
$actualites[] = $actualite;


$actualite['titre'] = "Roy Rubin, fondateur de Magento, invité de dernière minute au Forum PHP
2010!";
$actualite['contenu'] = "<p>Roy Rubin nous fera l'honneur de sa présence lors de la conférence 'Magento, un framework du E-commerce' menée par Hubert Desmarest et Guillaume Babik.  Magento, ou la meilleure solution de ecommerce open
source? Tous les deux, accompagnés de leur invité de marque, nous en parleront à travers l'exemple du site SmartBox.fr, développé sous Magento
en fonction des besoins propres aux métiers de SmartBox.</p>";
$actualite['date'] = "3 novembre 2010";
$actualites[] = $actualite;

$actualite['titre'] = "Forum PHP 2010 : Zeev Suraski répond présent.";
$actualite['contenu'] = "<p>Zend Technologies, partenaire du Forum PHP 2010, nous propose une conférence intitulée <a href=\"http://afup.org/pages/forumphp2010/sessions.php#512\">« Le paradoxe des performances PHP »</a>, animée par Zeev Suraski (co-fondateur de Zend Technologies).
Ces dernières années, de nombreuses fonctions ont été ajoutées à PHP 5, mais paradoxalement, il est également devenu significativement plus rapide avec chaque sortie majeure.
Cette conférence décrira les composants de PHP, la machine virtuelle de PHP et les plus importants changements et optimisations de PHP5 liés à la performance.</p>";
$actualite['date'] = "27 octobre 2010";
$actualites[] = $actualite;

$actualite['titre'] = "SkySQL en exclusivité pour le Forum PHP 2010 !";
$actualite['contenu'] = "<p>Michael « Monty » Widenius – Monty Program Ab- et Kaj Arnö – SkySQL Ab- nous font l'honneur d'animer ensemble la conférence de clôture du Forum PHP 2010, ayant pour thème <a href=\"http://afup.org/pages/forumphp2010/sessions.php#511\">« Etat de l'art de l'écosystème MySQL »</a>.
Au programme, le futur de MySQL et la présentation de leur alternative à Oracle, SkySQL. Que cela signifie-t-il pour l'écosystème des partenaires, développeurs, clients, utilisateurs professionnels et la communauté des contributeurs de MySQL ?
Que peut-on attendre du futur de MySQL : forks, correction des bugs, support commercial et feuille de route ?</p>";
$actualite['date'] = "26 octobre 2010";
$actualites[] = $actualite;

$actualite['titre'] = "Communiqué de presse : l'AFUP reçoit en exclusivité SkySQL Ab et Monty Program Ab";
$actualite['contenu'] = "<p>L'AFUP fédère l'ensemble des communautés PHP et reçoit en exclusivité SkySQL Ab et Monty Program Ab.
<br />
Une édition exceptionnelle pour fêter les 15 ans de PHP.<br /><br />
<a href='http://www.afup.org/templates/forumphp2010/pdf/L-AFUP recoit SkySQL et Monty Program.pdf'>Télécharger le Communiqué de presse</a> (PDF - 109 Ko).</p><br />";
$actualite['date'] = "22 octobre 2010";
$actualites[] = $actualite;

$actualite['titre'] = "Le Forum met en avant les projets Open Source";
$actualite['contenu'] = "<p>Après l'appel à candidature lancé il y a quelques semaines, la sélection
est tombée ! Voici les projets Open Source développés en PHP et les communautés qui seront
représentés lors du Forum PHP 2010, dans un espace qui leur sera
entièrement dédié : Hoa, RBS Change, CakePHP-fr, Fine FS, Jelix, Magix CMS et Drupal.</p>";
$actualite['date'] = "19 octobre 2010";
$actualites[] = $actualite;

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


$actualite['titre'] = "Communiqué de presse : Le rendez-vous incontournable de la scène PHP fête les 15 ans de PHP !";
$actualite['contenu'] = "<p>Le communiqué de presse est désormais disponible : n'hésitez pas à le faire circuler autour de vous</p>
            <p><a href=\"../../templates/forumphp2010/pdf/CP-ForumPHP_2010.pdf\">Télécharger le Communiqué de presse</a> (PDF - 220 Ko).</p><br />";
$actualite['date'] = "09 septembre 2010";
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

$smarty->assign('actualites', $actualites);

$smarty->display('index.html');
