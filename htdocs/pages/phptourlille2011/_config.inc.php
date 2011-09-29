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

define('AFUP_CHEMIN_SOURCE', realpath(dirname(__FILE__) . '/../../classes/afup/'));
date_default_timezone_set("Europe/Paris");
ini_set('display_errors',  $conf->obtenir('divers|afficher_erreurs'));

$config_forum['id'] = 6;
$coupons = array('INTERNIM','ADOBE','ZEND','ELAO','DEVELOPPEZ','MICROSOFT','WEKA',
				'VACONSULTING','CLEVERAGE','ENI','ALTERWAY','EMERCHANT','LINAGORA',
				'OXALIDE','BUSINESSDECISION','EYROLLES','PROGRAMMEZ','PHPSOLUTIONS',
				'RBSCHANGE','JELIX','CAKEPHPFR','HOA','DRUPAL','MAGIXCMS','FINEFS','SOLUTIONSLOGICIELS',
				'SYMFONY','DOLIBARR','PICPHPSQLI','CRISISCAMP','RBS','OBM',
				'EURATECH','POLENORD'
                );

$config_forum['project_ids'] = array();
$config_forum['coupons'] = array_merge($coupons,array_map("strtolower",$coupons));
$config_forum['annee'] = 2011;
$config_forum['date_fin_appel_projet'] = mktime(23, 59, 59, 10, 20, $config_forum['annee']);
$config_forum['date_fin_appel_conferencier'] = mktime(23, 59, 59, 5, 31, $config_forum['annee']);
$config_forum['date_fin_prevente'] = mktime(0, 0, 0, 8, 31, $config_forum['annee']);
$config_forum['date_fin_vente'] = mktime(0, 0, 0, 11, 23, $config_forum['annee']);
$config_forum['date_debut'] = mktime(0, 0, 0, 11, 24, $config_forum['annee']);
$config_forum['date_fin'] = mktime(0, 0, 0, 11, 25, $config_forum['annee']);
$smarty->assign('forum_annee', $config_forum['annee'] );


$sponsors_platinium=array(
//	array('nom'   => 'Adobe',
//          'site'  => 'http://www.adobe.com/fr/',
//          'logo'  => 'logo-adobe-sponsor.png',
//          'texte' => "<p>Adobe révolutionne l'échange d'idées et d'informations. Depuis vingt cinq
//						ans, les technologies et les logiciels réputés de cet éditeur redéfinissent
//						la communication des entreprises, du marché des loisirs et des particuliers
//						en établissant de nouveaux standards de production et de diffusion de
//						contenus véritablement fascinants - partout et à tout moment. À travers des
//						images d'une remarquable richesse visuelle pour l'impression, la vidéo et
//						le cinéma ou du contenu numérique dynamique adapté à une multitude de sup-
//						ports, l'impact des solutions est évident et peut être ressenti par
//						quiconque crée, visualise et manipule des informations. Fort de sa
//						réputation d'excellence et d'une gamme consti- tuée de nombreux produits
//						parmi les plus appréciés et les plus connus du marché, Adobe est l'un des
//						principaux éditeurs de logiciels mondiaux les plus diversifiés.</p>",
//	),
);
$smarty->assign('sponsors_platinium', $sponsors_platinium);

$sponsors_gold=array(
//	array('nom'   => 'Microsoft',
//          'site'  => 'http://www.microsoft.com/fr/fr/',
//          'logo'  => 'logo_microsoft.png',
//          'texte' => "<p>Depuis plusieurs années, Microsoft a multiplié les actions et engagements
//concrets en faveur de l'ouverture et tout particulièrement à destination
//des environnements Open Source. De la livraison de code au noyau Linux en
//juillet 2009 en GPL, à la coopération permanente avec des sociétés et
//communautés Open Source, son engagement est maintenant reconnu et respecté.
//En ce qui concerne spécifiquement les actions les plus marquantes avec
//l'environnement PHP, Microsoft a développé en 2009 un accélérateur,
//WinCache, afin d'optimiser les performances sur Windows Server et a inclut
//le support de la technologie PHP à son offre de Cloud Computing, Windows
//Azure. Cette dernière permet ainsi aux développeurs PHP de profiter de la
//souplesse du Cloud, dès maintenant.</p>",
//	),
);
$smarty->assign('sponsors_gold', $sponsors_gold);


