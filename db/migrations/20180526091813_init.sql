-- MySQL dump 10.15  Distrib 10.0.28-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 217.70.189.71    Database: 217.70.189.71
-- ------------------------------------------------------
-- Server version	10.0.28-MariaDB-1~jessie

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ZZZ__annu_pro`
--

DROP TABLE IF EXISTS `ZZZ__annu_pro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ZZZ__annu_pro` (
  `forme_juridique` varchar(5) NOT NULL DEFAULT '',
  `raison_sociale` varchar(50) NOT NULL DEFAULT '',
  `siret` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `site` varchar(60) DEFAULT NULL,
  `tel` varchar(30) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `adresse1` varchar(50) NOT NULL DEFAULT '',
  `adresse2` varchar(50) DEFAULT NULL,
  `cp` varchar(10) NOT NULL DEFAULT '',
  `ville` varchar(30) NOT NULL DEFAULT '',
  `pays` varchar(20) NOT NULL DEFAULT '',
  `heb` char(2) DEFAULT NULL,
  `forfait` char(2) DEFAULT NULL,
  `regie` char(2) DEFAULT NULL,
  `formation` char(2) DEFAULT NULL,
  `conseil` char(2) DEFAULT NULL,
  `STATUS` varchar(10) NOT NULL DEFAULT '',
  UNIQUE KEY `raison_sociale` (`raison_sociale`),
  KEY `nom` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ZZZ__forum2004_inscription`
--

