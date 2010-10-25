<?php

/**
    TODO en plus de cette config :
    Créer la ligne afup_forum et mettre l'id de la ligne dans $config_forum['id']
    Il faut aussi verifier le contenu des template (rechercher la date de l'année précedente )
    Modifer le pdf du formulaire papier dans "/site/templates/forumphpXXXX/inscription-forum.pdf"
       à partir du doc dans "/sources/doc/inscription au forum.odt"
    Modifer le pdf du dossier sponsors dans "/site/templates/forumphp2009/pdf/Forum-PHP-2009-dossier-sponsor.pdf"
       à partir du doc dans "/sources/forum/2009/Forum-PHP-2009-dossier-sponsor.odt"

 */

// Param de configuration sur site du Forum PHP

define('AFUP_CHEMIN_SOURCE', realpath(dirname(__FILE__) . '/../../classes/afup/')); // prod
// define('AFUP_CHEMIN_SOURCE', realpath(dirname(__FILE__) . '/../../../sources/Afup/')); // trunk

$config_forum['id'] = 5;
/*
 * 'PHPFRANCE','POLENORD','DIGIPORT', 'WAMPSERVER',
                  'ALTERWAY','ADOBE','SENSIO','SENSIOLABS','4D',
                  'HSC','MICROSOFT', 'CODEUR',
                  'AFUP','CONFERENCIER', 'TWITTER',
                  'POLLEN', 'PIWAM','PIC','FREEDOM', 'HAVEFNUBB','PHPTV','PRESTASHOP'
 */
$coupons = array('INTERNIM','ADOBE','ZEND','ELAO','DEVELOPPEZ','MICROSOFT','WEKA',
				'VACONSULTING','CLEVERAGE','ENI','ALTERWAY','EMERCHANT','LINAGORA',
				'OXALIDE','BUSINESSDECISION','EYROLLES','PROGRAMMEZ','PHPSOLUTIONS',
				'RBSCHANGE','JELIX','CAKEPHPFR','HOA','DRUPAL','MAGIXCMS','FINEFS','SOLUTIONSLOGICIELS',
				'SYMFONY'
                );

$config_forum['project_ids'] = array();
$config_forum['coupons'] = array_merge($coupons,array_map("strtolower",$coupons));
$config_forum['annee'] = 2010;
$config_forum['date_fin_appel_projet'] = mktime(23, 59, 59, 10, 25, $config_forum['annee']);
$config_forum['date_fin_appel_conferencier'] = mktime(23, 59, 59, 6, 30, $config_forum['annee']);
//$config_forum['date_fin_prevente'] = $config_forum['date_fin_appel_conferencier'] ;
$config_forum['date_fin_prevente'] = mktime(0, 0, 0, 07, 15, $config_forum['annee']);
$config_forum['date_debut'] = mktime(0, 0, 0, 11, 09, $config_forum['annee']);
$config_forum['date_fin'] = mktime(0, 0, 0, 11, 10, $config_forum['annee']);
$smarty->assign('forum_annee', $config_forum['annee'] );


$sponsors_platinium=array(
	array('nom'   => 'Adobe',
          'site'  => 'http://www.adobe.com/fr/',
          'logo'  => 'logo-adobe-sponsor.png',
          'texte' => "<p>Adobe révolutionne l'échange d'idées et d'informations. Depuis vingt cinq
						ans, les technologies et les logiciels réputés de cet éditeur redéfinissent
						la communication des entreprises, du marché des loisirs et des particuliers
						en établissant de nouveaux standards de production et de diffusion de
						contenus véritablement fascinants - partout et à tout moment. À travers des
						images d'une remarquable richesse visuelle pour l'impression, la vidéo et
						le cinéma ou du contenu numérique dynamique adapté à une multitude de sup-
						ports, l'impact des solutions est évident et peut être ressenti par
						quiconque crée, visualise et manipule des informations. Fort de sa
						réputation d'excellence et d'une gamme consti- tuée de nombreux produits
						parmi les plus appréciés et les plus connus du marché, Adobe est l'un des
						principaux éditeurs de logiciels mondiaux les plus diversifiés.</p>",
	),
	array('nom'   => 'Zend',
          'site'  => 'http://www.zend.com/fr/',
          'logo'  => 'logo_zend.png',
          'texte' => '<p>Zend Technologies, Inc., la PHP Company, est le leader des produits et services de développement,
          				déploiement et gestion d\'applications web critiques. PHP est utilisé par plus de 20 millions
          				de site Internet et est rapidement devenu le langage le plus répandu pour développer
          				des applications web stratégiques. Mondialement déployée dans plus de 25000 entreprises,
          				la gamme de produits Zend apporte une solution complète durant tout le cycle de vie
          				d\'une application PHP. Le siège de Zend est situé à Cupertino en Californie.</p>',
	),
);
$smarty->assign('sponsors_platinium', $sponsors_platinium);

