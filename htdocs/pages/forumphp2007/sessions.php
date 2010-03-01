<?php
require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

$sessions_fonctionnelles=array(
    array('horaire'            => '9h - 9h10',
          'nom'                => 'Keynote',
          'resume'             => 'Cette session d\'ouverture a pour objectif de présenter l\'AFUP qui est organisatrice de ce septième forum PHP.',
          'conferenciers'      => array(array('code' => 'alimbourg',
                                              'nom'  => 'Arnaud LIMBOURG'),
                                        array('code' => 'ppenet',
                                              'nom'  => 'Perrick PENET'))),
    array('horaire'            => '9h15 - 10h15',
          'nom'                => 'PHP en 2007 : PHP4 est mort, longue vie à PHP5 & 6',
          'resume'             => 'PHP est devenu très populaire grâce à son approche simple et pragmatique du problème "Web". Cet univers du web a beaucoup évolué et on parle désormais du "Web2.0". PHP y prend toute sa place avec de plus en plus de personnes combinant des services web pour obtenir des applications riches avec AJAX. Au cours de cette session Rasmus couvrira les briques nécessaires à la construction de ces applications modernes : avec un paquet d\'exemples au passage.',
          'conferenciers'      => array(array('code' => 'rlerdorf',
                                              'nom'  => 'Rasmus LERDORF'))),

    array('horaire'            => '10h15 - 11h15',
          'nom'                => 'Web 2.0 : Améliorer l\'experience utilisateur de vos applications PHP grâce à Flex',
          'resume'             => "Les RIA (Rich Internet Applications) remportent un succès grandissant. Les acteurs du business web comme Google ou et Yahoo!, tout comme les marques classiques comme Harley Davidson ou Sony Ericsson affirment leurs présence sur le web en proposant des expériences de plus en plus riches pour l'internaute.

                                   Cependant, encore peu de développeurs de RIA tirent profit de la puissance de la technologie serveur PHP combinée aux technologies de présentation comme Ajax, XUL, Flash ou Flex. Durant cette présentation, vous découvrirez comment mixer les technologies openSource Adobe Flex et PHP et enfin proposer des expériences nouvelles sur le web qui vont modifier les interactions avec les utilisateurs et capter leur attention.",
          'conferenciers'      => array(array('code' => 'mchaize',
                                              'nom'  => 'Michaël CHAIZE'))),

    array('horaire'            => '11h30 - 12h30',
          'nom'                => 'Retour d\'expérience de la Chambre de Commerce de Paris : du Chaos à l\'Agilité avec PHP',
          'resume'             => "Qui ne s'est pas retrouvé un jour dans un projet à l'issue incertaine pour diverses raisons ? Délais ridiculement courts, spécifications incomplètes ou bien changeant sans arrêt, collègues peu adaptés au travail en équipe, pas de chef de projet identifié, ... la liste des menaces pouvant peser sur un projet de développement est longue. Cette session s'efforcera de démontrer qu'avec les bons outils (PHP bien sûr), et un peu de méthode, on peut arriver à s'en sortir. S'il n'est pas possible d'utiliser une méthode agile dans son intégralité, certaines techniques peuvent être fort utiles. Nous étudierons les stratégies de tests (unitaires, fonctionnels et utilisateurs) à employer, ainsi que les outils associés. Nous montrerons enfin que PHP par sa souplesse, sa simplicité d'emploi, et ses très nombreuses librairies et frameworks, est un peu le couteau suisse ultime pour survivre à ce genre de projets.",
          'conferenciers'      => array(array('code' => 'rrougeron',
                                              'nom' => 'Raphaël ROUGERON'))),


    array('horaire'            => '13h45 - 14h',
          'nom'                => 'Etat des lieux de PHP en France et annonce presse',
          'resume'             => 'En quelques minutes nous ferons un état des lieux de PHP en France puis l\'un des principaux projets OpenSource liés à PHP (plus de 6.000 téléchargements jours) nous fera l\'honneur de venir annoncer et présenter sa nouvelle version.',
          'conferenciers'      => array(array('code' => 'cpierredegeyer',
                                              'nom'  => 'Cyril PIERRE de GEYER'),
                                        array('code' => 'guest',
                                              'nom'  => 'GUEST'))),

    array('horaire'            => '14h - 15h',
          'nom'                => 'Retour d\'expérience de l\'integration de PHP chez les grands comptes',
          'resume'             => "Depuis plusieurs années, nous assistons à l'émergence de PHP au sein des grandes entreprises dans des domaines tels que la télécommunication, les transports et la santé. PHP y est utilisé à la fois pour créer des solutions d'intégration et pour développer des applications opérationnelles.
                                   Nous montrerons d'abord comment PHP peut s'insérer au sein de l'architecture de l'entreprise, et utiliser toues les ressources disponibles : serveurs, bases de données, web-services, réseau, authentification, etc.
                                   Nous expliquerons ensuite comment PHP est utilisé pour créer une base de communication entre des solutions hétérogènes, via des solutions SOA, SQL, XML, sécurité, etc.
                                   Enfin, nous décrirons quelques exemples d'applications opérationnelles réalisées en PHP, et leur rôle dans le fonctionnement quotidien de l'entreprise.",
          'conferenciers'      => array(array('code' => 'diachetta',
                                              'nom'  => 'David IACHETTA'),
                                        array('code' => 'vdupont',
                                              'nom'  => 'Vincent DUPONT'))),
    array('horaire'            => '15h - 16h',
          'nom'                => 'Audit de code, retour d\'experience',
          'resume'             => "Le développement d'application web est un domaine complexe et la sécurité est trop souvent oubliée lors de la création d'un site internet.

                                   Tout d'abord, la présentation s'attachera à mettre en avant les diffèrentes attaques possibles sur les applications web : injection SQL, inclusion de fichiers, injection de code, cross site scripting, cross site request forgery ainsi que leur exploitation. Une présentation des erreurs souvent rencontrées lors d'audits de code réalisé par HSC sera réalisé afin de mettre en avant les problèmes récurrents dans les applications PHP. L'ensemble de la présentation s'appuiera sur des exemples concrets provenant d'audit de code ou de vulnérabilités sur des applications libres.

                                   Enfin, la présentation abordera aussi le durcissement du moteur PHP à l'aide du fichier php.ini et comment ce durcissement peut réduire l'impact d'une vulnérabilité.",
          'conferenciers'      => array(array('code' => 'ncollignon',
                                              'nom'  => 'Nicolas COLLIGNON'),
                                        array('code' => 'lnyffenegger',
                                              'nom'  => 'Louis NYFFENEGGER'))),
    array('horaire'            => '16h15 - 17h15',
          'nom'                => 'Accessibilite : le developpeur PHP aux premières loges',
          'resume'             => "En dernier ressort, une fois les maquettes HTML livrées, c'est au développeur PHP qu'incombe souvent de produire le code qui sera utilisé sur le site. Nous détaillerons ensemble le minimum pour que le niveau d'accessibilité produit soit satisfaisant, et nous apprendrons à éviter les écueils que nous réserve le développement d'un site dynamique du point de vue de l'accessibilité du produit fini.",
          'conferenciers'      => array(array('code' => 'sdeschamps',
                                              'nom'  => 'Stéphane DESCHAMPS'))),
);
$smarty->assign('sessions_fonctionnelles', $sessions_fonctionnelles);