DROP TABLE IF EXISTS `ZZZ__forum2004_inscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ZZZ__forum2004_inscription` (
  `civilite` varchar(4) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(80) NOT NULL DEFAULT '',
  `prenom` varchar(80) NOT NULL DEFAULT '',
  `compagnie` varchar(120) NOT NULL DEFAULT '',
  `email` varchar(120) NOT NULL DEFAULT '',
  `web` varchar(200) NOT NULL DEFAULT '',
  `adresse` varchar(255) NOT NULL DEFAULT '',
  `codepostal` varchar(5) NOT NULL DEFAULT '',
  `ville` varchar(50) NOT NULL DEFAULT '',
  `etat` varchar(50) NOT NULL DEFAULT '',
  `pays` varchar(50) NOT NULL DEFAULT '',
  `achat` varchar(20) NOT NULL DEFAULT '',
  `montant` float NOT NULL DEFAULT '0',
  `creation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modification` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `visibilite` enum('oui','non') NOT NULL DEFAULT 'oui',
  `afup` enum('oui','non') NOT NULL DEFAULT 'oui',
  `nexen` enum('oui','non') NOT NULL DEFAULT 'oui',
  `statut` enum('creation','paye','refuse','annule','erreur') NOT NULL DEFAULT 'creation',
  `commande` varchar(30) NOT NULL DEFAULT '',
  `autorisation` varchar(10) NOT NULL DEFAULT '',
  `transaction` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=229 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ZZZ__forum2005_inscription`
--

DROP TABLE IF EXISTS `ZZZ__forum2005_inscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ZZZ__forum2005_inscription` (
  `civilite` varchar(4) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(80) NOT NULL DEFAULT '',
  `prenom` varchar(80) NOT NULL DEFAULT '',
  `compagnie` varchar(120) NOT NULL DEFAULT '',
  `email` varchar(120) NOT NULL DEFAULT '',
  `web` varchar(200) NOT NULL DEFAULT '',
  `adresse` varchar(255) NOT NULL DEFAULT '',
  `codepostal` varchar(5) NOT NULL DEFAULT '',
  `ville` varchar(50) NOT NULL DEFAULT '',
  `etat` varchar(50) NOT NULL DEFAULT '',
  `pays` varchar(50) NOT NULL DEFAULT '',
  `achat` varchar(20) NOT NULL DEFAULT '',
  `montant` float NOT NULL DEFAULT '0',
  `creation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modification` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `visibilite` enum('oui','non') NOT NULL DEFAULT 'oui',
  `afup` enum('oui','non') NOT NULL DEFAULT 'oui',
  `nexen` enum('oui','non') NOT NULL DEFAULT 'oui',
  `statut` enum('creation','paye','refuse','annule','erreur') NOT NULL DEFAULT 'creation',
  `commande` varchar(30) NOT NULL DEFAULT '',
  `autorisation` varchar(10) NOT NULL DEFAULT '',
  `transaction` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ZZZ__forumphp`
--

DROP TABLE IF EXISTS `ZZZ__forumphp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ZZZ__forumphp` (
  `id` tinyint(11) NOT NULL AUTO_INCREMENT,
  `societe` varchar(50) DEFAULT NULL,
  `prenom` varchar(25) DEFAULT NULL,
  `nom` varchar(25) DEFAULT NULL,
  `tel` varchar(15) DEFAULT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  `media` varchar(15) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `citation` char(3) DEFAULT NULL,
  PRIMARY KEY (`id`,`email`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `email_2` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_accreditation_presse`
--

DROP TABLE IF EXISTS `afup_accreditation_presse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_accreditation_presse` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(11) unsigned NOT NULL DEFAULT '0',
  `titre_revue` varchar(255) NOT NULL DEFAULT '',
  `civilite` varchar(4) NOT NULL DEFAULT '',
  `nom` varchar(40) NOT NULL DEFAULT '',
  `prenom` varchar(40) NOT NULL DEFAULT '',
  `carte_presse` varchar(50) NOT NULL DEFAULT '',
  `adresse` text NOT NULL,
  `code_postal` varchar(10) NOT NULL DEFAULT '',
  `ville` varchar(50) NOT NULL DEFAULT '',
  `id_pays` char(2) NOT NULL DEFAULT '',
  `telephone` varchar(40) DEFAULT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `commentaires` text,
  `id_forum` smallint(6) NOT NULL DEFAULT '0',
  `valide` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_forum` (`id_forum`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COMMENT='Accreditation presse';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_antenne`
--

DROP TABLE IF EXISTS `afup_antenne`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_antenne` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ville` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_blacklist`
--

DROP TABLE IF EXISTS `afup_blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_compta_facture`
--

DROP TABLE IF EXISTS `afup_compta_facture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_compta_facture` (
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
  `etat_paiement` int(11) NOT NULL DEFAULT '0',
  `date_paiement` date DEFAULT NULL,
  `devise_facture` enum('EUR','DOL') DEFAULT 'EUR',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=488 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_compta_facture_details`
--

DROP TABLE IF EXISTS `afup_compta_facture_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_compta_facture_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idafup_compta_facture` int(11) NOT NULL,
  `ref` varchar(20) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `quantite` double(11,2) NOT NULL,
  `pu` double(11,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2461 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_conferenciers`
--

DROP TABLE IF EXISTS `afup_conferenciers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_conferenciers` (
  `conferencier_id` int(11) NOT NULL AUTO_INCREMENT,
  `id_forum` smallint(6) NOT NULL DEFAULT '0',
  `civilite` varchar(5) DEFAULT '',
  `nom` varchar(70) DEFAULT '',
  `prenom` varchar(50) DEFAULT '',
  `email` varchar(65) DEFAULT '',
  `societe` varchar(120) DEFAULT NULL,
  `biographie` text,
  `twitter` varchar(255) DEFAULT NULL,
  `user_github` int(10) unsigned DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`conferencier_id`),
  KEY `id_forum` (`id_forum`)
) ENGINE=MyISAM AUTO_INCREMENT=1886 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_conferenciers_sessions`
--

DROP TABLE IF EXISTS `afup_conferenciers_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_conferenciers_sessions` (
  `session_id` int(11) NOT NULL,
  `conferencier_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`,`conferencier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2000 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_contacts`
--

DROP TABLE IF EXISTS `afup_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_contacts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `organisation` varchar(255) NOT NULL,
  `poste` varchar(255) NOT NULL,
  `type` enum('ssii','agence web','grand compte','presse','projet','prof','sponsor','presse NPDC''') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=432 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_cotisations`
--

DROP TABLE IF EXISTS `afup_cotisations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_cotisations` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `date_debut` int(11) unsigned NOT NULL DEFAULT '0',
  `type_personne` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `id_personne` smallint(5) unsigned NOT NULL DEFAULT '0',
  `montant` float(5,2) unsigned NOT NULL DEFAULT '0.00',
  `type_reglement` tinyint(3) unsigned DEFAULT '0',
  `informations_reglement` varchar(255) DEFAULT NULL,
  `date_fin` int(11) unsigned NOT NULL DEFAULT '0',
  `numero_facture` varchar(15) NOT NULL DEFAULT '',
  `commentaires` text,
  `token` varchar(255) DEFAULT NULL,
  `nombre_relances` tinyint(3) unsigned DEFAULT NULL,
  `date_derniere_relance` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_personne` (`id_personne`)
) ENGINE=MyISAM AUTO_INCREMENT=2993 DEFAULT CHARSET=latin1 COMMENT='Cotisation des personnes physiques et morales';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_email`
--

DROP TABLE IF EXISTS `afup_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_email` (
  `email` varchar(128) NOT NULL DEFAULT '',
  `blacklist` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`email`),
  KEY `email` (`email`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_facturation_forum`
--

DROP TABLE IF EXISTS `afup_facturation_forum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_facturation_forum` (
  `reference` varchar(255) NOT NULL DEFAULT '',
  `montant` float NOT NULL DEFAULT '0',
  `date_reglement` int(11) unsigned DEFAULT NULL,
  `type_reglement` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `informations_reglement` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `societe` varchar(40) DEFAULT NULL,
  `nom` varchar(40) DEFAULT NULL,
  `prenom` varchar(40) DEFAULT NULL,
  `adresse` text NOT NULL,
  `code_postal` varchar(10) NOT NULL DEFAULT '',
  `ville` varchar(50) NOT NULL DEFAULT '',
  `id_pays` char(2) NOT NULL DEFAULT '',
  `autorisation` varchar(20) DEFAULT NULL,
  `transaction` varchar(20) DEFAULT NULL,
  `etat` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `facturation` tinyint(4) NOT NULL DEFAULT '0',
  `id_forum` smallint(6) NOT NULL DEFAULT '0',
  `date_facture` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`reference`),
  KEY `id_pays` (`id_pays`),
  KEY `id_forum` (`id_forum`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Facturation pour le forum PHP';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_forum`
--

DROP TABLE IF EXISTS `afup_forum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_forum` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `titre` varchar(50) NOT NULL DEFAULT '',
  `path` varchar(100) DEFAULT NULL,
  `trello_list_id` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `logo_url` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `nb_places` int(11) unsigned NOT NULL DEFAULT '0',
  `date_debut` date NULL,
  `date_fin` date NULL,
  `annee` int(11) DEFAULT NULL,
  `text` text,
  `date_fin_appel_projet` int(11) DEFAULT NULL,
  `date_fin_appel_conferencier` int(11) DEFAULT NULL,
  `date_fin_vote` datetime DEFAULT NULL,
  `date_fin_prevente` int(11) DEFAULT NULL,
  `date_fin_vente` int(11) DEFAULT NULL,
  `place_name` varchar(255) DEFAULT NULL,
  `place_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_forum_coupon`
--

DROP TABLE IF EXISTS `afup_forum_coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_forum_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_forum` int(11) NOT NULL,
  `texte` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=585 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_forum_partenaires`
--

DROP TABLE IF EXISTS `afup_forum_partenaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_forum_partenaires` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_forum` int(11) NOT NULL,
  `id_niveau_partenariat` int(11) NOT NULL,
  `ranking` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `presentation` text,
  `logo` varchar(100) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_forum_planning`
--

DROP TABLE IF EXISTS `afup_forum_planning`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_forum_planning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_session` int(11) DEFAULT NULL,
  `debut` int(10) DEFAULT NULL,
  `fin` int(10) DEFAULT NULL,
  `id_salle` smallint(4) DEFAULT NULL,
  `id_forum` int(11) DEFAULT NULL,
  `keynote` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=664 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_forum_salle`
--

DROP TABLE IF EXISTS `afup_forum_salle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_forum_salle` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `id_forum` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_forum_sessions_commentaires`
--

DROP TABLE IF EXISTS `afup_forum_sessions_commentaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_forum_sessions_commentaires` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_session` int(11) DEFAULT NULL,
  `id_personne_physique` int(11) DEFAULT NULL,
  `commentaire` mediumtext,
  `date` int(10) DEFAULT NULL,
  `public` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2029 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_forum_special_price`
--

DROP TABLE IF EXISTS `afup_forum_special_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_forum_special_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_event` int(10) unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `price` float DEFAULT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL,
  `creator_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_forum_sponsors_tickets`
--

DROP TABLE IF EXISTS `afup_forum_sponsors_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_forum_sponsors_tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `max_invitations` tinyint(3) unsigned NOT NULL,
  `used_invitations` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `id_forum` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `creator_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_forum_tarif`
--

DROP TABLE IF EXISTS `afup_forum_tarif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_forum_tarif` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `technical_name` varchar(64) NOT NULL,
  `pretty_name` varchar(255) NOT NULL,
  `public` tinyint(1) unsigned NOT NULL,
  `members_only` tinyint(1) unsigned NOT NULL,
  `default_price` float NOT NULL,
  `active` tinyint(1) NOT NULL,
  `day` set('one','two') NOT NULL,
  `cfp_submitter_only` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_forum_tarif_event`
--

DROP TABLE IF EXISTS `afup_forum_tarif_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_forum_tarif_event` (
  `id_tarif` int(10) unsigned NOT NULL,
  `id_event` int(10) unsigned NOT NULL,
  `price` float DEFAULT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id_tarif`,`id_event`),
  KEY `id_event` (`id_event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_inscription_forum`
--

DROP TABLE IF EXISTS `afup_inscription_forum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_inscription_forum` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(11) unsigned NOT NULL DEFAULT '0',
  `reference` varchar(255) NOT NULL DEFAULT '',
  `coupon` varchar(255) DEFAULT '',
  `type_inscription` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `montant` float NOT NULL DEFAULT '0',
  `informations_reglement` varchar(255) DEFAULT NULL,
  `civilite` varchar(4) NOT NULL DEFAULT '',
  `nom` varchar(40) NOT NULL DEFAULT '',
  `prenom` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `telephone` varchar(40) DEFAULT NULL,
  `citer_societe` tinyint(1) unsigned DEFAULT '0',
  `newsletter_afup` tinyint(1) unsigned DEFAULT '0',
  `newsletter_nexen` tinyint(1) unsigned DEFAULT '0',
  `commentaires` text,
  `etat` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `facturation` tinyint(4) NOT NULL DEFAULT '0',
  `id_forum` smallint(6) NOT NULL DEFAULT '0',
  `id_member` int(10) unsigned DEFAULT NULL,
  `member_type` int(10) unsigned DEFAULT NULL,
  `special_price_token` varchar(255) DEFAULT NULL,
  `mobilite_reduite` tinyint(1) NOT NULL DEFAULT '0',
  `mail_partenaire` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `presence_day1` tinyint(1) DEFAULT NULL,
  `presence_day2` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_forum` (`id_forum`),
  KEY `reference` (`reference`)
) ENGINE=InnoDB AUTO_INCREMENT=7533 DEFAULT CHARSET=latin1 COMMENT='Inscriptions au forum PHP';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_inscriptions_rappels`
--

DROP TABLE IF EXISTS `afup_inscriptions_rappels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_inscriptions_rappels` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `date` int(10) NOT NULL DEFAULT '0',
  `id_forum` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1497 DEFAULT CHARSET=latin1 COMMENT='Emails pour le rappel du forum PHP';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_logs`
--

DROP TABLE IF EXISTS `afup_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_logs` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(11) unsigned NOT NULL DEFAULT '0',
  `id_personne_physique` smallint(5) unsigned NOT NULL DEFAULT '0',
  `texte` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_personne_physique` (`id_personne_physique`)
) ENGINE=MyISAM AUTO_INCREMENT=213946 DEFAULT CHARSET=latin1 COMMENT='Logs des actions';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_mailing_lists`
--

DROP TABLE IF EXISTS `afup_mailing_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_mailing_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `members_only` tinyint(1) unsigned NOT NULL,
  `category` varchar(12) NOT NULL,
  `auto_registration` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_niveau_partenariat`
--

DROP TABLE IF EXISTS `afup_niveau_partenariat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_niveau_partenariat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_oeuvres`
--

DROP TABLE IF EXISTS `afup_oeuvres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_oeuvres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_personne_physique` smallint(5) unsigned DEFAULT NULL,
  `categorie` varchar(255) DEFAULT NULL,
  `valeur` smallint(5) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3449 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_pays`
--

DROP TABLE IF EXISTS `afup_pays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_pays` (
  `id` char(2) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL DEFAULT '',
  `nom` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Pays';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_personnes_morales`
--

DROP TABLE IF EXISTS `afup_personnes_morales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_personnes_morales` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `civilite` varchar(4) NOT NULL DEFAULT '',
  `nom` varchar(40) NOT NULL DEFAULT '',
  `prenom` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `raison_sociale` varchar(100) NOT NULL DEFAULT '',
  `siret` varchar(14) NOT NULL DEFAULT '',
  `adresse` text NOT NULL,
  `code_postal` varchar(10) NOT NULL DEFAULT '',
  `ville` varchar(50) NOT NULL DEFAULT '',
  `id_pays` char(2) NOT NULL DEFAULT '',
  `telephone_fixe` varchar(20) DEFAULT NULL,
  `telephone_portable` varchar(20) DEFAULT NULL,
  `max_members` tinyint(1) unsigned DEFAULT NULL COMMENT 'Nombre maximum de membre autorisé par la cotisation',
  `etat` tinyint(3) NOT NULL DEFAULT '-1',
  `date_relance` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pays` (`id_pays`)
) ENGINE=MyISAM AUTO_INCREMENT=354 DEFAULT CHARSET=latin1 COMMENT='Personnes morales';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_personnes_morales_invitations`
--

DROP TABLE IF EXISTS `afup_personnes_morales_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_personnes_morales_invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `manager` tinyint(1) unsigned NOT NULL,
  `submitted_on` datetime NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=248 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_personnes_physiques`
--

DROP TABLE IF EXISTS `afup_personnes_physiques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_personnes_physiques` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_personne_morale` smallint(5) unsigned NOT NULL DEFAULT '0',
  `login` varchar(30) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `mot_de_passe` varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `niveau` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `niveau_modules` char(10) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `roles` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `civilite` varchar(4) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `nom` varchar(40) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `prenom` varchar(40) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `adresse` text COLLATE latin1_general_ci NOT NULL,
  `code_postal` varchar(10) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `ville` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `id_pays` char(2) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `telephone_fixe` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `telephone_portable` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `etat` tinyint(3) NOT NULL DEFAULT '-1',
  `date_relance` int(11) unsigned DEFAULT NULL,
  `compte_svn` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `nearest_office` varchar(45) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email_unique` (`email`),
  KEY `pays` (`id_pays`),
  KEY `personne_morale` (`id_personne_morale`),
  KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2326 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Personnes physiques';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_planete_billet`
--

DROP TABLE IF EXISTS `afup_planete_billet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_planete_billet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `afup_planete_flux_id` int(11) DEFAULT NULL,
  `clef` varchar(255) DEFAULT NULL,
  `titre` mediumtext,
  `url` varchar(255) DEFAULT NULL,
  `maj` int(11) DEFAULT NULL,
  `auteur` mediumtext,
  `resume` mediumtext,
  `contenu` mediumtext,
  `etat` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12963 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_planete_flux`
--

DROP TABLE IF EXISTS `afup_planete_flux`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_planete_flux` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `feed` varchar(255) DEFAULT NULL,
  `etat` tinyint(4) DEFAULT NULL,
  `id_personne_physique` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=148 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_presences_assemblee_generale`
--

DROP TABLE IF EXISTS `afup_presences_assemblee_generale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_presences_assemblee_generale` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_personne_physique` smallint(5) unsigned DEFAULT NULL,
  `date` int(11) unsigned NOT NULL DEFAULT '0',
  `presence` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_personne_avec_pouvoir` smallint(5) unsigned NOT NULL DEFAULT '0',
  `date_consultation` int(11) unsigned DEFAULT '0',
  `date_modification` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5562 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_rendezvous`
--

DROP TABLE IF EXISTS `afup_rendezvous`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_rendezvous` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) DEFAULT NULL,
  `accroche` mediumtext,
  `theme` mediumtext,
  `debut` int(11) DEFAULT NULL,
  `fin` int(11) DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `plan` varchar(255) NOT NULL DEFAULT '',
  `adresse` mediumtext NOT NULL,
  `capacite` mediumint(9) DEFAULT NULL,
  `id_antenne` int(11) NOT NULL,
  `inscription` tinyint(1) NOT NULL DEFAULT '1',
  `url_externe` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_rendezvous_inscrits`
--

DROP TABLE IF EXISTS `afup_rendezvous_inscrits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_rendezvous_inscrits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_rendezvous` int(11) DEFAULT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(100) NOT NULL,
  `entreprise` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `presence` tinyint(4) DEFAULT NULL,
  `confirme` tinyint(4) DEFAULT '0',
  `creation` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1917 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_rendezvous_slides`
--

DROP TABLE IF EXISTS `afup_rendezvous_slides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_rendezvous_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_rendezvous` int(11) NOT NULL,
  `fichier` int(255) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_sessions`
--

DROP TABLE IF EXISTS `afup_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_sessions` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `id_forum` smallint(6) NOT NULL DEFAULT '0',
  `date_soumission` date NOT NULL,
  `titre` varchar(255) NOT NULL DEFAULT '',
  `abstract` text NOT NULL,
  `staff_notes` text,
  `journee` tinyint(1) NOT NULL DEFAULT '0',
  `genre` tinyint(1) NOT NULL DEFAULT '1',
  `skill` tinyint(1) NOT NULL,
  `plannifie` tinyint(1) DEFAULT NULL,
  `needs_mentoring` tinyint(1) NOT NULL DEFAULT '0',
  `youtube_id` varchar(30) DEFAULT NULL,
  `slides_url` varchar(255) DEFAULT NULL,
  `blog_post_url` varchar(255) DEFAULT NULL,
  `language_code` varchar(2) DEFAULT 'fr',
  `markdown` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `joindin` int(11) DEFAULT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2662 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_sessions_invitation`
--

DROP TABLE IF EXISTS `afup_sessions_invitation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_sessions_invitation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `talk_id` int(11) NOT NULL,
  `state` tinyint(3) unsigned NOT NULL,
  `submitted_on` datetime NOT NULL,
  `submitted_by` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `talk_id_email` (`talk_id`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_sessions_note`
--

DROP TABLE IF EXISTS `afup_sessions_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_sessions_note` (
  `session_id` int(11) NOT NULL DEFAULT '0',
  `note` tinyint(4) NOT NULL DEFAULT '0',
  `salt` char(32) NOT NULL DEFAULT '',
  `date_soumission` date NOT NULL,
  PRIMARY KEY (`note`,`session_id`,`salt`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_sessions_vote`
--

DROP TABLE IF EXISTS `afup_sessions_vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_sessions_vote` (
  `id_personne_physique` int(11) NOT NULL DEFAULT '0',
  `id_session` int(11) NOT NULL DEFAULT '0',
  `a_vote` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_session`,`id_personne_physique`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_sessions_vote_github`
--

DROP TABLE IF EXISTS `afup_sessions_vote_github`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_sessions_vote_github` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(10) unsigned NOT NULL,
  `user` int(10) unsigned NOT NULL,
  `comment` text,
  `vote` tinyint(3) unsigned NOT NULL,
  `submitted_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8119 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_site_article`
--

DROP TABLE IF EXISTS `afup_site_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_site_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_site_rubrique` int(11) DEFAULT NULL,
  `surtitre` tinytext,
  `titre` tinytext,
  `raccourci` varchar(255) DEFAULT NULL,
  `descriptif` mediumtext,
  `chapeau` mediumtext,
  `contenu` mediumtext,
  `position` mediumint(9) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `etat` tinyint(4) DEFAULT NULL,
  `id_personne_physique` smallint(5) unsigned DEFAULT NULL,
  `theme` int(11) DEFAULT NULL,
  `id_forum` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=999 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_site_feuille`
--

DROP TABLE IF EXISTS `afup_site_feuille`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_site_feuille` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) DEFAULT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `lien` varchar(255) DEFAULT NULL,
  `alt` varchar(255) DEFAULT NULL,
  `position` mediumint(9) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `etat` tinyint(4) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `patterns` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_site_rubrique`
--

DROP TABLE IF EXISTS `afup_site_rubrique`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_site_rubrique` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) DEFAULT NULL,
  `nom` tinytext,
  `raccourci` varchar(255) DEFAULT NULL,
  `contenu` mediumtext,
  `descriptif` tinytext,
  `position` mediumint(9) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `etat` tinyint(4) DEFAULT NULL,
  `id_personne_physique` smallint(5) unsigned DEFAULT NULL,
  `icone` varchar(255) DEFAULT NULL,
  `pagination` smallint(6) NOT NULL DEFAULT '0',
  `feuille_associee` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_speaker_suggestion`
--

DROP TABLE IF EXISTS `afup_speaker_suggestion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_speaker_suggestion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `suggester_email` varchar(255) NOT NULL,
  `suggester_name` varchar(255) NOT NULL,
  `speaker_name` varchar(255) NOT NULL,
  `comment` text,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_subscription_reminder_log`
--

DROP TABLE IF EXISTS `afup_subscription_reminder_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_subscription_reminder_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_type` tinyint(3) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `reminder_key` varchar(30) NOT NULL,
  `reminder_date` datetime NOT NULL,
  `mail_sent` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1142 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_tags`
--

DROP TABLE IF EXISTS `afup_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(255) DEFAULT NULL,
  `id_source` int(11) DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL,
  `id_personne_physique` int(11) DEFAULT NULL,
  `date` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `source` (`source`,`id_source`,`tag`)
) ENGINE=MyISAM AUTO_INCREMENT=3818 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_techletter`
--

DROP TABLE IF EXISTS `afup_techletter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_techletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sending_date` datetime NOT NULL,
  `techletter` text,
  `sent_to_mailchimp` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_techletter_subscriptions`
--

DROP TABLE IF EXISTS `afup_techletter_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_techletter_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `subscription_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_techletter_unsubscriptions`
--

DROP TABLE IF EXISTS `afup_techletter_unsubscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_techletter_unsubscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `unsubscription_date` datetime NOT NULL,
  `reason` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `mailchimp_id` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_throttling`
--

DROP TABLE IF EXISTS `afup_throttling`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_throttling` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(64) NOT NULL,
  `object_id` int(10) unsigned DEFAULT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_user_github`
--

DROP TABLE IF EXISTS `afup_user_github`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_user_github` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `github_id` int(10) unsigned NOT NULL,
  `login` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `profile_url` varchar(255) NOT NULL,
  `avatar_url` varchar(255) NOT NULL,
  `afup_crew` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1038 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_votes`
--

DROP TABLE IF EXISTS `afup_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` mediumtext,
  `lancement` int(11) DEFAULT '0',
  `cloture` int(11) DEFAULT '0',
  `date` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `afup_votes_poids`
--

DROP TABLE IF EXISTS `afup_votes_poids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `afup_votes_poids` (
  `id_vote` int(11) NOT NULL DEFAULT '0',
  `id_personne_physique` int(11) NOT NULL DEFAULT '0',
  `commentaire` mediumtext,
  `poids` tinyint(4) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  UNIQUE KEY `id_vote` (`id_vote`,`id_personne_physique`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `annuairepro_Activite`
--

DROP TABLE IF EXISTS `annuairepro_Activite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annuairepro_Activite` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `Nom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `annuairepro_ActiviteMembre`
--

DROP TABLE IF EXISTS `annuairepro_ActiviteMembre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annuairepro_ActiviteMembre` (
  `Membre` int(11) NOT NULL DEFAULT '0',
  `Activite` int(11) NOT NULL DEFAULT '0',
  `EstPrincipale` enum('True','False') DEFAULT NULL,
  UNIQUE KEY `Membre` (`Membre`,`Activite`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `annuairepro_FormeJuridique`
--

DROP TABLE IF EXISTS `annuairepro_FormeJuridique`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annuairepro_FormeJuridique` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `Nom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `annuairepro_MembreAnnuaire`
--

DROP TABLE IF EXISTS `annuairepro_MembreAnnuaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annuairepro_MembreAnnuaire` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FormeJuridique` int(11) NOT NULL DEFAULT '0',
  `RaisonSociale` varchar(255) DEFAULT NULL,
  `SIREN` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `SiteWeb` varchar(255) DEFAULT NULL,
  `Telephone` varchar(20) DEFAULT NULL,
  `Fax` varchar(20) DEFAULT NULL,
  `Adresse` text,
  `CodePostal` varchar(5) DEFAULT NULL,
  `Ville` varchar(255) DEFAULT NULL,
  `Zone` int(11) NOT NULL DEFAULT '0',
  `id_pays` varchar(2) NOT NULL,
  `NumeroFormateur` varchar(255) DEFAULT NULL,
  `MembreAFUP` tinyint(1) DEFAULT NULL,
  `Valide` tinyint(1) DEFAULT NULL,
  `DateCreation` datetime DEFAULT NULL,
  `TailleSociete` int(11) NOT NULL DEFAULT '0',
  `Password` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `RaisonSociale` (`RaisonSociale`)
) ENGINE=MyISAM AUTO_INCREMENT=901 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `annuairepro_MembreAnnuaire_iso`
--

DROP TABLE IF EXISTS `annuairepro_MembreAnnuaire_iso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annuairepro_MembreAnnuaire_iso` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FormeJuridique` int(11) NOT NULL DEFAULT '0',
  `RaisonSociale` varchar(255) DEFAULT NULL,
  `SIREN` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `SiteWeb` varchar(255) DEFAULT NULL,
  `Telephone` varchar(20) DEFAULT NULL,
  `Fax` varchar(20) DEFAULT NULL,
  `Adresse` text,
  `CodePostal` varchar(5) DEFAULT NULL,
  `Ville` varchar(255) DEFAULT NULL,
  `Zone` int(11) NOT NULL DEFAULT '0',
  `NumeroFormateur` varchar(255) DEFAULT NULL,
  `MembreAFUP` tinyint(1) DEFAULT NULL,
  `Valide` tinyint(1) DEFAULT NULL,
  `DateCreation` datetime DEFAULT NULL,
  `TailleSociete` int(11) NOT NULL DEFAULT '0',
  `Password` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `RaisonSociale` (`RaisonSociale`)
) ENGINE=MyISAM AUTO_INCREMENT=701 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `annuairepro_MembreAnnuaire_seq`
--

DROP TABLE IF EXISTS `annuairepro_MembreAnnuaire_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annuairepro_MembreAnnuaire_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=773 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `annuairepro_TailleSociete`
--

DROP TABLE IF EXISTS `annuairepro_TailleSociete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annuairepro_TailleSociete` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `Nom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `annuairepro_Zone`
--

DROP TABLE IF EXISTS `annuairepro_Zone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annuairepro_Zone` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `Nom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compta`
--

DROP TABLE IF EXISTS `compta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idclef` varchar(20) NOT NULL,
  `idoperation` tinyint(5) NOT NULL,
  `idcategorie` int(11) NOT NULL,
  `date_ecriture` date NOT NULL,
  `numero_operation` varchar(100) DEFAULT NULL,
  `nom_frs` varchar(50) NOT NULL,
  `montant` double(11,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `attachment_required` tinyint(1) DEFAULT '0',
  `attachment_filename` varchar(255) DEFAULT NULL,
  `numero` varchar(50) NOT NULL,
  `idmode_regl` tinyint(5) NOT NULL,
  `date_regl` date NULL,
  `obs_regl` varchar(255) NOT NULL,
  `idevenement` tinyint(5) NOT NULL,
  `idcompte` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6197 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compta_categorie`
--

DROP TABLE IF EXISTS `compta_categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compta_categorie` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `idevenement` int(11) NOT NULL,
  `categorie` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compta_compte`
--

DROP TABLE IF EXISTS `compta_compte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compta_compte` (
  `id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `nom_compte` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compta_evenement`
--

DROP TABLE IF EXISTS `compta_evenement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compta_evenement` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `evenement` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compta_operation`
--

DROP TABLE IF EXISTS `compta_operation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compta_operation` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `operation` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compta_periode`
--

DROP TABLE IF EXISTS `compta_periode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compta_periode` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `verouiller` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compta_reglement`
--

DROP TABLE IF EXISTS `compta_reglement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compta_reglement` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `reglement` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compta_simulation`
--

DROP TABLE IF EXISTS `compta_simulation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compta_simulation` (
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
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rdv_afup`
--

DROP TABLE IF EXISTS `rdv_afup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rdv_afup` (
  `session` varchar(40) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `nom` varchar(120) NOT NULL DEFAULT '',
  `prenom` varchar(120) NOT NULL DEFAULT '',
  `societe` varchar(120) NOT NULL DEFAULT '',
  `email` varchar(120) NOT NULL DEFAULT '',
  `telephone` varchar(20) NOT NULL DEFAULT '',
  `valide` tinyint(4) NOT NULL DEFAULT '0',
  `transmission` tinyint(2) NOT NULL DEFAULT '0',
  KEY `session` (`session`),
  KEY `valide` (`valide`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scan`
--

DROP TABLE IF EXISTS `scan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visitor_id` int(11) DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1182 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tweet`
--

DROP TABLE IF EXISTS `tweet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tweet` (
  `id` varchar(30) NOT NULL,
  `id_session` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wikini_acls`
--

DROP TABLE IF EXISTS `wikini_acls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wikini_acls` (
  `page_tag` varchar(50) NOT NULL DEFAULT '',
  `privilege` varchar(20) NOT NULL DEFAULT '',
  `list` text NOT NULL,
  PRIMARY KEY (`page_tag`,`privilege`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wikini_links`
--

DROP TABLE IF EXISTS `wikini_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wikini_links` (
  `from_tag` char(50) NOT NULL DEFAULT '',
  `to_tag` char(50) NOT NULL DEFAULT '',
  UNIQUE KEY `from_tag` (`from_tag`,`to_tag`),
  KEY `idx_from` (`from_tag`),
  KEY `idx_to` (`to_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wikini_pages`
--

DROP TABLE IF EXISTS `wikini_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wikini_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `body` text NOT NULL,
  `body_r` text NOT NULL,
  `owner` varchar(50) NOT NULL DEFAULT '',
  `user` varchar(50) NOT NULL DEFAULT '',
  `latest` enum('Y','N') NOT NULL DEFAULT 'N',
  `handler` varchar(30) NOT NULL DEFAULT 'page',
  `comment_on` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_tag` (`tag`),
  KEY `idx_time` (`time`),
  KEY `idx_latest` (`latest`),
  KEY `idx_comment_on` (`comment_on`),
  FULLTEXT KEY `tag` (`tag`,`body`)
) ENGINE=MyISAM AUTO_INCREMENT=10056 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wikini_referrers`
--

DROP TABLE IF EXISTS `wikini_referrers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wikini_referrers` (
  `page_tag` char(50) NOT NULL DEFAULT '',
  `referrer` char(150) NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `idx_page_tag` (`page_tag`),
  KEY `idx_time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wikini_users`
--

DROP TABLE IF EXISTS `wikini_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wikini_users` (
  `name` varchar(80) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `motto` text NOT NULL,
  `revisioncount` int(10) unsigned NOT NULL DEFAULT '20',
  `changescount` int(10) unsigned NOT NULL DEFAULT '50',
  `doubleclickedit` enum('Y','N') NOT NULL DEFAULT 'Y',
  `signuptime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `show_comments` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`name`),
  KEY `idx_name` (`name`),
  KEY `idx_signuptime` (`signuptime`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-05-26 11:12:28
