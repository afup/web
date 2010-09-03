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
// trunk : define('AFUP_CHEMIN_SOURCE', realpath(dirname(__FILE__) . '/../../../sources/Afup/'));

$config_forum['id'] = 5;
$coupons = array('PHPFRANCE','POLENORD','DIGIPORT', 'WAMPSERVER',
                  'ALTERWAY','ADOBE','SENSIO','SENSIOLABS','4D',
                  'HSC','MICROSOFT','ZEND', 'CODEUR',
                  'AFUP','CONFERENCIER', 'TWITTER',
                  'CAKEPHP','POLLEN', 'PIWAM',
                  'FINEFS','HOA',  'PIC', 'DRUPAL',
                  'FREEDOM','JELIX', 'HAVEFNUBB','PHPTV', 'ELAO','PRESTASHOP','DEVELOPPEZ',
                );

$coupons = array();

$config_forum['project_ids'] = array();
$config_forum['coupons'] = array_merge($coupons,array_map("strtolower",$coupons));
$config_forum['annee'] = 2010;
$config_forum['date_fin_appel_conferencier'] = mktime(23, 59, 59, 6, 30, $config_forum['annee']);
//$config_forum['date_fin_prevente'] = $config_forum['date_fin_appel_conferencier'] ;
$config_forum['date_fin_prevente'] = mktime(0, 0, 0, 07, 15, $config_forum['annee']);
$config_forum['date_debut'] = mktime(0, 0, 0, 11, 09, $config_forum['annee']);
$config_forum['date_fin'] = mktime(0, 0, 0, 11, 10, $config_forum['annee']);
$smarty->assign('forum_annee', $config_forum['annee'] );


$sponsors_platinium=array();
$smarty->assign('sponsors_platinium', $sponsors_platinium);
$smarty->assign('sponsors_platinium', array());

$sponsors_gold=array();
$smarty->assign('sponsors_gold', $sponsors_gold);
$smarty->assign('sponsors_gold', array());

$sponsors_silver=array(
    array('nom'   => 'E-Merchant',
          'site'  => 'http://www.e-merchant.com/',
          'logo'  => 'logo_e-merchant.gif',
          'texte' => "<p>Société du groupe Pixmania, E-merchant maîtrise et coordonne tous les métiers du e-commerce à votre demande : conception, hébergement, exploitation de votre activité.</p>
						<p>Profitez de nos 10 ans de pratique et de réussite comme acteur majeur du e-commerce pour servir votre croissance.</p>",
	),
    array('nom'   => 'Linagora',
          'site'  => 'http://www.linagora.com/',
          'logo'  => 'logo_linagora.gif',
          'texte' => "<p>LINAGORA, société spécialisée en Logiciel <strong>Open Source, est le leader français de ce marché</strong>, avec plus de 160 personnes et une présence <a href=\"http://www.linagora.com/-Nos-agences-\">en France (Paris, Toulouse, Lyon et Marseille), en Belgique (Bruxelles) et aux États-Unis (San Francisco)</a>.</p>
						<p><strong>LINAGORA édite ses <a href=\"http://www.linagora.com/-PRODUITS-\">propres logiciels Open Source</a></strong> et propose une <strong>gamme de <a href=\"http://www.linagora.com/-SERVICES-\">services professionnels</a></strong> pour réussir les grands projets du Libre.</p>
						<p>Plus d'informations : <a href=\"www.linagora.com\">www.linagora.com</a></p>",
	),
	array('nom'   => 'Oxalide',
          'site'  => 'http://www.oxalide.com/',
          'logo'  => 'logo_oxalide.jpg',
          'texte' => "<p><strong>Oxalide</strong>, hébergeur spécialisé dans les technologies open-source, conçoit des infrastructure sur-mesure qui respectent les contraintes de vos projets PHP.</p>
						<p>Déléguez la gestion de vos serveurs grâce à nos services d'infogérance et assurez l'évolutivité et la qualité de service grâce à notre conseil et notre expertise.</p>
						<p>Oxalide propulse de nombreux sites PHP : lexpress.fr, lanvin.com, la ligue nationale de Rugby, etc.</p>",
	),
);
$smarty->assign('sponsors_silver', $sponsors_silver);

$sponsors_bronze=array();
$smarty->assign('sponsors_bronze', $sponsors_bronze);
$smarty->assign('sponsors_bronze', array());

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