$sessions_techniques=array(
    array('horaire'            => '9h - 9h10',
          'nom'                => 'Keynote',
          'resume'             => 'Cette session d\'ouverture a pour but de présenter l\'AFUP et son rôle au sein de la communauté des techniciens. Elle abordera les évolutions techniques majeures proposées par PHP et leurs impacts sur les méthodes de travail.',
          'conferenciers'      => array(array('code' => 'alimbourg',
                                              'nom'  => 'Arnaud LIMBOURG'),
                                        array('code' => 'gponcon',
                                              'nom'  => 'Guillaume PON&Ccedil;ON'))),
    array('horaire'            => '9h15 - 10h15',
          'nom'                => 'Web 2.0 : Optimisation d\'un site Web 2.0 : le cas de wat.tv',
          'resume'             => "Assurer la tenue en charge et la disponibilité d'un site Web a fort traffic pose un certain nombre de problèmes, qui se trouvent exacerbés sur un site web 2.0, fortement interactif et personnalisé. Retour d'expérience de wat.tv, site de partage de médias audio/vidéo/photo du groupe TF1.",
          'conferenciers'      => array(array('code' => 'jfbustarret',
                                              'nom'  => 'Jean-François BUSTARRET'))),
    array('horaire'            => '10h45 - 11h45',
          'nom'                => 'Optimiser les performances avec un cache d\'OpCode - Le cas Facebook avec APC',
          'resume'             => "Facebook's exponential growth and popularity would not be possible without APC, a PHP bytecode and variable cache. Facebook's technical lead for PHP internals will introduce APC for intermediate and advanced PHP users. Attendees will learn about Facebook's scalability challenges and it's unique APC implementation details. Installation, configuration, and usage techniques will be presented that can be applied to improve performance and user experience.",
          'conferenciers'      => array(array('code' => 'bshire',
                                              'nom'  => 'Brian SHIRE'))),
    array('horaire'            => '11h45 - 12h45',
          'nom'                => 'Web 2.0 : Rich Desktop Applications : l\'evolution des methodes de programmation côte serveur',
          'resume'             => "Traditionnellement, le développement d'applis web en PHP prenait en compte la génération de l'interface HTML de l'application, mais l'arrivée en masse de nouvelles plateformes de RDA (Adobe AIR, Silverlight, XULRunner, etc...) va entrainer une évolution de l'architecture côté serveur. Les développeurs PHP vont devoir évoluer vers une architecture applicative orientée services, dans le but de fournir une API accessible par divers protocoles de communication (REST, SOAP, XMLRPC) et capable de gérer différents formats de données (XML, JSON, etc..). Après une revue des différentes plateformes RDA existantes, de leurs points forts et faibles, et surtout de leurs possibilités d'interfaçage avec PHP, nous examinerons plusieurs architectures possibles pour ce type d'applications, en mettant en avant des solutions permettant de fournir également aux utilisateurs une interface web classique. Nous réfléchirons en particulier aux changements de méthodes de développement nécessaires, notamment par rapport aux tests de l'application.",
          'conferenciers'      => array(array('code' => 'rrougeron',
                                              'nom'  => 'Raphaël ROUGERON'))),
    array('horaire'            => '14h - 15h',
          'nom'                => 'Web 2.0 : Flex et PHP: techniques d\'intégration pour faire communiquer un client riche et un backoffice PHP',
          'resume'             => "Les applications riches réalisées avec le SDK OpenSource Flex permettent de tirer profit de toute la puissance du Player Flash côté client. Les gains pour les utilisateurs sont évidents: expérience engageante, meilleurs temps de réponse, applications ergonomiques, rich media dans une même interface, etc...
									Thibault Imbert présentera les meilleurs techniques de communication entre une application Flex et un serveur PHP: http/REST, JSON et surtout AMF. Le framework opensource AMFPHP révolutionne l'échange de données en faisant transiter des objets typés sur un flux binaire sécurisé.",
          'conferenciers'      => array(array('code' => 'timbert',
                                              'nom'  => 'Thibault Imbert'),
                                        array('code' => 'mchaize',
                                              'nom'  => 'Michael CHAIZE'))),
    array('horaire'            => '15h - 16h',
          'nom'                => 'Sécurite MySQL',
          'resume'             => "La sécurité des bases de données est une condition critique à leur exploitation. Effacement, falsification ou simplement divulgation sont les menaces les plus sérieuses qui rôdent et attendent le premier faux-pas des administrateurs. Il est primordial de bien connaître les aspects sécurité de MySQL, et de faire des choix éclairés parmi les protections natives.Durant cette présentation nous examinerons le système de droits, les directives de configurations, les techniques d'intrusion et les vulnérabilités sur le Web : pour chaque menace, nous verrons quels sont les défenses disponibles pour se protéger efficacement.",
          'conferenciers'      => array(array('code' => 'dseguy',
                                              'nom'  => 'Damien SEGUY'))),
    array('horaire'            => '16h30 - 17h30',
          'nom'                => 'PHP::$unicode->i18n() : comment PHP 5.3 et PHP 6 vont changer la donne',
          'resume'             => 'Deux nouvelles versions de PHP sont en chantier avec leurs lots de fonctionnalités "namespace", "late static binding", "circular garbage collection", "unicode", etc.<br />Autant de nouveautés dans les mains des développeurs PHP. Un tour détaillé de ce qui attend les développeurs PHP dans les mois à venir avec le leader de la version 6 de PHP !',
          'conferenciers'      => array(array('code' => 'azmievski',
                                              'nom'  => 'Andrei ZMIEVSKI')))
);
$smarty->assign('sessions_techniques', $sessions_techniques);

