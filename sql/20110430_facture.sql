-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mer 18 Mai 2011 à 06:49
-- Version du serveur: 5.5.8
-- Version de PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `afup`
--

-- --------------------------------------------------------

--
-- Structure de la table `afup_compta_facture`
--

CREATE TABLE IF NOT EXISTS `afup_compta_facture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_devis` date NOT NULL,
  `numero_devis` varchar(50) NOT NULL,
  `date_facture` date NOT NULL,
  `numero_facture` varchar(50) NOT NULL,
  `societe` varchar(50) NOT NULL,
  `service` varchar(50) NOT NULL,
  `adresse` text NOT NULL,
  `code_postal` varchar(10) NOT NULL,
  `ville` varchar(50) NOT NULL,
  `id_pays` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `observation` text NOT NULL,
  `ref_clt1` varchar(50) NOT NULL,
  `ref_clt2` varchar(50) NOT NULL,
  `ref_clt3` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `tel` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `afup_compta_facture`
--


-- --------------------------------------------------------

--
-- Structure de la table `afup_compta_facture_details`
--

CREATE TABLE IF NOT EXISTS `afup_compta_facture_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idafup_compta_facture` int(11) NOT NULL,
  `ref` varchar(20) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `quantite` double(11,2) NOT NULL,
  `pu` double(11,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `afup_compta_facture_details`
--