$sponsors_gold=array(
	array('nom'   => 'Microsoft',
          'site'  => 'http://www.microsoft.com/fr/fr/',
          'logo'  => 'logo_microsoft.png',
          'texte' => "<p>Depuis plusieurs années, Microsoft a multiplié les actions et engagements
concrets en faveur de l'ouverture et tout particulièrement à destination
des environnements Open Source. De la livraison de code au noyau Linux en
juillet 2009 en GPL, à la coopération permanente avec des sociétés et
communautés Open Source, son engagement est maintenant reconnu et respecté.
En ce qui concerne spécifiquement les actions les plus marquantes avec
l'environnement PHP, Microsoft a développé en 2009 un accélérateur,
WinCache, afin d'optimiser les performances sur Windows Server et a inclut
le support de la technologie PHP à son offre de Cloud Computing, Windows
Azure. Cette dernière permet ainsi aux développeurs PHP de profiter de la
souplesse du Cloud, dès maintenant.</p>",
	),
	array('nom'   => 'Weka entertainment',
          'site'  => 'http://www.weka-entertainment.com/',
          'logo'  => 'logo-weka-sponsor.png',
          'texte' => "<p>Weka Entertainment est un éditeur français indépendant de jeux en ligne et
						d'applications sociales destinés au grand public. Accessibles par tous sur
						les réseaux sociaux tels que Facebook, ces jeux sont gratuits mais, pour
						les joueurs les plus chevronnés, intègrent des fonctionnalités premium et des biens virtuels qu'il est possible d'acheter par des micro-transactions. En l'espace de 18 mois, Weka Entertainment a développé un savoir-faire unique en matière de technologies, de systèmes d'information, de marketing,
						de gestion de communauté et de monétisation ; savoir-faire qu'elle exploite aujourd'hui sur toute sa gamme de jeux.</p>
						<p>Avec plus de 10 millions d’utilisateurs inscrits, Weka Entertainment est le leader du marché français du social gaming. Weka Entertainment siège à Paris dans le 2ème arrondissement et compte plus de 50 collaborateurs.</p>",
	),
);
$smarty->assign('sponsors_gold', $sponsors_gold);