$sessions_atelier_1=array(
    array('horaire'            => '9h - 10h30',
          'nom'                => 'Remanier son code pour PHP6',
          'resume'             => "PHP6 commence à pointer le bout de son nez : sous la forme d'un kata".
								" (écriture de code en live), nous explorerons des concepts et".
								" méthodes utilisables en PHP6 -- avec des tests unitaires et ".
								"de recette au passage !<br />De \"vielles\" lignes en PHP4, nous ".
								"aboutirons en direct à un code remanié prêt pour la migration.".
								" Attention après le 08/08/2008 -- fin définitive du support PHP4 -- il sera trop tard.",
          'conferenciers'      => array(array('code' => 'ppenet',
                                              'nom'  => 'Perrick PENET'))),
    array('horaire'            => '11h - 12h30',
          'nom'                => 'Utiliser pleinement le navigateur & les nouveaux clients Web',
          'resume'             => "Il est possible d'aller plus loin que les applications classiques. En profitant pleinement des capacités du navigateurs cet atelier vous montrera comment améliorer les performances et modulariser l'existant. HTTP, REST et Ajax sont au menu pour une application orienté services légère, simple à modifier et avec une API partageable avec vos clients.",
          'conferenciers'      => array(array('code' => 'edaspet',
                                              'nom'  => 'Eric DASPET'))),
    array('horaire'            => '14h - 15h30',
          'nom'                => 'PHP\'s Dirty Secrets',
          'resume'             => "PHP's internals are a big secret, even things that appear so easy from the outside are not as simple as they seem. In this session I will explain to you several of PHP's concepts, and with this knowledge you will be able to use PHP in a smarter way, increasing the performance of your applications.

                                   In the first part of this session I will explains the basics of how PHP manages to execute your scripts through a web server. This includes PHP's execution cycle: locating the file containing the script; reading, parsing, and compiling; the difference between compile time and run time and what sort of impact that has on certain uses of PHP. I will also cover how byte code caches such as APC, eAccelerator and the accelerator part of Zend Platform work.

                                   In the second part of the session I will dive into the internals a little bit more, and cover explain how PHP deals with variables, classes and references in great detail, going into the details of the implementation (copy-on-write, refcounting, circular references, types and objects).",
          'conferenciers'      => array(array('code' => 'drethans',
                                              'nom'  => 'Derick RETHANS'))),
);
$smarty->assign('sessions_atelier_1', $sessions_atelier_1);

