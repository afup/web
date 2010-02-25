-- phpMyAdmin SQL Dump
-- version 2.8.2.4
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Samedi 17 Janvier 2009 à  11:01
-- Version du serveur: 5.0.24
-- Version de PHP: 5.2.1
-- 
-- Base de données: `afup`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `afup_aperos`
-- 

DROP TABLE IF EXISTS `afup_aperos`;
CREATE TABLE IF NOT EXISTS `afup_aperos` (
  `id` int(11) NOT NULL auto_increment,
  `date` datetime NOT NULL,
  `lieu` varchar(70) NOT NULL,
  `id_ville` int(11) NOT NULL,
  `id_responsable` int(11) NOT NULL,
  `publier` tinyint(1) NOT NULL default '0',
  `annuler` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_aperos`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_aperos_inscrits`
-- 

DROP TABLE IF EXISTS `afup_aperos_inscrits`;
CREATE TABLE IF NOT EXISTS `afup_aperos_inscrits` (
  `id` int(11) NOT NULL auto_increment,
  `date` date NOT NULL,
  `login` varchar(20) NOT NULL,
  `mot_de_passe` varchar(32) NOT NULL,
  `nom` varchar(70) NOT NULL,
  `prenom` varchar(70) NOT NULL,
  `email` varchar(150) NOT NULL,
  `site_internet` varchar(150) NOT NULL,
  `id_ville` int(11) NOT NULL,
  `valider` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`login`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_aperos_inscrits`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_aperos_participants`
-- 

DROP TABLE IF EXISTS `afup_aperos_participants`;
CREATE TABLE IF NOT EXISTS `afup_aperos_participants` (
  `id_apero` int(11) NOT NULL,
  `id_inscript` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id_apero`,`id_inscript`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_aperos_participants`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_conferenciers`
-- 

DROP TABLE IF EXISTS `afup_conferenciers`;
CREATE TABLE IF NOT EXISTS `afup_conferenciers` (
  `conferencier_id` int(11) NOT NULL auto_increment,
  `id_forum` smallint(6) NOT NULL,
  `civilite` varchar(5) NOT NULL,
  `nom` varchar(70) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(65) NOT NULL,
  `societe` varchar(120) default NULL,
  `biographie` text NOT NULL,
  PRIMARY KEY  (`conferencier_id`),
  KEY `id_forum` (`id_forum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_conferenciers`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_conferenciers_sessions`
-- 

DROP TABLE IF EXISTS `afup_conferenciers_sessions`;
CREATE TABLE IF NOT EXISTS `afup_conferenciers_sessions` (
  `session_id` int(11) NOT NULL auto_increment,
  `conferencier_id` int(11) NOT NULL,
  PRIMARY KEY  (`session_id`,`conferencier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_conferenciers_sessions`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_cotisations`
-- 

DROP TABLE IF EXISTS `afup_cotisations`;
CREATE TABLE IF NOT EXISTS `afup_cotisations` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `date_debut` int(11) unsigned NOT NULL default '0',
  `type_personne` tinyint(3) unsigned NOT NULL default '0',
  `id_personne` smallint(5) unsigned NOT NULL default '0',
  `montant` float(5,2) unsigned NOT NULL default '0.00',
  `type_reglement` tinyint(3) unsigned NOT NULL default '0',
  `informations_reglement` varchar(255) default NULL,
  `date_fin` int(11) unsigned NOT NULL default '0',
  `numero_facture` varchar(15) NOT NULL default '',
  `commentaires` text,
  `nombre_relances` tinyint(3) unsigned default NULL,
  `date_derniere_relance` int(11) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_personne` (`id_personne`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_cotisations`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_departements`
-- 

DROP TABLE IF EXISTS `afup_departements`;
CREATE TABLE IF NOT EXISTS `afup_departements` (
  `id` char(2) NOT NULL default '0',
  `nom` varchar(100) NOT NULL default '',
  `id_region` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_departements`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_facturation_forum`
-- 

DROP TABLE IF EXISTS `afup_facturation_forum`;
CREATE TABLE IF NOT EXISTS `afup_facturation_forum` (
  `reference` varchar(255) NOT NULL default '',
  `montant` float NOT NULL default '0',
  `date_reglement` int(11) unsigned default NULL,
  `type_reglement` tinyint(1) unsigned NOT NULL default '0',
  `informations_reglement` varchar(255) default NULL,
  `email` varchar(100) NOT NULL default '',
  `societe` varchar(40) default NULL,
  `nom` varchar(40) default NULL,
  `prenom` varchar(40) default NULL,
  `adresse` text NOT NULL,
  `code_postal` varchar(10) NOT NULL default '',
  `ville` varchar(50) NOT NULL default '',
  `id_pays` char(2) NOT NULL default '',
  `autorisation` varchar(20) default NULL,
  `transaction` varchar(20) default NULL,
  `etat` tinyint(1) unsigned NOT NULL default '0',
  `facturation` tinyint(4) NOT NULL,
  `id_forum` smallint(6) NOT NULL,
  `date_facture` int(11) unsigned default NULL,
  PRIMARY KEY  (`reference`),
  KEY `id_pays` (`id_pays`),
  KEY `id_forum` (`id_forum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Facturation pour le forum PHP';

-- 
-- Contenu de la table `afup_facturation_forum`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_forum`
-- 

DROP TABLE IF EXISTS `afup_forum`;
CREATE TABLE IF NOT EXISTS `afup_forum` (
  `id` smallint(6) NOT NULL auto_increment,
  `titre` varchar(50) NOT NULL,
  `nb_places` int(11) unsigned NOT NULL default '0',
  `date_debut` date NOT NULL default '0000-00-00',
  `date_fin` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_forum`
-- 

INSERT INTO `afup_forum` (`id`, `titre`, `nb_places`, `date_debut`, `date_fin`) VALUES (1, 'Forum 2006', 200, '0000-00-00', '0000-00-00'),
(2, 'Forum 2007', 400, '0000-00-00', '0000-00-00');

-- --------------------------------------------------------

-- 
-- Structure de la table `afup_forum_planning`
-- 

DROP TABLE IF EXISTS `afup_forum_planning`;
CREATE TABLE IF NOT EXISTS `afup_forum_planning` (
  `id` int(11) NOT NULL auto_increment,
  `id_session` int(11) default NULL,
  `debut` int(10) default NULL,
  `fin` int(10) default NULL,
  `id_salle` smallint(4) default NULL,
  `id_forum` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_forum_planning`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_forum_salle`
-- 

DROP TABLE IF EXISTS `afup_forum_salle`;
CREATE TABLE IF NOT EXISTS `afup_forum_salle` (
  `id` smallint(4) NOT NULL auto_increment,
  `nom` varchar(255) default NULL,
  `id_forum` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_forum_salle`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_forum_sessions_commentaires`
-- 

DROP TABLE IF EXISTS `afup_forum_sessions_commentaires`;
CREATE TABLE IF NOT EXISTS `afup_forum_sessions_commentaires` (
  `id` int(11) NOT NULL auto_increment,
  `id_session` int(11) default NULL,
  `id_personne_physique` int(11) default NULL,
  `commentaire` mediumtext,
  `date` int(10) default NULL,
  `public` tinyint(4) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_forum_sessions_commentaires`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_inscription_forum`
-- 

DROP TABLE IF EXISTS `afup_inscription_forum`;
CREATE TABLE IF NOT EXISTS `afup_inscription_forum` (
  `id` int(5) unsigned NOT NULL auto_increment,
  `date` int(11) unsigned NOT NULL default '0',
  `reference` varchar(255) NOT NULL default '',
  `type_inscription` tinyint(1) unsigned NOT NULL default '0',
  `montant` float NOT NULL default '0',
  `informations_reglement` varchar(255) default NULL,
  `civilite` varchar(4) NOT NULL default '',
  `nom` varchar(40) NOT NULL default '',
  `prenom` varchar(40) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `telephone` varchar(40) default NULL,
  `citer_societe` tinyint(1) unsigned NOT NULL default '0',
  `newsletter_afup` tinyint(1) unsigned NOT NULL default '0',
  `newsletter_nexen` tinyint(1) unsigned NOT NULL default '0',
  `commentaires` text,
  `etat` tinyint(1) unsigned NOT NULL default '0',
  `facturation` tinyint(4) NOT NULL,
  `id_forum` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_forum` (`id_forum`),
  KEY `reference` (`reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Inscriptions au forum PHP';

-- 
-- Contenu de la table `afup_inscription_forum`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_inscriptions_rappels`
-- 

DROP TABLE IF EXISTS `afup_inscriptions_rappels`;
CREATE TABLE IF NOT EXISTS `afup_inscriptions_rappels` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `email` varchar(255) NOT NULL,
  `date` int(10) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_inscriptions_rappels`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_logs`
-- 

DROP TABLE IF EXISTS `afup_logs`;
CREATE TABLE IF NOT EXISTS `afup_logs` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `date` int(11) unsigned NOT NULL default '0',
  `id_personne_physique` smallint(5) unsigned NOT NULL default '0',
  `texte` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `id_personne_physique` (`id_personne_physique`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_logs`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_news`
-- 

DROP TABLE IF EXISTS `afup_news`;
CREATE TABLE IF NOT EXISTS `afup_news` (
  `ID` mediumint(8) unsigned NOT NULL auto_increment,
  `ID_TOPIC` tinyint(3) unsigned NOT NULL default '0',
  `DATE` date NOT NULL default '0000-00-00',
  `LIB` varchar(128) NOT NULL default '',
  `TEXTE` text NOT NULL,
  `IMG` tinyint(4) NOT NULL default '0',
  `IMG_ALIGN` tinyint(4) NOT NULL default '0',
  `LINK` varchar(200) NOT NULL default '',
  `PRIORITY` tinyint(4) NOT NULL default '1',
  `DRAW` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`ID`),
  KEY `DRAW` (`DRAW`),
  KEY `DATE` (`DATE`),
  KEY `PRIORITY` (`PRIORITY`),
  KEY `ID_TOPIC` (`ID_TOPIC`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_news`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_oeuvres`
-- 

DROP TABLE IF EXISTS `afup_oeuvres`;
CREATE TABLE IF NOT EXISTS `afup_oeuvres` (
  `id` int(11) NOT NULL auto_increment,
  `id_personne_physique` smallint(5) unsigned default NULL,
  `categorie` varchar(255) default NULL,
  `valeur` smallint(5) default NULL,
  `date` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_oeuvres`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_pays`
-- 

DROP TABLE IF EXISTS `afup_pays`;
CREATE TABLE IF NOT EXISTS `afup_pays` (
  `id` char(2) NOT NULL default '',
  `nom` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_pays`
-- 

INSERT INTO `afup_pays` (`id`, `nom`) VALUES ('AD', 'Andorre'),
('AE', 'Emirats Arabes Unis'),
('AF', 'Afghanistan'),
('AG', 'Antigua-et-Barbuda'),
('AI', 'Anguilla'),
('AL', 'Albanie'),
('AM', 'Arménie'),
('AN', 'Antilles néerlandaises'),
('AO', 'Angola'),
('AQ', 'Antarctique'),
('AR', 'Argentine'),
('AS', 'Samoa américaines'),
('AT', 'Autriche'),
('AU', 'Australie'),
('AW', 'Aruba'),
('AZ', 'Azerbaïdjan'),
('BA', 'Bosnie et Herzégovine'),
('BB', 'Barbade (la)'),
('BD', 'Bangladesh'),
('BE', 'Belgique'),
('BF', 'Burkina Faso'),
('BG', 'Bulgarie'),
('BH', 'Bahre'),
('BI', 'Burundi'),
('BJ', 'Bénin'),
('BM', 'Bermudes'),
('BN', 'Brunei'),
('BO', 'Bolivie'),
('BR', 'Brésil'),
('BS', 'Bahamas'),
('BT', 'Bhoutan'),
('BV', 'Iles Bouvet'),
('BW', 'Botswana'),
('BY', 'Biélorussie'),
('BZ', 'Belize'),
('CA', 'Canada'),
('CC', 'Iles Cocos-Keeling'),
('CD', 'République démocratique du Congo'),
('CF', 'République Centrafricaine'),
('CG', 'Congo'),
('CH', 'Suisse'),
('CI', 'Côte D''Ivoire'),
('CK', 'Iles Cook'),
('CL', 'Chili'),
('CM', 'Cameroun'),
('CN', 'Chine'),
('CO', 'Colombie'),
('CR', 'Costa Rica'),
('CU', 'Cuba'),
('CV', 'Cap-Vert'),
('CX', 'Ile Christmas'),
('CY', 'Chypre'),
('CZ', 'République tchèque'),
('DE', 'Allemagne'),
('DJ', 'Djibouti'),
('DK', 'Danemark'),
('DM', 'Dominique(la)'),
('DO', 'République Dominicaine'),
('DZ', 'Algérie'),
('EC', 'Equateur (République de l'')'),
('EE', 'Estonie'),
('EG', 'Egypte'),
('ER', 'Erythr'),
('ES', 'Espagne'),
('ET', 'Ethiopie'),
('FI', 'Finlande'),
('FJ', 'Iles Fidji'),
('FK', 'Iles Malouines'),
('FM', 'Micronésie'),
('FO', 'Iles Faro'),
('FR', 'France'),
('GA', 'Gabon'),
('GD', 'Grenade'),
('GE', 'Géorgie'),
('GF', 'Guyane française (DOM-TOM)'),
('GH', 'Ghana'),
('GI', 'Gibraltar'),
('GL', 'Groenland'),
('GM', 'Gambie'),
('GN', 'Guin'),
('GP', 'Guadeloupe (France DOM-TOM)'),
('GQ', 'Guinée Equatoriale'),
('GR', 'Grèce'),
('GS', 'Géorgie du Sud et Sandwich du Sud (IIes)'),
('GT', 'Guatemala'),
('GU', 'Guam'),
('GW', 'Guinée-Bissau'),
('GY', 'Guyane'),
('HK', 'Hong Kong (Région administrative spéciale de)'),
('HM', 'Iles Heard et Mc Iles Donald'),
('HN', 'Honduras (le)'),
('HR', 'Croatie(Hrvatska)'),
('HT', 'Haïti'),
('HU', 'Hongrie'),
('ID', 'Indonésie'),
('IE', 'Irlande'),
('IL', 'Isra'),
('IN', 'Inde'),
('IO', 'Territoires Britanniques de l''océan Indien'),
('IQ', 'Irak'),
('IR', 'Iran'),
('IS', 'Islande'),
('IT', 'Italie'),
('JM', 'Jamaïque'),
('JO', 'Jordanie'),
('JP', 'Japon'),
('KE', 'Kenya'),
('KG', 'Kirghizistan'),
('KH', 'Cambodge'),
('KI', 'Kiribati'),
('KM', 'Comores'),
('KN', 'Saint-Christopher et Nevis (Iles)'),
('KP', 'République démocratique populaire de Cor'),
('KR', 'Cor'),
('KW', 'Kowe'),
('KY', 'Iles Caïmans'),
('KZ', 'Kazakhstan'),
('LA', 'République Démocratique populaire du Laos'),
('LB', 'Liban'),
('LC', 'Sainte-Lucie'),
('LI', 'Liechtenstein'),
('LK', 'Sri Lanka'),
('LR', 'Liberia'),
('LS', 'Lesotho'),
('LT', 'Lituanie'),
('LU', 'Luxembourg'),
('LV', 'Lettonie'),
('LY', 'Jamahiriya arabe libyenne (Lybie)'),
('MA', 'Maroc'),
('MC', 'Monaco'),
('MD', 'Moldavie'),
('MG', 'Madagascar'),
('MH', 'Iles Marshall'),
('MK', 'Macédoine'),
('ML', 'Mali'),
('MM', 'Myanmar (Union de)'),
('MN', 'Mongolie'),
('MP', 'Mariannes du Nord(Commonwealth des Iles)'),
('MQ', 'Martinique (France DOM-TOM)'),
('MR', 'Mauritanie'),
('MS', 'Montserrat'),
('MT', 'Malte'),
('MU', 'Ile Maurice'),
('MV', 'Maldives'),
('MW', 'Malawi'),
('MX', 'Mexique'),
('MY', 'Malaisie'),
('MZ', 'Mozambique'),
('NA', 'Namibie'),
('NC', 'Nouvelle Calédonie'),
('NE', 'Niger'),
('NF', 'Ile de Norfolk'),
('NG', 'Nigéria'),
('NI', 'Nicaragua'),
('NL', 'Pays-Bas'),
('NO', 'Norvège'),
('NP', 'Népal'),
('NR', 'Nauru (République de)'),
('NU', 'Niue'),
('NZ', 'Nouvelle Zélande'),
('OM', 'Oman'),
('PA', 'Panama'),
('PE', 'Pérou'),
('PF', 'Polynésie française (DOM-TOM)'),
('PG', 'Papouasie Nouvelle-Guin'),
('PH', 'Philippines'),
('PK', 'Pakistan'),
('PL', 'Pologne'),
('PM', 'Saint-Pierre-et-Miquelon (France DOM-TOM)'),
('PN', 'Pitcairn (Iles)'),
('PR', 'Porto Rico'),
('PT', 'Portugal'),
('PW', 'Palau'),
('PY', 'Paraguay'),
('QA', 'Qatar'),
('RE', 'Réunion (Ile de la) - (France DOM-TOM)'),
('RO', 'Roumanie'),
('RU', 'Fédération de Russie'),
('RW', 'Rwanda'),
('SA', 'Arabie Saoudite'),
('SB', 'Iles Salomon'),
('SC', 'Seychelles'),
('SD', 'Soudan'),
('SE', 'Suède'),
('SG', 'Singapour'),
('SH', 'Sainte Hélène'),
('SI', 'Slovénie'),
('SJ', 'Svalbard'),
('SK', 'Slovaquie'),
('SL', 'Sierra Leone'),
('SM', 'Saint-Marin'),
('SN', 'Sénégal'),
('SO', 'Somalie'),
('SR', 'Suriname'),
('ST', 'Séo Tomé et Prince'),
('SV', 'Salvador'),
('SY', 'République arabe syrienne'),
('SZ', 'Swaziland'),
('TC', 'Iles Turks et Caécos'),
('TD', 'Tchad'),
('TF', 'Terres Australes françaises (DOM-TOM)'),
('TG', 'Togo'),
('TH', 'Thaïlande'),
('TJ', 'Tajikistan'),
('TK', 'Iles Tokelau'),
('TM', 'Turkménistan'),
('TN', 'Tunisie'),
('TO', 'Tonga'),
('TP', 'Timor oriental'),
('TR', 'Turquie'),
('TT', 'Trinité-et-Tobago'),
('TV', 'Tuvalu (Iles)'),
('TW', 'Taiwan'),
('TZ', 'Tanzanie'),
('UA', 'Ukraine'),
('UG', 'Ouganda'),
('UK', 'Royaume-Uni'),
('UM', 'Dépendances américaines du Pacifique'),
('US', 'Etats-Unis'),
('UY', 'Uruguay'),
('UZ', 'Ouzbékist'),
('VA', 'Etat de la cité du Vatican'),
('VC', 'Saint-Vincent et les Grenadines'),
('VE', 'Venezuela'),
('VG', 'Iles Vierges britanniques'),
('VI', 'Iles Vierges américaines'),
('VN', 'Vietnam'),
('VU', 'Vanuatu (République de)'),
('WF', 'Wallis et Futuna'),
('WS', 'Samoa'),
('YE', 'Yémen'),
('YT', 'Mayotte'),
('YU', 'Yougoslavie'),
('ZA', 'Afrique du Sud'),
('ZM', 'Zambie'),
('ZW', 'Zimbabwe');
-- --------------------------------------------------------

-- 
-- Structure de la table `afup_personnes_morales`
-- 

DROP TABLE IF EXISTS `afup_personnes_morales`;
CREATE TABLE IF NOT EXISTS `afup_personnes_morales` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `civilite` varchar(4) NOT NULL default '',
  `nom` varchar(40) NOT NULL default '',
  `prenom` varchar(40) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `raison_sociale` varchar(100) NOT NULL default '',
  `siret` varchar(14) NOT NULL default '',
  `adresse` text NOT NULL,
  `code_postal` varchar(10) NOT NULL default '',
  `ville` varchar(50) NOT NULL default '',
  `id_pays` char(2) NOT NULL default '',
  `telephone_fixe` varchar(20) default NULL,
  `telephone_portable` varchar(20) default NULL,
  `etat` tinyint(3) unsigned NOT NULL default '0',
  `date_relance` int(11) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `pays` (`id_pays`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_personnes_morales`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_personnes_physiques`
-- 

DROP TABLE IF EXISTS `afup_personnes_physiques`;
CREATE TABLE IF NOT EXISTS `afup_personnes_physiques` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `id_personne_morale` smallint(5) unsigned default NULL,
  `login` varchar(30) NOT NULL default '',
  `mot_de_passe` varchar(32) NOT NULL default '',
  `niveau` tinyint(3) unsigned NOT NULL default '0',
  `niveau_modules` varchar(3) NOT NULL,
  `civilite` varchar(4) NOT NULL default '',
  `nom` varchar(40) NOT NULL default '',
  `prenom` varchar(40) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `adresse` text NOT NULL,
  `code_postal` varchar(10) NOT NULL default '',
  `ville` varchar(50) NOT NULL default '',
  `id_pays` char(2) NOT NULL default '',
  `telephone_fixe` varchar(20) default NULL,
  `telephone_portable` varchar(20) default NULL,
  `etat` tinyint(3) unsigned NOT NULL default '0',
  `date_relance` int(11) unsigned default NULL,
  `compte_svn` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  KEY `pays` (`id_pays`),
  KEY `personne_morale` (`id_personne_morale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_personnes_physiques`
-- 

INSERT INTO `afup_personnes_physiques` (`id`, `id_personne_morale`, `login`, `mot_de_passe`, `niveau`, `niveau_modules`, `civilite`, `nom`, `prenom`, `email`, `adresse`, `code_postal`, `ville`, `id_pays`, `telephone_fixe`, `telephone_portable`, `etat`, `date_relance`, `compte_svn`) VALUES (1, NULL, 'admin', '1a1dc91c907325c69271ddf0c944bc72', 2, '', 'Mlle', 'Ministrateur', 'Aude', 'aude@example.com', '3 rue du lac', '59000', 'Lille', 'FR', '03 20 01 02 03', '06 20 04 05 06', 1, NULL, NULL);

-- --------------------------------------------------------

-- 
-- Structure de la table `afup_planete_billet`
-- 

DROP TABLE IF EXISTS `afup_planete_billet`;
CREATE TABLE IF NOT EXISTS `afup_planete_billet` (
  `id` int(11) NOT NULL auto_increment,
  `afup_planete_flux_id` int(11) default NULL,
  `clef` varchar(255) default NULL,
  `titre` mediumtext,
  `url` varchar(255) default NULL,
  `maj` int(11) default NULL,
  `auteur` mediumtext,
  `resume` mediumtext,
  `contenu` mediumtext,
  `etat` tinyint(4) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_planete_billet`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_planete_flux`
-- 

DROP TABLE IF EXISTS `afup_planete_flux`;
CREATE TABLE IF NOT EXISTS `afup_planete_flux` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `feed` varchar(255) default NULL,
  `etat` tinyint(4) default NULL,
  `id_personne_physique` smallint(5) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_planete_flux`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_presences_assemblee_generale`
-- 

DROP TABLE IF EXISTS `afup_presences_assemblee_generale`;
CREATE TABLE IF NOT EXISTS `afup_presences_assemblee_generale` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `id_personne_physique` smallint(5) unsigned default NULL,
  `date` int(11) unsigned NOT NULL default '0',
  `presence` tinyint(1) unsigned NOT NULL default '0',
  `id_personne_avec_pouvoir` smallint(5) unsigned NOT NULL default '0',
  `date_consultation` int(11) unsigned default '0',
  `date_modification` int(11) unsigned default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_presences_assemblee_generale`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_rendezvous`
-- 

DROP TABLE IF EXISTS `afup_rendezvous`;
CREATE TABLE IF NOT EXISTS `afup_rendezvous` (
  `id` int(11) NOT NULL auto_increment,
  `titre` varchar(255) default NULL,
  `accroche` mediumtext,
  `theme` mediumtext,
  `debut` int(11) default NULL,
  `fin` int(11) default NULL,
  `lieu` varchar(255) default NULL,
  `url` varchar(255) NOT NULL,
  `plan` varchar(255) NOT NULL,
  `adresse` mediumtext NOT NULL,
  `capacite` mediumint(9) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_rendezvous`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_rendezvous_inscrits`
-- 

DROP TABLE IF EXISTS `afup_rendezvous_inscrits`;
CREATE TABLE IF NOT EXISTS `afup_rendezvous_inscrits` (
  `id` int(11) NOT NULL auto_increment,
  `id_rendezvous` int(11) default NULL,
  `nom` varchar(255) default NULL,
  `entreprise` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `telephone` varchar(255) default NULL,
  `presence` tinyint(4) default NULL,
  `confirme` tinyint(4) default '0',
  `creation` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_rendezvous_inscrits`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_sessions`
-- 

DROP TABLE IF EXISTS `afup_sessions`;
CREATE TABLE IF NOT EXISTS `afup_sessions` (
  `session_id` int(11) NOT NULL auto_increment,
  `id_forum` smallint(6) NOT NULL default '0',
  `date_soumission` date NOT NULL default '0000-00-00',
  `titre` varchar(255) NOT NULL default '',
  `abstract` text NOT NULL,
  `journee` tinyint(1) NOT NULL default '0',
  `genre` tinyint(1) NOT NULL default '1',
  `plannifie` tinyint(1) default NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_sessions`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_sessions_note`
-- 

DROP TABLE IF EXISTS `afup_sessions_note`;
CREATE TABLE IF NOT EXISTS `afup_sessions_note` (
  `session_id` int(11) NOT NULL,
  `note` tinyint(4) NOT NULL,
  `salt` char(32) NOT NULL,
  `date_soumission` date NOT NULL,
  PRIMARY KEY  USING BTREE (`note`,`session_id`,`salt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_sessions_note`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_sessions_vote`
-- 

DROP TABLE IF EXISTS `afup_sessions_vote`;
CREATE TABLE IF NOT EXISTS `afup_sessions_vote` (
  `id_personne_physique` int(11) NOT NULL,
  `id_session` int(11) NOT NULL,
  `a_vote` tinyint(1) default '0',
  PRIMARY KEY  (`id_session`,`id_personne_physique`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_sessions_vote`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_site_article`
-- 

DROP TABLE IF EXISTS `afup_site_article`;
CREATE TABLE IF NOT EXISTS `afup_site_article` (
  `id` int(11) NOT NULL auto_increment,
  `id_site_rubrique` int(11) default NULL,
  `surtitre` tinytext,
  `titre` tinytext,
  `raccourci` varchar(255) default NULL,
  `descriptif` mediumtext,
  `chapeau` mediumtext,
  `contenu` mediumtext,
  `position` mediumint(9) default NULL,
  `date` int(11) default NULL,
  `etat` tinyint(4) default NULL,
  `id_personne_physique` smallint(5) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_site_article`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_site_feuille`
-- 

DROP TABLE IF EXISTS `afup_site_feuille`;
CREATE TABLE IF NOT EXISTS `afup_site_feuille` (
  `id` int(11) NOT NULL auto_increment,
  `id_parent` int(11) default NULL,
  `nom` varchar(255) default NULL,
  `lien` varchar(255) default NULL,
  `alt` varchar(255) default NULL,
  `position` mediumint(9) default NULL,
  `date` int(11) default NULL,
  `etat` tinyint(4) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_site_feuille`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_site_rubrique`
-- 

DROP TABLE IF EXISTS `afup_site_rubrique`;
CREATE TABLE IF NOT EXISTS `afup_site_rubrique` (
  `id` int(11) NOT NULL auto_increment,
  `id_parent` int(11) default NULL,
  `nom` tinytext,
  `raccourci` varchar(255) default NULL,
  `contenu` mediumtext,
  `descriptif` tinytext,
  `position` mediumint(9) default NULL,
  `date` int(11) default NULL,
  `etat` tinyint(4) default NULL,
  `id_personne_physique` smallint(5) unsigned default NULL,
  `icone` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_site_rubrique`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_tags`
-- 

DROP TABLE IF EXISTS `afup_tags`;
CREATE TABLE IF NOT EXISTS `afup_tags` (
  `id` int(11) NOT NULL auto_increment,
  `source` varchar(255) default NULL,
  `id_source` int(11) default NULL,
  `tag` varchar(255) default NULL,
  `id_personne_physique` int(11) default NULL,
  `date` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_tags`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `afup_villes`
-- 

DROP TABLE IF EXISTS `afup_villes`;
CREATE TABLE IF NOT EXISTS `afup_villes` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `nom` char(50) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `afup_villes`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `annuairepro_activite`
-- 

DROP TABLE IF EXISTS `annuairepro_activite`;
CREATE TABLE IF NOT EXISTS `annuairepro_activite` (
  `ID` int(11) NOT NULL default '0',
  `Nom` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `annuairepro_activite`
-- 

INSERT INTO `annuairepro_activite` (`ID`, `Nom`) VALUES (1, 'DÃ©veloppement au forfait'),
(2, 'DÃ©veloppement en rÃ©gie'),
(3, 'Conseil / Architecture'),
(4, 'Formation'),
(5, 'Editeur (logiciels PHP et pour PHP)'),
(0, 'HÃ©bergement');

-- --------------------------------------------------------

-- 
-- Structure de la table `annuairepro_activitemembre`
-- 

DROP TABLE IF EXISTS `annuairepro_activitemembre`;
CREATE TABLE IF NOT EXISTS `annuairepro_activitemembre` (
  `Membre` int(11) NOT NULL default '0',
  `Activite` int(11) NOT NULL default '0',
  `EstPrincipale` enum('True','False') default NULL,
  UNIQUE KEY `Membre` (`Membre`,`Activite`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `annuairepro_activitemembre`
-- 

INSERT INTO `annuairepro_activitemembre` (`Membre`, `Activite`, `EstPrincipale`) VALUES (19, 5, 'True'),
(19, 1, 'False'),
(31, 1, 'True'),
(31, 2, 'False'),
(31, 5, 'False');

-- --------------------------------------------------------

-- 
-- Structure de la table `annuairepro_formejuridique`
-- 

DROP TABLE IF EXISTS `annuairepro_formejuridique`;
CREATE TABLE IF NOT EXISTS `annuairepro_formejuridique` (
  `ID` int(11) NOT NULL default '0',
  `Nom` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `annuairepro_formejuridique`
-- 

INSERT INTO `annuairepro_formejuridique` (`ID`, `Nom`) VALUES (1, 'Entreprise Individuelle'),
(2, 'Profession libÃ©rale'),
(3, 'EURL/SARL'),
(4, 'SA/SAS'),
(5, 'Association');

-- --------------------------------------------------------

-- 
-- Structure de la table `annuairepro_membreannuaire`
-- 

DROP TABLE IF EXISTS `annuairepro_membreannuaire`;
CREATE TABLE IF NOT EXISTS `annuairepro_membreannuaire` (
  `ID` int(11) NOT NULL auto_increment,
  `FormeJuridique` int(11) NOT NULL default '0',
  `RaisonSociale` varchar(255) default NULL,
  `SIREN` varchar(255) default NULL,
  `Email` varchar(255) default NULL,
  `SiteWeb` varchar(255) default NULL,
  `Telephone` varchar(20) default NULL,
  `Fax` varchar(20) default NULL,
  `Adresse` text,
  `CodePostal` varchar(5) default NULL,
  `Ville` varchar(255) default NULL,
  `Zone` int(11) NOT NULL default '0',
  `NumeroFormateur` varchar(255) default NULL,
  `MembreAFUP` tinyint(1) default NULL,
  `Valide` tinyint(1) default NULL,
  `DateCreation` datetime default NULL,
  `TailleSociete` int(11) NOT NULL default '0',
  `Password` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `RaisonSociale` (`RaisonSociale`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `annuairepro_membreannuaire`
-- 

INSERT INTO `annuairepro_membreannuaire` (`ID`, `FormeJuridique`, `RaisonSociale`, `SIREN`, `Email`, `SiteWeb`, `Telephone`, `Fax`, `Adresse`, `CodePostal`, `Ville`, `Zone`, `NumeroFormateur`, `MembreAFUP`, `Valide`, `DateCreation`, `TailleSociete`, `Password`) VALUES (31, 1, 'SimplementNet', '44489452100020', 'contact@simplementnet.com', 'http://www.simplementnet.com', '0 820 024 572', '0 820 024 572', '78, rue d\\''Amsterdam', '75009', 'Paris', 1, '', 0, 1, '2004-05-10 14:09:36', 2, 'saintmalo'),
(19, 3, 'No Parking', '452 488 596 00019', 'p.penet@noparking.net', 'http://www.noparking.net/', '0320065126', '--', '10 rue stappaert', '59000', 'Lille', 3, '', 1, 1, '2004-04-19 14:50:10', 2, 'FYSi6af');

-- --------------------------------------------------------

-- 
-- Structure de la table `annuairepro_membreannuaire_seq`
-- 

DROP TABLE IF EXISTS `annuairepro_membreannuaire_seq`;
CREATE TABLE IF NOT EXISTS `annuairepro_membreannuaire_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=773 DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `annuairepro_membreannuaire_seq`
-- 

INSERT INTO `annuairepro_membreannuaire_seq` (`id`) VALUES (772);

-- --------------------------------------------------------

-- 
-- Structure de la table `annuairepro_taillesociete`
-- 

DROP TABLE IF EXISTS `annuairepro_taillesociete`;
CREATE TABLE IF NOT EXISTS `annuairepro_taillesociete` (
  `ID` int(11) NOT NULL default '0',
  `Nom` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `annuairepro_taillesociete`
-- 

INSERT INTO `annuairepro_taillesociete` (`ID`, `Nom`) VALUES (1, 'Une personne'),
(2, 'Entre 2 et 5 personnes'),
(3, 'Entre 6 et 10 personnes'),
(4, 'Plus de 10 personnes');

-- --------------------------------------------------------

-- 
-- Structure de la table `annuairepro_zone`
-- 

DROP TABLE IF EXISTS `annuairepro_zone`;
CREATE TABLE IF NOT EXISTS `annuairepro_zone` (
  `ID` int(11) NOT NULL default '0',
  `Nom` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `annuairepro_zone`
-- 

INSERT INTO `annuairepro_zone` (`ID`, `Nom`) VALUES (1, '01 - Ile de France'),
(2, '02 - Nord Ouest'),
(3, '03 - Nord Est'),
(4, '04 - Sud Est'),
(5, '05 - Sud Ouest');