$sponsors_silver=array(
	array('nom'   => 'Alter Way',
          'site'  => 'http://www.alterway.fr/',
          'logo'  => 'logo-alter-way-sponsor.png',
          'texte' => '<p>Alter Way, opérateur de services open source, accompagne les grands comptes, administrations, collectivités locales et Pme/Pmi dans le développement et l\'usage de leur système d\'information. Alter Way propose une offre industrielle à 360&deg;, structurée autour de cinq activités clés :</p>
                      <ul>
                      <li>Conseil IT (Alter Way Consulting),</li>
                      <li>Communication, studio graphique et e-marketing (Reciprok),</li>
                      <li>Intégration, développement et infogérance (Alter Way Solutions),</li>
                      <li>Hébergement à valeur ajoutée (Alter Way Hosting),</li>
                      <li>Formation (Alter Way Formation)</li></ul>
                      <p>Accordant une place essentielle à sa contribution et à son implication dans l\'écosystème Open Source, Alter Way se caractérise par le niveau élevé d\'expertise de ses consultants, reconnus par la communauté. La société se distingue également par un investissement permanent en matière d\'innovation, la plaçant ainsi à la pointe des plus récentes avancées technologiques.</p>
                      <p>Alter Way fut la première entreprise à fédérer les acteurs historiques de l\'Open Source autour d\'un projet d\'industrialisation du marché. Elle compte aujourd\'hui une centaine de collaborateurs. En 2009, elle a réalisé une croissance de 10% avec un chiffre d\'affaires de 9 M€.</p>',
	),
    array('nom'   => 'E-Merchant',
          'site'  => 'http://www.e-merchant.com/',
          'logo'  => 'logo-e-merchant-sponsor.png',
          'texte' => "<p>Société du groupe Pixmania, E-merchant maîtrise et coordonne tous les métiers du e-commerce à votre demande : conception, hébergement, exploitation de votre activité.</p>
						<p>Profitez de nos 10 ans de pratique et de réussite comme acteur majeur du e-commerce pour servir votre croissance.</p>",
	),
    array('nom'   => 'Linagora',
          'site'  => 'http://www.linagora.com/',
          'logo'  => 'logo-linagora-sponsor.png',
          'texte' => "<p>LINAGORA, société spécialisée en Logiciel <strong>Open Source, est le leader français de ce marché</strong>, avec plus de 160 personnes et une présence <a href=\"http://www.linagora.com/-Nos-agences-\">en France (Paris, Toulouse, Lyon et Marseille), en Belgique (Bruxelles) et aux États-Unis (San Francisco)</a>.</p>
						<p><strong>LINAGORA édite ses <a href=\"http://www.linagora.com/-PRODUITS-\">propres logiciels Open Source</a></strong> et propose une <strong>gamme de <a href=\"http://www.linagora.com/-SERVICES-\">services professionnels</a></strong> pour réussir les grands projets du Libre.</p>
						<p>Plus d'informations : <a href=\"www.linagora.com\">www.linagora.com</a></p>",
	),
	array('nom'   => 'Oxalide',
          'site'  => 'http://www.oxalide.com/',
          'logo'  => 'logo-oxalide-sponsor.png',
          'texte' => "<p><strong>Oxalide</strong>, hébergeur spécialisé dans les technologies open-source, conçoit des infrastructure sur-mesure qui respectent les contraintes de vos projets PHP.</p>
						<p>Déléguez la gestion de vos serveurs grâce à nos services d'infogérance et assurez l'évolutivité et la qualité de service grâce à notre conseil et notre expertise.</p>
						<p>Oxalide propulse de nombreux sites PHP : lexpress.fr, lanvin.com, la ligue nationale de Rugby, etc.</p>",
	),
);
$smarty->assign('sponsors_silver', $sponsors_silver);

