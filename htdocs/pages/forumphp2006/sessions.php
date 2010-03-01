<?php
require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

$sessions_fonctionnelles=array(
    array('horaire'            => '9h - 9h15',
          'nom'                => 'Keynote',
          'resume'             => 'Cette session d\'ouverture a pour objectif de présenter l\'AFUP qui est organisatrice de ce cinquième forum PHP. Il sera également abordé les évolutions de moeurs vis à vis de PHP, son adoption massive sur Internet et sa percée sur les Intranet.<br /><br />Perrick PENET et Romain BOURDON respectivement président et trésorier de l\'AFUP présenteront également le programme des conférences de ces deux jours.',
          'conferenciers'      => array(array('code' => 'ppenet',
                                              'nom'  => 'Perrick PENET'),
                                        array('code' => 'rbourdon',
                                              'nom'  => 'Romain BOURDON'))),
    array('horaire'            => '9h20 - 10h20',
          'nom'                => 'PHP en 2006 : writing and profiling rich applications',
          'resume'             => 'PHP est devenu très populaire grâce à son approche simple et pragmatique du problème "Web". Cet univers du web a beaucoup évolué et on parle désormais du "Web2.0". PHP y prend toute sa place avec de plus en plus de personnes combinant des services web pour obtenir des applications riches avec AJAX. Au cours de cette session Rasmus couvrira les briques nécessaires à la construction de ces applications modernes : avec un paquet d\'exemples au passage.',
          'conferenciers'      => array(array('code' => 'rlerdorf',
                                              'nom'  => 'Rasmus LERDORF'))),
    array('horaire'            => '10h30 - 11h30',
          'nom'                => 'Ajax et Web Services en PHP : Google AdWords API avec APIlity',
          'resume'             => 'AdWords a fait la fortune de Google. Et l\'API AdWords, une bibliothèque pour réaliser un service Web,'.
                                  ' vous offre cette puissance. Une librairie Open Source - APIlity - permet de remplacer des appels de'.
                                  ' fonctions AdWords API complexes par des appels de fonctions PHP. Découvrez un cas concret de l\'application'.
                                  ' web d\'aujourd\'hui et de demain avec web services et AJAX.',
          'conferenciers'      => array(array('code' => 'tsteiner',
                                              'nom'  => 'Thomas STEINER'))),
    array('horaire'            => '11h40 - 12h40',
          'nom'                => 'PHP dans l\'entreprise ... la contribution de ZEND',
          'resume'             => 'Un an après son installation en France, ZEND France présentera sa démarche et son apport concret sur le marché français, pour l\'adoption de PHP comme option professionnelle et stratégique dans le monde de l\'entreprise et des grandes organisations.<br />'.
								  'Zeev SURASKI sera accompagné par Laurent BOUFFIES pour présenter le projet PRESTO pour l\'Administration. Le "ZEND Way for PHP" (professionnalisation des pratiques PHP) et le "ZEND framework" (capitalisation, mutualisation et distribution de composants PHP) seront aussi abordés.',
          'conferenciers'      => array(array('code' => 'zsuraski',
											  'nom' => 'Zeev SURASKI'),
									    array('code' => 'lbouffies',
									    	  'nom' => 'Laurent BOUFFIES'))),
    array('horaire'            => '14h - 14h45',
          'nom'                => 'PHP au Service Public Fédéral Finances de Belgique : l\'intégration réussie PHP-Java',
          'resume'             => 'Depuis l\'an 2000, un vaste chantier de modernisation a été mis en oeuvre, notamment au niveau informatique.'.
                                  ' Dans un univers au départ Java, PHP5 (avec PDO et son modèle objet amélioré) s\'est fait une'.
                                  ' place : le retour d\'expérience précieux d\'une évolution à grande échelle.',
          'conferenciers'      => array(array('code' => 'dvannuffelen',
                                              'nom'  => 'Denis VAN NUFFELEN'))),
    array('horaire'            => '14h55 - 15h40',
          'nom'                => 'Optimisation / industrialisation d\'une plateforme de service basée sur PHP',
          'resume'             => 'Cette conférence résolument axée vers la pratique permettra de trouver des réponses à l\'optimisation de PHP:'.
          		                 ' évaluer un site existant, mettre en place une architecture scalable et optimale, optimiser les performances'.
                                  ' (configuration logicielle, cache, compilation, bases de données). Elle sera illustrée par une'.
                                  ' présentation des évolutions et optimisations de la plateforme de services TV / ADSL du groupe Canal+.',
          'conferenciers'      => array(array('code' => 'cpierredegeyer',
                                              'nom'  => 'Cyril PIERRE DE GEYER'),
                                        array('code' => 'gponcon',
                                              'nom'  => 'Guillaume PON&Ccedil;ON'),
                                        array('code' => 'flombardi',
                                              'nom'  => 'Franck LOMBARDI'))),
    array('horaire'            => '15h50 - 16h30',
          'nom'                => 'Des briques Open Source pour refondre un site internet en PHP (Trac, SVN, Symfony, architecture) -- l\'exemple de Richelieu Finance',
          'resume'             => 'Richelieu Finance a fait appel à Clever Age pour l\'assister dans la refont de son site internet en PHP.'.
                                  ' Ce retour d\'expérience sera l\'occasion de présenter l\'atelier de développement basé sur Trac et'.
                                  ' Subversion ainsi que l\'intégration du framework d\'application PHP5 Symfony qui a permis de reprendre les'.
                                  ' développements existants. La mise en place d\'une architecture de serveurs tolérante aux pannes et'.
                                  ' progressivement extensible complétera la présentation.',
          'conferenciers'      => array(array('code' => 'trivoallan',
                                              'nom'  => 'Tristan RIVOALLAN'),
                                        array('code' => 'xlacot',
                                              'nom'  => 'Xavier LACOT'),
                                        array('code' => 'hschmitt',
                                              'nom'  => 'Hervé SCHMITT'))),
    array('horaire'            => '16h40 - 17h25',
          'nom'                => 'eZ systems : PHP inside',
          'resume'             => 'eZ systems est éditeur de logiciels Open Source pour les entreprises, dans le domaine de la gestion de contenus. Depuis sa création en 1999, eZ systems a misé sur la technologie PHP pour développer ses solutions. Aujourd\'hui, dans une logique d\'ouverture qui lui est très chère, eZ systems propose librement eZ publish, un des logiciels de gestion de contenus Open Source les plus sophistiqués sur le marché ainsi que eZ components, un ensemble de librairies PHP de haute qualité. Cette conférence présentera eZ  publish, le logiciel principal d\'eZ systems, sous un axe technique et fonctionnel, et présentera également un retour d\'expérience sur le choix stratégique de PHP pour un éditeur de logiciel tel que eZ systems et les liens tenus existant aujourd\'hui entre PHP et eZ systems.',
          'conferenciers'      => array(array('code' => 'rbenedetti',
                                              'nom'  => 'Roland BENEDETTI'),
                                        array('code' => 'bdunogier',
                                              'nom'  => 'Bertrand DUNOGIER'))),
);
$smarty->assign('sessions_fonctionnelles', $sessions_fonctionnelles);