$sponsors_silver=array(
//	array('nom'   => 'Alter Way',
//          'site'  => 'http://www.alterway.fr/',
//          'logo'  => 'logo-alter-way-sponsor.png',
//          'texte' => '<p>Alter Way, opérateur de services open source, accompagne les grands comptes, administrations, collectivités locales et Pme/Pmi dans le développement et l\'usage de leur système d\'information. Alter Way propose une offre industrielle à 360&deg;, structurée autour de cinq activités clés :</p>
//                      <ul>
//                      <li>Conseil IT (Alter Way Consulting),</li>
//                      <li>Communication, studio graphique et e-marketing (Reciprok),</li>
//                      <li>Intégration, développement et infogérance (Alter Way Solutions),</li>
//                      <li>Hébergement à valeur ajoutée (Alter Way Hosting),</li>
//                      <li>Formation (Alter Way Formation)</li></ul>
//                      <p>Accordant une place essentielle à sa contribution et à son implication dans l\'écosystème Open Source, Alter Way se caractérise par le niveau élevé d\'expertise de ses consultants, reconnus par la communauté. La société se distingue également par un investissement permanent en matière d\'innovation, la plaçant ainsi à la pointe des plus récentes avancées technologiques.</p>
//                      <p>Alter Way fut la première entreprise à fédérer les acteurs historiques de l\'Open Source autour d\'un projet d\'industrialisation du marché. Elle compte aujourd\'hui une centaine de collaborateurs. En 2009, elle a réalisé une croissance de 10% avec un chiffre d\'affaires de 9 M€.</p>',
//	),
);
$smarty->assign('sponsors_silver', $sponsors_silver);

$sponsors_bronze=array(
//	array('nom'   => 'Business & Decisions',
//          'site'  => 'http://www.fr.businessdecision.com/',
//          'logo'  => 'logo-business-decision-sponsor.png',
//          'texte' => "<p>Business & Decision est consultant et intégrateur de systèmes international (CIS). Leader de la Business Intelligence (BI) et du CRM, acteur majeur de l'e-Business, de l'Enterprise Information Management (EIM), des Enterprise Solutions ainsi que du Management Consulting, le Groupe contribue à la réussite des projets à forte valeur ajoutée des entreprises. Il est reconnu pour son expertise fonctionnelle et technologique par les plus grands éditeurs de logiciels du marché avec lesquels il a noué des partenariats.</p>
//						<p>Présent dans 18 pays, Business & Decision emploie actuellement plus de 2 500 personnes en France et dans le Monde.</p>",
//	),
);
$smarty->assign('sponsors_bronze', $sponsors_bronze);


$partenaires=array(
//    array('nom'   => 'Eyrolles',
//          'site'  => 'http://www.editions-eyrolles.com/',
//          'logo'  => 'logo_eyrolles.gif',
//          'texte' => 'Les Editions Eyrolles ont placé PHP au coeur de leur offre Développeurs,
//                  de l\'initiation (<a href="http://www.editions-eyrolles.com/Livre/9782212114072/php-5">
//                  manuels avec cours et exercices</a>,
//                  <a href="http://www.editions-eyrolles.com/Livre/9782212116786/php-mysql-et-javascript">
//                  apprentissage par la pratique</a>) à l\'exploitation professionnelle
//                  (livres de référence dont <a href="http://www.editions-eyrolles.com/Livre/9782212123692/php-5-avance">PHP 5 avancé</a>,
//                  études de cas détaillées dont une sur <a href="http://www.editions-eyrolles.com/Livre/9782212112344/php-5">PHP</a>).
//                  Au-delà de la maîtrise de PHP, chaque ouvrage offre un véritable savoir-faire métier au développeur.<br/>
//                  Suivez les nouveautés Eyrolles en vous abonnant au fil RSS
//                  <a href="http://www.editions-eyrolles.com/rss.php?q=php">http://www.editions-eyrolles.com/rss.php?q=php</a> !'),
);
$smarty->assign('partenaires', $partenaires);
