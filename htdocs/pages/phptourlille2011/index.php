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

$actualites = array(
	array(
		'date' => "28 juin 2011",
		'titre' => "Les inscriptions sont ouvertes !",
		'contenu' => "<p>Le Forum PHP 2010 annonçait complet : nous espérons que le PHP Tour Lille 2011 suivra la même voie ! Pour être sûr d'assister à ce nouveau
			cycle de conférences itinérant de l'AFUP, réservez dès maintenant votre place et profitez des tarifs préférentiels en pré-vente. Et vous aussi, vous pourrez dire <cite>&laquo;J'y étais !&raquo;</cite></p>",
	),
	array(
		'date' => "23 juin 2011",
		'titre' => "Demandez le programme !",
		'contenu' => "<p>Le choix est ardu, les débats sont animés, pour satisfaire décideurs comme techniciens, amateurs comme développeurs expérimentés... Les premiers conférenciers pour le PHP Tour Lille 2011 sont enfin confirmés !</p>
			<p>Nous pouvons d'ores et déjà annoncer les retours d'expériences vécues au sein
			de <a href=\"http://afup.org/pages/phptourlille2011/sessions.php#597\">Mediapart</a>,
			<a href=\"http://afup.org/pages/phptourlille2011/sessions.php#526\">Conforama</a>
			ou <a href=\"http://afup.org/pages/phptourlille2011/sessions.php#556\">20 Minutes</a>,
			des sujets pointus et innovants tels que
			<a href=\"http://afup.org/pages/phptourlille2011/sessions.php#567\">&laquo;les services asynchrones et multilangages avec Mongrel2 et ZeroMQ&raquo;</a>,
			<a href=\"http://afup.org/pages/phptourlille2011/sessions.php#596\">&laquo;le traitement XML de pointe avec PHP et XQuery&raquo;</a>
			et <a href=\"http://afup.org/pages/phptourlille2011/sessions.php#601\">&laquo;concevoir de puissantes applications VoIP grâce à PHP&raquo;</a>,
			et des interventions des Community Managers
			de <a href=\"http://afup.org/pages/phptourlille2011/sessions.php#566\">SugarCRM</a>
			et <a href=\"http://afup.org/pages/phptourlille2011/sessions.php#572\">eZ Systems</a>.</p>
			<p>Découvrez en détail notre programmation dans notre rubrique
			<a href=\"http://afup.org/pages/phptourlille2011/deroulement.php\"><strong>Programme</strong></a>.
			Et ce n'est que le début : affaire à suivre !</p>",
	),
	array(
		'date' => "30 avril 2011",
		'titre' => "L'appel à conférenciers est lancé !",
		'contenu' => "<img align=\"left\" src=\"../../templates/forumphp2010/images/forumafup2009.jpg\" alt=\"PHP Tour\" />
			<p>Expert PHP, devenez conférencier lors du PHP Tour Lille 2011, le nouvel événement itinérant de l'AFUP ! Vous êtes expert dans le domaine du commerce en ligne, vous avez des connaissances pointues dans le domaine de l'intégration d'application hétérogènes dans les systèmes d'informations, vous pouvez nous faire bénéficier d'un retour d'expérience et de vos outils et astuces pour résister aux montées en charge: venez partager vos connaissances, en solo ou en groupe ! </p>
			<p>Rendez-vous dans la rubrique « Appel à conférenciers » et remplissez le formulaire avant le 31 mai 2011, minuit. </p>",
	),
	array(
		'date' => "18 avril 2011",
		'titre' => "PHP Tour Lille 2011<br>les 24 et 25 novembre 2011",
		'contenu' => "<p>Le <strong>PHP Tour Lille 2011</strong> est officiellement annoncé pour les <strong>24 et 25 novembre 2011</strong> à <strong>Euratechnologies / Lille</strong> (France).</p>
			<p>Nouvel événement annuel et itinérant lancé par l'AFUP, <strong>le PHP Tour se penche sur les problématiques et thématiques propres à la région d'accueil</strong>. Experts nationaux et internationaux animeront conférences, retours d'expérience et ateliers en lien avec le tissu économique local, pour aider la communauté PHP à parvenir au top de ses capacités ! </p>
			<p>Le PHP Tour Lille 2011 mettra notamment l'accent sur le <strong>commerce en ligne</strong>, <strong>l'intégration d'application hétérogènes au sein des systèmes d'informations</strong> et <strong>l'échelle du web</strong>.</p>
			<p>Évènement itinérant, mais ambition nationale: l'AFUP se donne pour mission de faire du PHP Tour un rendez-vous annuel incontournable pour les professionnels du monde PHP. Developpeurs, décideurs, journalistes, venez découvrir le PHP Tour Lille 2011 !</p>",
	),
);
$smarty->assign('actualites', $actualites);

$smarty->display('index.html');