$sessions_techniques=array(
    array('horaire'            => '9h - 9h15',
          'nom'                => 'Keynote',
          'resume'             => 'Guillaume PON&Ccedil;ON et Arnaud LIMBOURG, respectivement vice-président et secrétaire de l\'AFUP, présenteront le programme de cette journée technique.',
          'conferenciers'      => array(array('code' => 'gponcon',
                                              'nom'  => 'Guillaume PON&Ccedil;ON'),
                                        array('code' => 'alimbourg',
                                              'nom'  => 'Arnaud LIMBOURG'))),
    array('horaire'            => '9h20 - 10h20',
          'nom'                => 'eZ components, RAD pour PHP',
          'resume'             => 'eZ components est une librairie de composants pour PHP qui permettent de résoudre une large panoplie des'.
                                  ' problèmes récurrents en développement web. Intéropérabilité, design propre, flexibilité, liberté,'.
                                  ' cohérence : telles en sont les maîtres-mots. Avec aussi une bonne documentation, une license BSD claire'.
                                  ' et une gratuité totale. Derick Rethans - responsable du projet - en présentera la structure, les contenus'.
                                  ' et les avancées, avec une démonstration s\'appuyant sur les composants les plus excitants.',
          'conferenciers'      => array(array('code' => 'drethans',
                                              'nom'  => 'Derick RETHANS'))),
    array('horaire'            => '10h30 - 11h30',
          'nom'                => 'Design Patterns & PHP',
          'resume'             => 'Les design patterns, largement connus et utilisés dans le monde Java, sont un peu la prose du monde PHP :'.
                                  ' Les développeurs les utilisent parfois sans le savoir.  Cette session à pour objectif de présenter les'.
                                  ' principaux designs patterns, leurs objectifs, et la façon dont ils peuvent être implémentés en PHP. Une'.
                                  ' session truffée d\'exemples d\'implémentation avec en particulier une gestion transparente des transactions.',
          'conferenciers'      => array(array('code' => 'gcroes',
                                              'nom'  => 'Gérald CROES'))),
    array('horaire'            => '11h40 - 12h40',
          'nom'                => 'PHPUnit: Comment faire du développement agile un atout compétitif',
          'resume'             => 'PHPUnit  est  une  librairie  de  tests  unitaires  qui vous permettra d\'améliorer  la  qualité  de  vos'.
                                  ' applications, leur flexibilité et le votre  confort de développement. A l\'issue de cette présentation,'.
                                  ' vous saurez comment fonctionne cette librairie, comment l\'utiliser et surtout comment se servir de cette'.
                                  ' approche "agile" pour livrer encore plus rapidement vos projets. Un retour d\'expérience sur une application'.
                                  ' d\'entreprise -- le gestionnaire  de rapports utilisateurs wIT -- permettra de voir avec un cas concret'.
                                  ' les améliorations quotidienne apportées par cette technique.',
          'conferenciers'      => array(array('code' => 'shordeaux',
                                              'nom'  => 'Sébastien HORDEAUX'))),
    array('horaire'            => '14h - 15h00',
          'nom'                => 'Sécurité des applications PHP',
          'resume'             => 'Depuis 2005, la sécurité est un point crucial pour les applications Web en général et PHP en particulier.'.
                                  ' Avec son statut de langage dominant sur le Web, PHP est une cible facile pour les pirates. Dans cette'.
                                  ' session, vous aurez un bilan des problèmes de sécurité qui se présentent aux applications Web'.
                                  ' écritent en PHP et MySQL, les techniques d\'attaques et les défenses à mettre en place, ainsi que les'.
                                  ' concepts de protections des applications. Avec le regard exercé d\'un hébergeur reconnu.',
          'conferenciers'      => array(array('code' => 'dseguy',
                                              'nom'  => 'Damien SEGUY'))),
    array('horaire'            => '15h10 - 16h10',
          'nom'                => 'PHPMeter, un outil simple et léger pour évaluer la qualité d\'un code PHP',
          'resume'             => 'La présentation porte sur l\'outil PHPMeter d\'évaluation de la qualité du code d\'une application PHP.'.
                                  ' PHPMeter est un outil d\'analyse de code PHP 4 & 5 basé sur la famille de fonctions Tokenizer de PHP.'.
                                  ' PHPMeter analyse les sources et collecte une série de mesures relatives à la qualité du code (complexité,'.
                                  ' maintenabilité, fiabilité, évolutivité). Il permet l\'évaluation de trois qualités importantes d\'un code'.
                                  ' source PHP, à savoir: la maintenabilité, la fiabilité et l\'évolutivité. La présentation sera faite sur'.
                                  ' base de l\'analyse du code source de deux projets open source d\'envergure : Spip et Wordpress.',
          'conferenciers'      => array(array('code' => 'mlopez',
                                              'nom'  => 'Miguel LOPEZ'))),
    array('horaire'            => '16h20 - 17h20',
          'nom'                => 'Unicode : une révolution en marche pour PHP6',
          'resume'             => 'Le support d\'Unicode en natif est la principale avancée de PHP6. Au cours de cette sessions vous'.
                                  ' découvrirez la révolution apportée à votre façon de programmer et manipuler des textes'.
                                  ' mutlilingues. Le mot d\'ordre ? Profiter enfin des subtilités de langage et de culture au coeur de votre'.
                                  ' code !',
          'conferenciers'      => array(array('code' => 'azmievski',
                                              'nom'  => 'Andrei ZMIEVSKI')))
);
$smarty->assign('sessions_techniques', $sessions_techniques);

$smarty->display('sessions.html');
?>