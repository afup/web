-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 29 Novembre 2010 à 11:39
-- Version du serveur: 5.1.33
-- Version de PHP: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `afupcompta`
--

-- --------------------------------------------------------

--
-- Structure de la table `compta`
--

CREATE TABLE IF NOT EXISTS `compta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idclef` varchar(20) NOT NULL,
  `idoperation` tinyint(5) NOT NULL,
  `idcategorie` int(11) NOT NULL,
  `date_ecriture` date NOT NULL,
  `nom_frs` varchar(50) NOT NULL,
  `montant` double(11,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `idmode_regl` tinyint(5) NOT NULL,
  `date_regl` date NOT NULL,
  `obs_regl` varchar(255) NOT NULL,
  `idevenement` tinyint(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=450 ;

-- --------------------------------------------------------

--
-- Structure de la table `compta_categorie`
--

CREATE TABLE IF NOT EXISTS `compta_categorie` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `idevenement` int(11) NOT NULL,
  `categorie` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Structure de la table `compta_evenement`
--

CREATE TABLE IF NOT EXISTS `compta_evenement` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `evenement` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Structure de la table `compta_operation`
--

CREATE TABLE IF NOT EXISTS `compta_operation` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `operation` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `compta_periode`
--

CREATE TABLE IF NOT EXISTS `compta_periode` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `verouiller` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `compta_reglement`
--

CREATE TABLE IF NOT EXISTS `compta_reglement` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `reglement` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Structure de la table `compta_simulation`
--

CREATE TABLE IF NOT EXISTS `compta_simulation` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `idclef` varchar(20) NOT NULL,
  `idcategorie` int(11) NOT NULL,
  `montant_theo` double(11,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `idevenement` tinyint(5) NOT NULL,
  `idoperation` tinyint(5) NOT NULL,
  `periode` date NOT NULL,
  `verouiller` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;
