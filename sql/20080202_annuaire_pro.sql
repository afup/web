-- phpMyAdmin SQL Dump
-- version 2.6.2-Debian-3sarge6
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Feb 02, 2008 at 11:58 AM
-- Server version: 4.1.15
-- PHP Version: 5.2.5-0.dotdeb.0
-- 
-- Database: `afup`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `annuairepro_Activite`
-- 

DROP TABLE IF EXISTS `annuairepro_Activite`;
CREATE TABLE `annuairepro_Activite` (
  `ID` int(11) NOT NULL default '0',
  `Nom` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `annuairepro_Activite`
-- 

INSERT INTO `annuairepro_Activite` VALUES (1, 'Développement au forfait');
INSERT INTO `annuairepro_Activite` VALUES (2, 'Développement en régie');
INSERT INTO `annuairepro_Activite` VALUES (3, 'Conseil / Architecture');
INSERT INTO `annuairepro_Activite` VALUES (4, 'Formation');
INSERT INTO `annuairepro_Activite` VALUES (5, 'Editeur (logiciels PHP et pour PHP)');
INSERT INTO `annuairepro_Activite` VALUES (0, 'Hébergement');

-- --------------------------------------------------------

-- 
-- Table structure for table `annuairepro_ActiviteMembre`
-- 

DROP TABLE IF EXISTS `annuairepro_ActiviteMembre`;
CREATE TABLE `annuairepro_ActiviteMembre` (
  `Membre` int(11) NOT NULL default '0',
  `Activite` int(11) NOT NULL default '0',
  `EstPrincipale` enum('True','False') default NULL,
  UNIQUE KEY `Membre` (`Membre`,`Activite`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `annuairepro_ActiviteMembre`
-- 

INSERT INTO `annuairepro_ActiviteMembre` VALUES (19, 5, 'True');
INSERT INTO `annuairepro_ActiviteMembre` VALUES (19, 1, 'False');
INSERT INTO `annuairepro_ActiviteMembre` VALUES (31, 1, 'True');
INSERT INTO `annuairepro_ActiviteMembre` VALUES (31, 2, 'False');
INSERT INTO `annuairepro_ActiviteMembre` VALUES (31, 5, 'False');

-- --------------------------------------------------------

-- 
-- Table structure for table `annuairepro_FormeJuridique`
-- 

DROP TABLE IF EXISTS `annuairepro_FormeJuridique`;
CREATE TABLE `annuairepro_FormeJuridique` (
  `ID` int(11) NOT NULL default '0',
  `Nom` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `annuairepro_FormeJuridique`
-- 

INSERT INTO `annuairepro_FormeJuridique` VALUES (1, 'Entreprise Individuelle');
INSERT INTO `annuairepro_FormeJuridique` VALUES (2, 'Profession libérale');
INSERT INTO `annuairepro_FormeJuridique` VALUES (3, 'EURL/SARL');
INSERT INTO `annuairepro_FormeJuridique` VALUES (4, 'SA/SAS');
INSERT INTO `annuairepro_FormeJuridique` VALUES (5, 'Association');

-- --------------------------------------------------------

-- 
-- Table structure for table `annuairepro_MembreAnnuaire`
-- 

DROP TABLE IF EXISTS `annuairepro_MembreAnnuaire`;
CREATE TABLE `annuairepro_MembreAnnuaire` (
  `ID` int(11) NOT NULL default '0',
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
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `annuairepro_MembreAnnuaire`
-- 

INSERT INTO `annuairepro_MembreAnnuaire` VALUES (31, 1, 'SimplementNet', '44489452100020', 'contact@simplementnet.com', 'http://www.simplementnet.com', '0 820 024 572', '0 820 024 572', '78, rue d\\''Amsterdam', '75009', 'Paris', 1, '', 0, 1, '2004-05-10 14:09:36', 2, 'saintmalo');
INSERT INTO `annuairepro_MembreAnnuaire` VALUES (19, 3, 'No Parking', '452 488 596 00019', 'p.penet@noparking.net', 'http://www.noparking.net/', '0320065126', '--', '10 rue stappaert', '59000', 'Lille', 3, '', 1, 1, '2004-04-19 14:50:10', 2, 'FYSi6af');

-- --------------------------------------------------------

-- 
-- Table structure for table `annuairepro_MembreAnnuaire_seq`
-- 

DROP TABLE IF EXISTS `annuairepro_MembreAnnuaire_seq`;
CREATE TABLE `annuairepro_MembreAnnuaire_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=773 ;

-- 
-- Dumping data for table `annuairepro_MembreAnnuaire_seq`
-- 

INSERT INTO `annuairepro_MembreAnnuaire_seq` VALUES (772);

-- --------------------------------------------------------

-- 
-- Table structure for table `annuairepro_TailleSociete`
-- 

DROP TABLE IF EXISTS `annuairepro_TailleSociete`;
CREATE TABLE `annuairepro_TailleSociete` (
  `ID` int(11) NOT NULL default '0',
  `Nom` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `annuairepro_TailleSociete`
-- 

INSERT INTO `annuairepro_TailleSociete` VALUES (1, 'Une personne');
INSERT INTO `annuairepro_TailleSociete` VALUES (2, 'Entre 2 et 5 personnes');
INSERT INTO `annuairepro_TailleSociete` VALUES (3, 'Entre 6 et 10 personnes');
INSERT INTO `annuairepro_TailleSociete` VALUES (4, 'Plus de 10 personnes');

-- --------------------------------------------------------

-- 
-- Table structure for table `annuairepro_Zone`
-- 

DROP TABLE IF EXISTS `annuairepro_Zone`;
CREATE TABLE `annuairepro_Zone` (
  `ID` int(11) NOT NULL default '0',
  `Nom` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `annuairepro_Zone`
-- 

INSERT INTO `annuairepro_Zone` VALUES (1, '01 - Ile de France');
INSERT INTO `annuairepro_Zone` VALUES (2, '02 - Nord Ouest');
INSERT INTO `annuairepro_Zone` VALUES (3, '03 - Nord Est');
INSERT INTO `annuairepro_Zone` VALUES (4, '04 - Sud Est');
INSERT INTO `annuairepro_Zone` VALUES (5, '05 - Sud Ouest');