$sessions_atelier_2=array(
    array('horaire'            => '9h - 10h30',
          'nom'                => 'Optimisation MySQL',
          'resume'             => "Installer et mettre en production un serveur MySQL est une chose aisée. Cette simplicité ne doit pas faire oublier que les performances de votre application dépendent également des performances de votre SGBD. Un paramétrage optimal et des requêtes pertinentes permettons d'éviter que votre serveur MySQL ne se transforme en goulet d'étranglement.

                                   Lors de cette conférence, nous verrons :
                                   - Paramètres du serveur MySQL
                                   - Tuning des requêtes
                                   - Les bonnes pratiques
                                   - Techniques avancées",
          'conferenciers'      => array(array('code' => 'odasini',
                                              'nom'  => 'Olivier DASINI'))),
    array('horaire'            => '11h - 12h30',
          'nom'                => 'Framework : Simplifier le developpement des interfaces bases de donnees avec Symfony',
          'resume'             => "Chaque projet Web nécessite de développement d'un Back-Office. Afin d'interagir avec la base de données, le développeur réinvente la roue à chaque projet afin de proposer à l'utilisateur une interface double permettant d'un côté de lister, filtrer, trier des listes d'éléments et d'un autre de visualiser, créer, mettre à jour et supprimer des éléments de la base. La création d'un telle interface est généralement longue, fastidieuse et sans réelle valeur ajoutée.

                                   Durant cette session, je vous propose de découvrir le générateur d'administration du framework symfony. Ce générateur d'administration permet de développer et de mettre en ligne une interface Back-Office de façon quasiment automatique à partir de la description de votre base de données.

                                   Le grand intérêt de ce générateur est qu'il est très extensible, tant du point de vue de sa configurabilité que des possibilités d'extensions et ceci grâce aux avancées du PHP5 et au socle du framework symfony.

                                   Enfin, nous verrons que cette abstraction est indépendante de l'ORM choisi et que symfony propose de façon indifférente la génération de ces interfaces avec Propel ou Doctrine de façon transparente.",
          'conferenciers'      => array(array('code' => 'fpotencier',
                                              'nom'  => 'Fabien POTENCIER'))),
    array('horaire'            => '14h - 15h30',
          'nom'                => 'Les technos Microsoft pour les applications PHP',
          'resume'             => "Fast CGI dans IIS 7 (Windows Server 2008), Microsoft Ajax Framework et PHP, Phalanger (Compiler du PHP pour le framework .NET), Silverlight",
          'conferenciers'      => array(array('code' => 'clauer',
                                              'nom'  => 'Christophe LAUER'))),
);
$smarty->assign('sessions_atelier_2', $sessions_atelier_2);

$smarty->display('sessions.html');
?>