$sponsors_bronze=array(
	array('nom'   => 'Business & Decisions',
          'site'  => 'http://www.fr.businessdecision.com/',
          'logo'  => 'logo-business-decision-sponsor.png',
          'texte' => "<p>Business & Decision est consultant et intégrateur de systèmes international (CIS). Leader de la Business Intelligence (BI) et du CRM, acteur majeur de l'e-Business, de l'Enterprise Information Management (EIM), des Enterprise Solutions ainsi que du Management Consulting, le Groupe contribue à la réussite des projets à forte valeur ajoutée des entreprises. Il est reconnu pour son expertise fonctionnelle et technologique par les plus grands éditeurs de logiciels du marché avec lesquels il a noué des partenariats.</p>
						<p>Présent dans 18 pays, Business & Decision emploie actuellement plus de 2 500 personnes en France et dans le Monde.</p>",
	),
	array('nom'   => 'Clever Age',
          'site'  => 'http://www.clever-age.com/',
          'logo'  => 'logo-clever-age-sponsor.png',
          'texte' => "<p>Clever Age se positionne sur l'ensemble de la chaine de production
Web depuis près de 10 ans. Cette couverture 100% digitale, ainsi que
les références notables du groupe, en font un prestataire majeur du
marché francophone. Nous présence se répartit sur 4 agences : Paris,
Lyon, Bordeaux et Nantes (dans l'ordre de lancement).</p>
          				<p>Nous privilégions un usage pragmatique des technologies du Web,
appliquant les bonnes pratiques techniques et méthodologiques en
évolution perpétuelle, sur les standards, l'ergonomie et
l'accessibilité, la performance, la capitalisation, etc. Nous
partageons ces convictions avec l'association Forum PHP et sommes
fiers d'en être partenaire cette année, pour l'organisation de
l'événement Forum PHP, au cours duquel nous serons heureux d'échanger
avec nos clients et confrères.</p>",
	),
	array('nom'   => 'Elao',
          'site'  => 'http://www.elao.com/',
          'logo'  => 'logo-elao-sponsor.png',
          'texte' => "<p>Fondée en 2005 par Xavier Gorse, ELAO est une agence Web implantée à la fois en Région Parisienne et à Lyon. Spécialisée dans le conseil et l'accompagnement de projets d'envergure, ELAO est experte dans le framework Symfony. Ses prestations couvrent également l'hébergement de sites, la formation et le développement d'applications métiers en environnements PHP. Dans le cadre de ses activités, ELAO privilégie les technologies Open Source ; soucieuse de répondre aux attentes suscitées par les nouvelles technologies, elle propose également le développement d'applications iPhone et iPad.</p>
          <p>Agence Web à dimension résolument humaine, ELAO est particulièrement attachée à la qualité de la relation clients. Sa structure lui permet de faire preuve de toute la souplesse et la réactivité nécessaires afin de mettre en &oelig;uvre les meilleures technologies actuelles au service du client.</p>",
	),
	array('nom'   => 'Elixis',
          'site'  => 'http://www.elixis.fr/',
          'logo'  => 'logo-elixis-sponsor.png',
          'texte' => "<p>Elixis est un groupe Internet créé en 2005. Ses activités s'étendent du marketing à la performance à l'email marketing, en passant par l'édition de sites Internet.</p>
          				<p>Elixis est en perpétuelle recherche de nouveaux talents pour compléter ses équipes.</p>",
	),
	array('nom'   => 'VA Consulting',
          'site'  => '',
          'logo'  => 'logo-va-consulting-sponsor.png',
          'texte' => "<p>VA Consulting est une société de conseil et de service hyper-spécialisée sur l'écosystème PHP. L'offre de service est conçue pour augmenter siginificativement la productivité et la réactivité des équipes de développement, avec comme objectif final l'amélioration de la qualité et la garantie de la pérénnité des projets. Audits de compétences et de code, conseil et implémentation d'environnements de développement et d'architectures d'applications, formations, coaching... toutes les prestations de VA Consulting s'appuient sur les meilleures pratiques pour servir l'efficacité et le succès de ses clients.</p>",
	),
);
$smarty->assign('sponsors_bronze', $sponsors_bronze);


$partenaires=array(
    array('nom'   => 'Developpez.com',
          'site'  => 'http://www.developpez.com/',
          'logo'  => 'logo_dvp-afup.gif',
          'texte' => 'Le club <a href="http://www.developpez.com">www.developpez.com</a> met à disposition gratuitement tous les
                      services utiles aux décideurs et professionnels en informatique :
                      newsletter, magazine, actualités, cours, tutoriels, articles, FAQ\'s,
                      tests, comparatifs, débats, sondages, outils, sources, composants et
                      exemples de codes, les BLOGs, et l\'hébergement gratuit de sites sur
                      l\'informatique. <a href="http://www.developpez.com">www.developpez.com</a> est la communauté en langue
                      française qui concentre le plus d\'informaticiens professionnel!'),
    array('nom'   => 'Eyrolles',
          'site'  => 'http://www.editions-eyrolles.com/',
          'logo'  => 'logo_eyrolles.gif',
          'texte' => 'Les Editions Eyrolles ont placé PHP au coeur de leur offre Développeurs,
                  de l\'initiation (<a href="http://www.editions-eyrolles.com/Livre/9782212114072/php-5">
                  manuels avec cours et exercices</a>,
                  <a href="http://www.editions-eyrolles.com/Livre/9782212116786/php-mysql-et-javascript">
                  apprentissage par la pratique</a>) à l\'exploitation professionnelle
                  (livres de référence dont <a href="http://www.editions-eyrolles.com/Livre/9782212123692/php-5-avance">PHP 5 avancé</a>,
                  études de cas détaillées dont une sur <a href="http://www.editions-eyrolles.com/Livre/9782212112344/php-5">PHP</a>).
                  Au-delà de la maîtrise de PHP, chaque ouvrage offre un véritable savoir-faire métier au développeur.<br/>
                  Suivez les nouveautés Eyrolles en vous abonnant au fil RSS
                  <a href="http://www.editions-eyrolles.com/rss.php?q=php">http://www.editions-eyrolles.com/rss.php?q=php</a> !'),
    array('nom'   => 'ENI',
          'site'  => 'http://www.editions-eni.fr/',
          'logo'  => 'logo_eni.jpg',
          'texte' => 'Le monde de l\'informatique est en perpétuelle évolution et les technologies liées notamment au
                  développement ne sont pas en reste. Editions ENI est au coeur de cette actualité technique et
                  accompagne les informaticiens dans leur apprentissage, leur évolution de carrière.
                  Nos différentes collections (manuels de référence, livres expert, mise en place de solution,
                  recueil d\'exercices) couvrent un grand nombre de besoins en proposant des approches pédagogique variées.
                  Nos livres sont écrits par des formateurs et/ou consultants.
                  Retrouvez tous les mois nos dernières nouveautés sur
                  <a href="http://www.editions-eni.fr">www.editions-eni.fr</a>.'),
//    array('nom'   => 'TooLinux',
//          'site'  => 'www.toolinux.com',
//          'logo'  => 'logo-toolinux.png',
//          'texte' => 'TOOLINUX.com est un quotidien d\'information sur Linux et les logiciels Libres. Généraliste, il offre chaque jour une revue de presse en ligne et des articles traitant du mouvement opensource, de l\'économie du libre ainsi que des logiciels Linux ou multi-plateformes. Depuis l\'été 2006, TOOLINUX.com s\'ouvre à la problématique de l\'interopérabilité des solutions informatiques.'),
//    array('nom'   => 'Linagora',
//          'site'  => 'www.linagora.com',
//          'logo'  => 'logo-linagora.png',
//          'texte' => 'Créateur des concepts de SS2L et de TM2L, LINAGORA se définit désormais comme un Editeur Orienté Service. Sa vocation est d\'être un spécialiste de l\'Open Source et d\'être un intermédiaire de confiance entre les communautés du logiciel libre ou des éditeurs Open Source d\'une part et les utilisateurs ou intégrateurs d\'autre part.'),
    array('nom'   => 'Programmez !',
          'site'  => 'http://www.programmez.com/',
          'logo'  => 'logo_programmez.gif',
          'texte' => 'Avec plus de 30.000 lecteurs mensuels, PROGRAMMEZ ! s\'est imposé comme
                  un magazine de référence des développeurs.',
    ),
    array('nom'   => 'PHP Solutions',
          'site'  => 'http://www.phpsolmag.org/',
          'logo'  => 'logo-php-solutions.png',
          'texte' => 'PHP Solutions est un magazine international pour tous ceux qui s\'intéressent
                  à la programmation en PHP et à la création d\'applications Web. C\'est le seul
                  magazine consacré entièrement au language PHP sur le marché français. Nous
                  décrivons des technologies les plus récentes, des projets complets et des
                  problèmes de programmeurs.'
    ),
    array('nom'   => 'Solutions & logiciels',
          'site'  => 'http://www.solutions-logiciels.com/',
          'logo'  => 'logo_solutions_et_logiciels.jpg',
          'texte' => 'Solutions-Logiciels : le magazine, le portail web, la newsletter des décideurs IT. Téléchargez gratuitement le dernier numéro du magazine&nbsp;: <a href="http://www.solutions-logiciels.com">www.solutions-logiciels.com</a>'
    ),
    array(),
);
$smarty->assign('partenaires', $partenaires);
?>