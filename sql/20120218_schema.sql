-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Sam 18 Février 2012 à 12:00
-- Version du serveur: 5.5.20
-- Version de PHP: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `afup`
--

-- --------------------------------------------------------

--
-- Structure de la table `afup_accreditation_presse`
--

CREATE TABLE IF NOT EXISTS `afup_accreditation_presse` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Accreditation presse' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_blacklist`
--

CREATE TABLE IF NOT EXISTS `afup_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail_unique` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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

-- --------------------------------------------------------

--
-- Structure de la table `afup_conferenciers`
--

CREATE TABLE IF NOT EXISTS `afup_conferenciers` (
  `conferencier_id` int(11) NOT NULL AUTO_INCREMENT,
  `id_forum` smallint(6) NOT NULL DEFAULT '0',
  `civilite` varchar(5) NOT NULL DEFAULT '',
  `nom` varchar(70) NOT NULL DEFAULT '',
  `prenom` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(65) NOT NULL DEFAULT '',
  `societe` varchar(120) DEFAULT NULL,
  `biographie` text NOT NULL,
  PRIMARY KEY (`conferencier_id`),
  KEY `id_forum` (`id_forum`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_conferenciers_sessions`
--

CREATE TABLE IF NOT EXISTS `afup_conferenciers_sessions` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `conferencier_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`,`conferencier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_contacts`
--

CREATE TABLE IF NOT EXISTS `afup_contacts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `organisation` varchar(255) NOT NULL,
  `poste` varchar(255) NOT NULL,
  `type` enum('ssii','agence web','grand compte','presse','projet','prof','sponsor','') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_cotisations`
--

CREATE TABLE IF NOT EXISTS `afup_cotisations` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `date_debut` int(11) unsigned NOT NULL DEFAULT '0',
  `type_personne` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `id_personne` smallint(5) unsigned NOT NULL DEFAULT '0',
  `montant` float(5,2) unsigned NOT NULL DEFAULT '0.00',
  `type_reglement` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `informations_reglement` varchar(255) DEFAULT NULL,
  `date_fin` int(11) unsigned NOT NULL DEFAULT '0',
  `numero_facture` varchar(15) NOT NULL DEFAULT '',
  `commentaires` text,
  `nombre_relances` tinyint(3) unsigned DEFAULT NULL,
  `date_derniere_relance` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_personne` (`id_personne`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Cotisation des personnes physiques et morales' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_email`
--

CREATE TABLE IF NOT EXISTS `afup_email` (
  `email` varchar(128) NOT NULL DEFAULT '',
  `blacklist` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`email`),
  KEY `email` (`email`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `afup_facturation_forum`
--

CREATE TABLE IF NOT EXISTS `afup_facturation_forum` (
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

-- --------------------------------------------------------

--
-- Structure de la table `afup_forum`
--

CREATE TABLE IF NOT EXISTS `afup_forum` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `titre` varchar(50) NOT NULL DEFAULT '',
  `path` varchar(100) DEFAULT NULL,
  `nb_places` int(11) unsigned NOT NULL DEFAULT '0',
  `date_debut` date NOT NULL DEFAULT '0000-00-00',
  `date_fin` date NOT NULL DEFAULT '0000-00-00',
  `annee` int(11) DEFAULT NULL,
  `date_fin_appel_projet` int(11) DEFAULT NULL,
  `date_fin_appel_conferencier` int(11) DEFAULT NULL,
  `date_fin_prevente` int(11) DEFAULT NULL,
  `date_fin_vente` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_forum_coupon`
--

CREATE TABLE IF NOT EXISTS `afup_forum_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_forum` int(11) NOT NULL,
  `texte` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

--
-- Contenu de la table `afup_forum_coupon`
--

INSERT INTO `afup_forum_coupon` (`id`, `id_forum`, `texte`) VALUES
(1, 6, 'INTERNIM'),
(2, 6, 'ADOBE'),
(3, 6, 'ZEND'),
(4, 6, 'ELAO'),
(5, 6, 'DEVELOPPEZ'),
(6, 6, 'MICROSOFT'),
(7, 6, 'WEKA'),
(8, 6, 'VACONSULTING'),
(9, 6, 'CLEVERAGE'),
(10, 6, 'ENI'),
(11, 6, 'ALTERWAY'),
(12, 6, 'EMERCHANT'),
(13, 6, 'LINAGORA'),
(14, 6, 'OXALIDE'),
(15, 6, 'BUSINESSDECISION'),
(16, 6, 'EYROLLES'),
(17, 6, 'PROGRAMMEZ'),
(18, 6, 'PHPSOLUTIONS'),
(19, 6, 'RBSCHANGE'),
(20, 6, 'JELIX'),
(21, 6, 'CAKEPHPFR'),
(22, 6, 'HOA'),
(23, 6, 'DRUPAL'),
(24, 6, 'MAGIXCMS'),
(25, 6, 'FINEFS'),
(26, 6, 'SOLUTIONSLOGICIELS'),
(27, 6, 'SYMFONY'),
(28, 6, 'DOLIBARR'),
(29, 6, 'PICPHPSQLI'),
(30, 6, 'CRISISCAMP'),
(31, 6, 'RBS'),
(32, 6, 'OBM'),
(33, 6, 'EURATECH'),
(34, 6, 'POLENORD');

-- --------------------------------------------------------

--
-- Structure de la table `afup_forum_partenaires`
--

CREATE TABLE IF NOT EXISTS `afup_forum_partenaires` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_forum` int(11) NOT NULL,
  `id_niveau_partenariat` int(11) NOT NULL,
  `ranking` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `presentation` text,
  `logo` varchar(100) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `afup_forum_partenaires`
--

INSERT INTO `afup_forum_partenaires` (`id`, `id_forum`, `id_niveau_partenariat`, `ranking`, `nom`, `presentation`, `logo`, `site`) VALUES
(1, 6, 5, 1, 'EuraTechnologies', 'Lieu de convergence des acteurs des projets et des innovations, le p?le d''excellence EuraTechnologies a pour vocation de d?velopper le parc d''activit?s ? un ?chelon international, d''accompagner les entreprises du p?le dans leur d?veloppement technologique, commercial et strat?gique, de favoriser l''?mergence de projets TIC et de nouveaux talents, et de proposer des outils et un environnement r?pondant aux besoins des entreprises.', 'logo_euratechnologies.png', 'http://www.euratechnologies.com/'),
(2, 6, 5, 2, 'P?le Nord', 'P?le Nord est l''association d''?diteurs de logiciels libre et open-source du Nord-Pas-de-Calais. Elle a pour objet la promotion et le d?veloppement des acteurs du Free/Libre and Open Source Software (FLOSS) de la r?gion Nord-Pas-de-Calais.', 'logo_polenord.png', 'http://www.polenord.info/'),
(3, 6, 5, 3, 'P?le Ubiquitaire', 'LE POLE UBIQUITAIRE est un r?seau informel pilot? par une gouvernance d''experts qui s''appuie sur un outil unique, pour installer la r?gion Nord-Pas-de-Calais comme leader d''un ?cosyst?me ?conomique d''avenir, l''ubiquitaire.', 'logo_pole-ubiquitaire.png', 'http://www.pole-ubiquitaire.fr/'),
(4, 6, 5, 4, 'FrenchWeb', 'Le magazine des professionnels du net francophone, a pour mission de pr?senter les initiatives des acteurs fran?ais d''internet. Il regroupe une communaut? de plus de 12 000 professionnels, entrepreneurs, experts.\nL''information multim?dia en continu, les interviews des experts, les fiches pratiques : rejoignez vite le CLUB Frenchweb pour tout savoir sur l''internet B2B !', 'logo_frenchweb.png', 'http://frenchweb.fr/'),
(5, 6, 5, 5, 'TooLinux', 'TOOLINUX.com est un quotidien d''information sur Linux et les logiciels Libres. G?n?raliste, il offre chaque jour une revue de presse en ligne et des articles traitant du mouvement opensource, de l''?conomie du libre ainsi que des logiciels Linux ou multi-plateformes. Depuis l''?t? 2006, TOOLINUX.com s''ouvre ? la probl?matique de l''interop?rabilit? des solutions informatiques.', 'logo_toolinux.png', 'http://www.toolinux.com'),
(6, 6, 5, 6, 'Programmez !', 'Avec plus de 30.000 lecteurs mensuels, PROGRAMMEZ ! s''est impos? comme un magazine de r?f?rence des d?veloppeurs.', 'logo_programmez.png', 'http://www.programmez.com/');

-- --------------------------------------------------------

--
-- Structure de la table `afup_forum_planning`
--

CREATE TABLE IF NOT EXISTS `afup_forum_planning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_session` int(11) DEFAULT NULL,
  `debut` int(10) DEFAULT NULL,
  `fin` int(10) DEFAULT NULL,
  `id_salle` smallint(4) DEFAULT NULL,
  `id_forum` int(11) DEFAULT NULL,
  `keynote` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_forum_salle`
--

CREATE TABLE IF NOT EXISTS `afup_forum_salle` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `id_forum` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_forum_sessions_commentaires`
--

CREATE TABLE IF NOT EXISTS `afup_forum_sessions_commentaires` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_session` int(11) DEFAULT NULL,
  `id_personne_physique` int(11) DEFAULT NULL,
  `commentaire` mediumtext,
  `date` int(10) DEFAULT NULL,
  `public` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=566 ;

--
-- Contenu de la table `afup_forum_sessions_commentaires`
--

INSERT INTO `afup_forum_sessions_commentaires` (`id`, `id_session`, `id_personne_physique`, `commentaire`, `date`, `public`) VALUES
(1, 1, 12, 'A mon avis, c''est un truc bidon. On doit pouvoir le supprimer !', 1208895008, 0),
(2, 5, 12, 'J''ai l''impression que c''est la démo d''un produit. S''il est Open Source pourquoi pas, sinon c''est pas trop l''endroit. A moins que ça rentre dans les "conf. éclairs" de la partie sans programme.', 1208895125, 0),
(3, 8, 12, 'Le genre de truc idéal pour le premier jour. Est-ce qu''il ne serait pas judicieux de le proposer comme "sponsor". Une piste à suivre selon moi ;-)', 1208895180, 0),
(4, 38, 12, 'C''est mon expert-comptable. Il passe plutôt bien à l''oral et il se trouve qu''il a une vraie connaissance de la start-up : il a participé à plusieurs levés de fond (au tournant 2000), à des rachats de start-ups en pleine santé (l''année dernière) et à des développements fulgurants (en ce moment). Bref un profil qui peu donner un vrai plus pour les sessions business.', 1209544816, 0),
(5, 42, 12, 'C''est un de mes contacts : je dois le rencontrer un peu plus tard pour mettre au point sa conférence au besoin (Drupal & Memcache seraient les points de départ).', 1209565750, 0),
(6, 76, 173, 'On dirait du spam\r\n', 1210174005, 0),
(7, 78, 173, 'On dirait du spam, je me suis permise de modifier le titre...mais on peut la sucrer', 1210174036, 0),
(8, 38, 173, 'Rien que le titre donne envie, c''est une session qui a un public parmi les visiteurs habituels du forum, et qui peut drainer encore d''autres visiteurs.', 1210174189, 0),
(9, 69, 173, 'spam ?', 1210174411, 0),
(10, 3, 173, 'doublon ?', 1210174655, 0),
(11, 90, 12, 'David Sklar est une pointure désormais discrète du monde PHP. Et Ning avait grand bruit il y a qq temps... En tout cas un "nom" intéressant.', 1210236303, 0),
(12, 83, 12, 'J''ai l''impression qu''il s''agit d''une session très accès pour les débutants. Peut-être pour un atelier ?', 1210236405, 0),
(13, 75, 12, 'Ouf Jelix a fait une propal. Un retour d''expérience aurait peut-être été préférable mais comme il s''agit qui revient régulièrement sur la mailing-list, ça me paraît intéressant.', 1210236543, 0),
(14, 86, 12, 'Cela ressemble fort à la session poste Open-Source de Sarah, non ?', 1210236578, 0),
(15, 69, 12, 'Je pense aussi qu''on peut la virer.', 1210236614, 0),
(16, 62, 12, 'Un retour d''expérience avec un nom connu (TF1) : pile dans notre cible.', 1210236685, 0),
(17, 112, 12, 'Peut être intéressant si c''est vraiement abordé sous l''angle d''un retour d''expérience... En tout ça ressemble à une vraie "web2.0 app" !', 1210431496, 0),
(18, 104, 12, 'C''est pile dans notre thème de cette année. Par contre je ne connais pas le gars en question. La boîte -- SQLI -- est crédible sur ce genre de problématique.', 1210431563, 0),
(19, 105, 12, 'Pas la peine de les re-présenter, ils avaient fait une conf. il y a deux ans. Depuis William continue à bosser sur Eclipse, je pense que ça peut être pas mal.', 1210431619, 0),
(20, 113, 12, 'Pas loin de la session "Sans maîtrise, le code n''est rien" de Gérarl Croes. Reste à voir laquelle pourrait être vraiment intéressante.', 1210433947, 0),
(21, 106, 12, 'Peut-être à comparer avec "Organisation pour un développement portable et efficace". A priori avec des expériences en SSII (SQLI), en interne (Alptis) et en Open Source (Copix), ça peut être intéressant. Peut-être en accentuant sur les différences entre cest 3 cas de figure.', 1210434042, 0),
(22, 97, 44, 'Qui a le droit de commenter ?\r\nDans le doute j''indique que le sujet me semble intéressant pour un TP.\r\n', 1210515180, 0),
(23, 86, 173, 'Celle de Sarah va sauter :D car je ne peux être juge et partie.', 1210593406, 0),
(24, 84, 173, 'Pas sûr d''être maintenue...je ne peux être juge et partie...', 1210593501, 0),
(25, 121, 173, 'Simple présentation j''ai l''impression qui recoupe les RendezVous AFUP', 1210593541, 0),
(26, 5, 173, 'UGC : c''est du cinéma ?\r\n\r\nPlus sérieusement, j''appuie le point de vue de Perrick', 1210593592, 0),
(27, 127, 12, 'Me paraît plus pertinent que la session équivalente de Damien Séguy : il propose au moins un outil en particulier et une promesse d''exemples. Il a déjà fait des conférences dans le cadre des XP Days (en 2007).', 1210599421, 0),
(28, 125, 12, 'Sous la forme d''un jeu, c''est pas mal. Le concept est intéressant pour un atelier !', 1210599452, 0),
(29, 128, 12, 'Une conférence que je suis allé "piocher" ailleurs que dans le technico-technique PHP : un atelier assez ludique pour trouver des nouvelles techniques de communication. Presque un happening en soi. Au maximum pour 25 personnes, c''est très révélateur comme démarche.', 1210599562, 0),
(30, 129, 12, 'J''ai déjà fait un kata avec Arnaud. J''avais été bluffé par les possibilités du langage. Bref plutôt pertinent. Surtout pour ceux qui croient en XUL !', 1210599627, 0),
(31, 121, 12, 'Si c''est bien sous l''angle business, c''est peut-être intéressant. On a déjà eu l''angle technique -- comme le dit Sarah. Reste à bien orienter la conf. avec Fabien.', 1210599692, 0),
(32, 108, 12, 'Ouf, plutôt abstrait et assez loin de nos préoccupations quotidiennes. Et comme je n''ai jamais entendu parlé du gars et encore moins de la fac en question...', 1210599813, 0),
(33, 121, 44, 'Si c''est orienté business attention à l''approche trop promotion pur de sensio.\r\n\r\nSi c''est une approche technique attention à ne pas avoir pour une nième fois la même conférence (j''ai déjà assisté trois fois à la ''meme'' conférence donnée par Fabien).', 1210600568, 0),
(34, 126, 44, 'J''ai un peu causé de ces outils et méthodes avec Damien. C''est potentiellement intéressant, d''autant plus si Damien à une approche technique avec des démos.', 1210600630, 0),
(35, 125, 44, 'Excellent !', 1210600647, 0),
(36, 128, 44, 'J''ai pas bien compris ce que ca voulait dire en lisant le résumé, ce serait bien de reformuler une partie ?', 1210600710, 0),
(37, 129, 44, '"Bref plutôt pertinent. Surtout pour ceux qui croient en XUL !"\r\n\r\nBref les fous et les idéalistes ? \r\n\r\nPlus sérieusement le thème me semble intéressant. Il faut qu''au moins une personne traite ce thème (Js)\r\n', 1210600776, 0),
(38, 120, 44, 'Ca a l''air sympa et ça changera des confs habituelles de Fabien. Par contre cela risque d''être pour un public tres technique vu que c''est traité par Fabien.', 1210600853, 0),
(39, 112, 44, 'Potentiellement intéressant bien que tres spécifique. A mettre dans les ateliers ?', 1210600982, 0),
(40, 104, 44, 'Il a écrit un article sur Spip et contribuerait à Copix. On pourrait voir ce qu''en pense gerald Croes (Copix) ?²', 1210601121, 0),
(41, 4, 44, 'Bon thème mais je pense qu''il faudrait le traiter en français.', 1210603774, 0),
(42, 7, 44, 'Bon sujet.', 1210603849, 0),
(43, 24, 44, 'A approfondir. Si il y a des cas d''applications associés je suis preneur.', 1210603885, 0),
(44, 30, 44, 'Intéressant.', 1210603922, 0),
(45, 35, 44, 'Potentiellement intéressant en atelier technique', 1210604001, 0),
(46, 42, 44, 'Pareil ce monsieur m''avait été recommandé par Linagora parcequ''il souhaitait faire un retour sur le dernier forum. Donc oui c''est bien d''avoir un retour sur la partie performance et Drupal.', 1210604115, 0),
(47, 38, 44, 'Oui en atelier mais le titre du sujet n''est pas bon, je pense qu''il faudrait que ce soit plus explicite dans le genre :\r\n"créer votre entreprise, les bons plans pour ..."', 1210604179, 0),
(48, 32, 44, 'C pas en OpenSource non ?', 1210604280, 0),
(49, 130, 44, 'Sujet d''actualité.', 1210604312, 0),
(50, 65, 44, 'Why not sur une conf eclair ?', 1210604344, 0),
(51, 75, 44, '--> Atelier : +1', 1210604434, 0),
(52, 96, 44, 'Déjà eu l''année dernière non ?\r\nMieux vaut se concentrer sur des retours d''utilisation en France.', 1210604567, 0),
(53, 131, 44, '+1', 1210604597, 0),
(54, 113, 44, 'A la limite si les conférenciers sont ok ca peut être bien de leur faire traiter du sujet à deux. \r\nCa rend les confs plus vivante.', 1210604659, 0),
(55, 107, 44, 'Copix ou Jelix il faudra choisir à mon avis.\r\nJe crois que Copix à une communauté plus grande et est soutenu par une entreprise (sqli). \r\nSans connaître les qualités intrinsèques des deux framework je pencherais plus vers Copix tout en briefant Gerald pour qu''il oriente sa conférence vers le grand public. \r\nDans l''idéal : atelier avec 20 minutes pres du projet et 30 minutes en mode démo.', 1210604815, 0),
(56, 94, 44, 'blabla ?\r\nLe resumé est pas vendeur, c''est dommage car le thème vaudrait d''être traité...', 1210604853, 0),
(57, 125, 151, 'Un theme que tout le monde peut etre confronté... Indispensable pour moi', 1210608062, 0),
(58, 129, 12, 'En fait je parle du XUL parce que sur son dernier projet, le présentateur devait améliorer les 40 000 lignes en JS d''une appli XUL. Le bonheur ;-) J''espère qu''il pourra parler de cette expérience-là.', 1210682119, 0),
(59, 65, 12, 'Effectivement, les confs éclair vont être un bon moyen de ré-aiguiller des sessions...', 1210682280, 0),
(60, 125, 159, '+1, très utile', 1210793020, 0),
(61, 90, 62, 'David est excellent comme conférencier. Ning est une belle référence, et un sujet qui bouge. Même leur site est intéressant, d''un point de vue communauté.\r\n\r\n Je crois qu''il parle un peu de Francais (ou bien c''est sa blonde? ou sa mère?) en tous cas, on peut le forcer à en faire plus :D', 1210889634, 0),
(62, 81, 62, 'Sujet très vaste : il faudrait bien voir avec le conférencier pour ne pas tomber dans les généralités ou les cas particuliers.', 1210889686, 0),
(63, 96, 62, 'Cycle de vie, c''est bon ça. \r\n\r\nBelgique, oui, on a eu l''an dernier. ', 1210889731, 0),
(64, 97, 62, 'subversion, c''est bien, mais seul, ca me semble un peu loin de PHP. Je préfère son autre session, à tout prendre.\r\n\r\n', 1210889782, 0),
(65, 2, 62, 'Sebastian Bergmann : excellent conférencier, avec du contenu et de l''expérience. \r\n\r\nPHPUnit, est dans l''esprit de l''année. Ca me parait bien, comme sujet.', 1210889858, 0),
(66, 4, 62, 'Idem. Pas besoin de Sebastian pour ce sujet.', 1210889882, 0),
(67, 6, 62, 'Pourquoi Mensah n''a pas soumis en Francais? Il le parle couramment (originaire d''Afrique de l''ouest, et il a bossé à Oracle France avant d''aller aux US).\r\n\r\nSujet Innovant, et l''équipe communauté PHP est très agréable à vivre. ', 1210889983, 0),
(68, 5, 62, 'http://prestataires.journaldunet.com/fiche/chiffre_cle/24664/scan_target.shtml\r\n\r\npas dur à trouver, même.\r\n\r\nScan & Target est un éditeur de logiciel dont le métier est de fournir \r\ndes solutions de filtrage, de modération et de monétisation des contenus du web 2.0 (UGC).\r\n\r\nPeu intéressant.\r\n\r\n', 1210890066, 0),
(69, 8, 62, 'Ils vont nous proposer de produire 500 elephpants! \r\n\r\nLes budgets seront difficiles à obtenir, mais c''est surement possible. Je vais voir avec Christopher Jones si c''est possible. Avec Kuassi déjà au courant, ils doivent avoir le forum sur leur radar.', 1210890547, 0),
(70, 31, 62, 'Je l''ai vu à PHP Québec sur l''Unicode : complet, amusant, pointu. Un excellent orateur.\r\n\r\nCanadien d''origine, avec un fort accent québécois quand il parle francais : on peut le pousser la dedans, mais je ne crois pas qu''il aille jusqu''à la session.\r\n\r\nHabite à Pekin, avec une expérience de vie hors de l''ordinaire.\r\n\r\nPar contre, cette session est nouvelle pour moi.', 1210890729, 0),
(71, 65, 62, 'Tres prometteur, mais tres pointu techniquement. ', 1210890846, 0),
(72, 66, 62, 'ca me parait long pour traiter ce sujet.\r\n', 1210890864, 0),
(73, 79, 62, 'J''aurai bien vu ''pourquoi'', plutôt que ''comment''.\r\nToutes ces structures d''architecture sont super importantes à connaitres, et ca fait un excellent sujet, si le but est bien de faire la différence entre les options disponibles.\r\n\r\nJe garde.', 1210890945, 0),
(74, 128, 62, 'j''ai du mal à accrocher.... et voir le rapport avec PHP. ', 1210891001, 0),
(75, 4, 263, 'je pense aussi, qu''en français ce serait mieux ', 1211449234, 0),
(76, 5, 263, '- 1 \r\nPeu intéressant. ', 1211449360, 0),
(77, 6, 263, 'J''ai eu Oracle pour le sponsoring et on m''a dit que les confs de Mensah seront en français \r\n\r\nPeut-être un peu trop technique pour une conf , je verrai plus un atelier ', 1211449671, 0),
(78, 7, 263, '+ 1 pour un atelier', 1211449726, 0),
(79, 24, 263, 'intéressant, dommage qu''il ne soit pas en français', 1211450888, 0),
(80, 31, 263, 'Entre l''annonce récente du Google App Engine , un conf sur le cloud computing serait intéressante', 1211451132, 0),
(81, 42, 263, '+1', 1211451172, 0),
(82, 105, 263, 'Ce serait bien s''il abordait la gestion des projets avec eclipse Svn/Cvs et Mylyn ', 1211451469, 0),
(83, 81, 263, 'Bien pour un atelier', 1211451524, 0),
(84, 106, 263, 'Intéressant, mais attention le sujet est vaste et il ne faudrait pas tomber dans la simple énumération de termes / outils.', 1211451912, 0),
(85, 131, 263, 'Intéressant', 1211451992, 0),
(86, 125, 263, '+1 \r\n\r\nOriginal ', 1211452102, 0),
(87, 129, 263, '+1 \r\n\r\nJe ne pense pas qu''il y ait beaucoup d''appli php sans Javascript de nos jours', 1211452259, 0),
(88, 121, 263, 'Je suis d''accord surtout qu''ils ont en train de mettre en place un programme de certification\r\n\r\nC''est intéressant de voir l''approche business (investissement, retombé...) d''un projet open source comme Symfony porté par une société française et membre de l&#8217;AFUP\r\n\r\n', 1211452503, 0),
(89, 112, 263, 'Bien pour un atelier', 1211452584, 0),
(90, 104, 263, '+1', 1211452632, 0),
(91, 2, 12, 'On a déjà des sessions proposés en français sur ce thème. Pas sûr qu''il faille en rajouter.', 1211727746, 0),
(92, 4, 12, 'Sur un sujet aussi technique, c''est clair que ça peut être chaud à digérer en anglais.', 1211727789, 0),
(93, 6, 12, 'Sauf que si Oracle est sponsor, il faudra autre chose qu''un "petit" atlier ;-)', 1211727844, 0),
(94, 7, 12, 'C''est déjà positionné comme un atelier. Sauf qu''il fallait attendre ma mise à jour pour le voir ;-)', 1211727899, 0),
(95, 134, 12, 'Petit bémol : sur le côté "BNP n&#8217;aurait pas franchi le cap d&#8217;adopter PHP sans le support d&#8217;un éditeur". Je suis en même temps assez curieux de découvrir le pourquoi. Est-ce qu''à la BNP ils ne sont pas assez bon ? Bref un peu perplexe de l''accroche alors que les références sont excellentes...', 1211728838, 0),
(96, 107, 12, 'Mon avais est partagé sur ce choix Jelix / Copix. Peut-être l''un en conf et l''autre en atelier. Je reste convaincu que celui qui passera en atelier a intérêt à faire un retour d''expérience : ce sera largement plus parlant.', 1211728911, 0),
(97, 130, 12, 'Et Eric est plutôt crédible quoi qu''il dise. Je penche quand même plutôt sur son autre session : il ne fait pas de PHP chez Yahoo et donc le côté "retour d''expérience" sera plus léger ici. Par contre c''est l''actu (et il est le seul à l''évoquer)...', 1211729050, 0),
(98, 131, 12, 'Surtout que c''est bien le métier d''Eric en ce moment chez Yahoo! Très intéressant sans aucun doute.', 1211729114, 0),
(99, 33, 12, 'Cela fait très pub. Trop ?', 1211729321, 0),
(100, 133, 12, 'Hyper léger comme présentation. N''y aurait-il pas moyen d''avoir un paragraphe plus intéressant ? En tout cas pour un atelier ça peut être intéressant. Il y a aussi eu un Google Summer of Code sur le sujet l''année dernière...', 1211729407, 0),
(101, 128, 12, 'J''ai reçu un nouveau texte... En espérant qu''il soit plus clair. Sinon le rapport avec PHP ? Il n''est pas direct. Cela reste une session sur le travail en équipe avant tout.', 1211780981, 0),
(102, 34, 12, 'Je n''y comprends pas grand chose. Sauf qu''on y évoque rapidement le web3.0 pour 2009 / 2010 et que je n''adhère pas à cette vision. Encore moins à la crédibilité des orateurs.', 1211781113, 0),
(103, 25, 12, 'Dommage que ce ne soit pas en français. C''est un thème très peu abordé... Mais en même temps, si Oracle se bouge, difficile de présenter une concurrence.', 1211824149, 0),
(104, 35, 12, 'J''ai juste peur que ça fasse comme il y a 2 ans avec la démo Google...', 1211824207, 0),
(105, 126, 151, 'C''est un sujet qui peut etre retenu comme theme de secours en cas d''absence de derniere minute d''un conférencier, que l''on est quelques sujets  de secours. ', 1211828046, 0),
(106, 42, 159, '+1,  Drupal a le vent en poupe en ce moment, ça peut intéresser du monde', 1211832035, 0),
(107, 97, 159, '+1 en TP', 1211832116, 0),
(108, 5, 159, '-1', 1211832133, 0),
(109, 85, 159, 'je verrais cela plus en atelier, dans le cadre de monter son business php et bien s''équiper', 1211832224, 0),
(110, 126, 159, 'je suis d''accord avec l''avis de Cyril', 1211832339, 0),
(111, 129, 159, '+1 aussi', 1211832458, 0),
(112, 38, 159, 'Je suis preneur. Ce sujet est susceptible d''intéresser pas mal de monde. L''atelier risque de vite faire le plein...', 1211832628, 0),
(113, 104, 159, '+1', 1211833119, 0),
(114, 112, 159, 'ok pour atelier', 1211833234, 0),
(115, 80, 159, 'Je verrais ça plutôt en atelier', 1211833350, 0),
(116, 30, 159, '+1', 1211833417, 0),
(117, 121, 159, '+1 pour l''avis de perrick', 1211833550, 0),
(118, 131, 159, '+1', 1211833670, 0),
(119, 75, 159, '+1 en atelier', 1211833747, 0),
(120, 34, 159, 'bof', 1211833819, 0),
(121, 81, 159, 'mouais va pour un atelier', 1211833859, 0),
(122, 112, 173, 'Depuis il a quitté la sté Findawine...', 1213699912, 0),
(123, 35, 173, 'Beaucoup de contenu pour un atelier de 20 minutes', 1213700008, 0),
(124, 139, 12, 'Numéro de portable : 06 65 13 88 72 (au besoin, laisser un message sur le répondeur : je ne suis pas pendu à proximité de mon téléphone en permanence, en particulier les jours de boulot)', 1227090281, 0),
(125, 144, 151, 'Philippe est de PHP Quebec et aussi Co-auteur avec Damien Seguy du livre Sécurité PHP 5 et MySQL', 1240797964, 0),
(126, 145, 151, 'Philippe est membre de phpQuebec et Co-auteur du livre "Securit& PHP 5 et Mysql" avec Damien Seguy', 1240798037, 0),
(127, 316, 173, 'Pascal Martin a pris goût aux conf :)', 1246276171, 0),
(128, 146, 173, 'Sujet intéressant, retoucher le titre ?', 1246276238, 0),
(129, 144, 12, 'Intéressant pour un atelier. Reste à voir si la distance n''est pas de trop pour un conférenceier qui viendrait de loin.', 1246387321, 0),
(130, 329, 12, 'On a déjà eu des conférences sur ce type de sujet par des belges précedemment. C''est dommage qu''il manque les références Open Source pour se faire une idée précise.', 1246387373, 0),
(131, 254, 12, 'WAT, c''est TF1. Pour moi c''est qq chose de très intéressant, à conserver !', 1246387401, 0),
(132, 291, 12, 'Raphaël fait parti des conférenciers français de qualité. Le sujet est intéressant, surtout avec la combinaison "cloud".', 1246387443, 0),
(133, 147, 12, 'C''est un doublon d''une session proposé par Ausy. Encore une fois, il manque la liste des outils open source qui viendraient compléter la session.', 1246387490, 0),
(134, 253, 12, 'Très intéressant. On sort un peu de la technique au passage. C''est pas mal pour un lancement de journée ou une clôture.', 1246387527, 0),
(135, 205, 12, 'J''aime bien l''aspect clinique, surtout s''il propose des sites des visiteurs (à commencer par celui de l''AFUP)', 1246387562, 0),
(136, 227, 12, 'Why not ! Surtout s''ils sont sponsors ;-)', 1246387585, 0),
(137, 322, 12, 'C''est tout en anglais. Et je ne connais pas les développeurs en question. Gros doute !', 1246387618, 0),
(138, 249, 12, 'Je n''y vois pas de liaison avec PHP. Et puis il y avait Adobe comme sponsor il n''y a pas si longtemps. Cependant on est dans la logique "client riche"', 1246387673, 0),
(139, 325, 12, 'Encore une fois tout en anglais. Dommage...', 1246387696, 0),
(140, 226, 12, 'Je ne sais pas si ça vaut une plénière. On est typiquement sur un truc de R&D pur.', 1246387738, 0),
(141, 324, 12, 'Toujours nos sessions par des indiens qui se bougent : http://osscube.com/blog', 1246387797, 0),
(142, 252, 12, 'Très lié à Abode encore. Sans qu''ils soient sponsors... A creuser peut-être.', 1246387860, 0),
(143, 232, 12, 'Probablement plus pratique que les sessions sur les métriques déjà proposés. Surtout s''ils sont sponsors. En tout cas le test de recette n''est pas si facile à faire... Donc ça m''intéresse.', 1246387918, 0),
(144, 203, 12, 'J''aime vraiment beaucoup. Chaque dev. PHP sera confronté à ce genre de problématique, y compris à l''intérieur d''une société. Die LDAP, die !', 1246387970, 0),
(145, 314, 12, 'Trop pointu pour une conf. plénière. Pour cela il faudrait faire un tour complet des solutions de cache. En atelier, ça me paraît plus intéressant.', 1246388020, 0),
(146, 285, 12, 'Je n''arrive pas à voir s''il s''agit d''un retour d''expérience (dans ce cas pourquoi pas) ou bien si c''est un produit open source (et donc à la limite) ou encore un truc propriétaire (et là non)', 1246388078, 0),
(147, 238, 12, 'Je ne cerne pas très bien le périmètre. Et puis en creusant un peu la société, je n''arrive même pas à télécharger leur composant Open Source...', 1246388261, 0),
(148, 157, 12, 'Sujet déjà couvert par Eric Daspet les années précédentes. Je ne vois pas l''intérêt en plus.', 1246388295, 0),
(149, 236, 12, 'Suffisament technique pour être intéressant. Il faudrait peut-être retravaillé le texte pour le rendre plus compréhensible. Cela me fait penser à la conf. de William et son prof sur le "tissage" de la programmation aspect.', 1246388384, 0),
(150, 286, 12, 'Pas compréhensible ni intéressant en l''état.', 1246388409, 0),
(151, 178, 12, 'Le genre de conférence annexe qui peut faire plaisir.', 1246388441, 0),
(152, 326, 12, 'On continue avec des indiens qui sèment à tout vent.', 1246388460, 0),
(153, 234, 12, 'Connais pas le sujet du tout.', 1246388478, 0),
(154, 321, 12, 'Enfin un retour d''expérience. Zend nous connait, on commence à le sentir !', 1246388503, 0),
(155, 251, 12, 'Tiens encore du référencement...', 1246388528, 0),
(156, 317, 12, 'Tellement basique que je ne comprends même qu''il ose se présenter.', 1246388556, 0),
(157, 323, 12, 'Toujours nos indiens qui proposent en masse.', 1246388570, 0),
(158, 327, 12, 'Enfin des outils qui sortent de l''ordinaire. Intéressant pour une atelier.', 1246388609, 0),
(159, 158, 12, 'Mieux que le protocole HTTP. J''avais recherché un conférencier Apache à l''époque, c''est un sujet intéressant.', 1246388644, 0),
(160, 235, 12, 'Peut-être trop ciblé.', 1246388661, 0),
(161, 224, 12, 'Le contenu est très vaste (trop pour une seule conférence) mais le sujet est intéressant. Reste à voir si on peut lui demander un retour d''expérience sur un site connu.', 1246388713, 0),
(162, 255, 12, 'Déjà venu il y a 2 ans. Mais peut-être a-t-il de vraies annonces techniques intéressantes.', 1246388747, 0),
(163, 225, 12, 'J''aime assez le côté retour d''expérience. Surtout sur un domaine aussi peu connu.', 1246388781, 0),
(164, 146, 12, 'La sécurité, toujours la sécurité. On avait fait un carton plein l''année dernière avec ce sujet. A creuser...', 1246388815, 0),
(165, 149, 12, 'Olivier s''engage professionnellement dans cette voie. C''est un gage de sérieur. Et puis le côté tour d''horizon convient bien à un conf. plénière.', 1246388867, 0),
(166, 328, 12, 'Vive les outils que personne ne connaît : on sort des sentiers battus !!!', 1246388894, 0),
(167, 148, 12, 'Why not, le mode "kata" devant les yeux est bluffant quand c''est bien fait et répété...', 1246388939, 0),
(168, 292, 12, 'Une petite révolution dans les modes de travailler : à ne pas manquer.', 1246388968, 0),
(169, 268, 12, 'Microsoft avait fait un bid la dernière fois. On recommence ??', 1246388988, 0),
(170, 237, 12, 'Je préfère leur proposition sur l''objet. Mais ici c''est pas mal non plus. En tout cas ils ont l''air d''assurer techniquement.', 1246389029, 0),
(171, 159, 12, 'Toujours les tests, cette fois-ci avec ZF. En contre-point de la session de Fabien avec Symfony ?', 1246389074, 0),
(172, 316, 12, 'Et puis ça nous permet d''enrichir la track parallèle à PHP. On avait déjà Apache, MySql, Git. On obtient du lourd.', 1246389122, 0),
(173, 233, 12, 'Oui oui oui, on sera dans l''actualité enfin.', 1246389141, 0),
(174, 145, 12, 'On a déjà des proposition Symfony et/ou sécurité. Attention à ne pas faire de doublon.', 1246389171, 0),
(175, 143, 12, 'Pour l''avoir déjà, c''est une démo bluffante : ils sont à deux et ça va très vite. Comme VIM bien maitrisé. Toujours dans une track "les outils annexes".', 1246389214, 0),
(176, 315, 12, 'Celle-là ou bien celle de Philippe sur Oui PHP est industriel. C''est le même genre.', 1246389240, 0),
(177, 204, 12, 'Doublon : Fabien ou Eric ??? En tout cas je préfère Eric sur le partage du web. Avec sa casquette Yahoo! il est assez crédible sur l''aspect communautaire et échange inter-site.', 1246389299, 0),
(178, 250, 12, 'Flex ou Air, il faudra choisir !', 1246389319, 0),
(179, 150, 12, 'Très en marge d''un programme classique. Peut-être dans une track "innovation PHP".', 1246389374, 0),
(180, 242, 12, 'Un tour d''horizon plutôt orienté "décideurs qui souhaitent avoir du nez". A creuser.', 1246389417, 0),
(181, 269, 12, 'Toujours Microsoft qui souhaite participer à la danse....', 1246389442, 0),
(182, 231, 12, 'J''imangine qu''en tant que sponsor, il faudra choisir une ou deux de leurs sessions...', 1246389480, 0),
(183, 332, 12, 'J''aurais préféré un retour d''expérience pour un axe plus direct. Cependant Thomais a déjà fait des conf. dans le cadre des journées Symfony. Donc c''est peut-être rodé.', 1246390847, 0),
(184, 330, 12, 'J''aime bien le côté "clinique". Il y a peut-être là-aussi une track dédiée à mettre en place. Avec le côté "sécurité" ou "l''application en 1h"...', 1246390909, 0),
(185, 331, 12, 'Trop précis comme sujet. J''ai l''impression qu''on rentre dans le domaine de la micro-optimisation.', 1246390961, 0),
(186, 231, 44, 'Non rien n''est obligatoire. Seul la qualité des sessions doit nous guider comme nous l''avons fait jusqu''ici.', 1246427196, 0),
(187, 249, 44, 'On peut demander de recadrer sur Flex + PHP sinon pas d''intérêt.', 1246427311, 0),
(188, 315, 44, 'A coupler avec la présentation d''Olivier.', 1246427357, 0),
(189, 159, 44, 'On pourrait faire une apres-midi framework ou chaque conférencier aurait 1h pour mettre en place une application avec son framework.\r\n\r\nLe principe "1h pour convaincre".\r\n\r\nDans ce cas il faut sortir de symfony+ZF et aller vers les autres frameworks et associés.', 1246427467, 0),
(190, 322, 44, 'idem perrick.\r\nSuis pas fan', 1246427546, 0),
(191, 232, 406, 'Complètement pour une session sur les tests dans la mesure où c''est un sujet qui attire de plus en plus de monde. Cette session sera, je pense, l''occasion de découvrir le logiciel d''intégration continue Sismo développé par Fabien sous forme d''un plug-in Symfony.', 1246450304, 0),
(192, 143, 216, 'En annexe oui ca fiat une bonne session', 1246479010, 0),
(193, 232, 216, 'Sujet en vogue (à juste titre), l''atelier parait plus approprié.', 1246479090, 0),
(194, 315, 216, 'Classique, ca permet de rappeler les choses :)', 1246479145, 0),
(195, 249, 216, 'Deja fait', 1246479165, 0),
(196, 321, 216, 'Ca plaira forcément à une tranche du public.', 1246479208, 0),
(197, 323, 216, 'La vitesse à laquelle il parle (probablement) va perdre tout le monde. Sujet vu en plus', 1246479297, 0),
(198, 325, 216, 'Sujet qui risque d''être trop complexe en anglais', 1246479396, 0),
(199, 332, 216, 'Je vois mal l''intérêt de la session, à lui faire détailler ? Cela fait très newbie', 1246479455, 0),
(200, 317, 216, 'Heu, nous sommes en 2001 ?', 1246479493, 0),
(201, 144, 216, 'Bon thème, conférencier crédible.', 1246479556, 0),
(202, 145, 216, 'Plutot en atelier non ?', 1246479590, 0),
(203, 238, 216, 'Je ne vois ce que cela apporte par rapport à des confs déjà faite sur le sujet. Le résumé va partout et n''arrive nulle part.', 1246479730, 0),
(204, 316, 216, 'En track parallelle oui', 1246479764, 0),
(205, 253, 216, 'J''aime ce genre de sujet un peu décalé', 1246479795, 0),
(206, 227, 216, 'Hehe, ils sont sponsors et puis on connait damien', 1246479844, 0),
(207, 333, 216, 'Si retour d''expérience pourquoi pas mais j''ai du mal à comprendre l''axe', 1246479899, 0),
(208, 237, 216, 'Techniquement ca en jette (sur le papier en tout cas).', 1246479991, 0),
(209, 328, 216, 'Why not, différent', 1246480021, 0),
(210, 205, 216, 'Bon sujet d''atelier', 1246480054, 0),
(211, 250, 216, 'Je préfère celle sur la motivation', 1246480111, 0),
(212, 314, 216, 'Bof, facebook a présenté de bonnes confs sur le sujet déjà', 1246480152, 0),
(213, 322, 216, 'Clairement pas, trop étroit ', 1246480271, 0),
(214, 269, 216, 'mouais, le résumé n''est pas vendeur', 1246480319, 0),
(215, 324, 216, 'On ferait mieux d''offrir le livre High performance MySQL aux gens qui viennent', 1246480388, 0),
(216, 242, 216, 'SUjet plus business comme les aime damien', 1246480415, 0),
(217, 327, 216, 'Différent, go!', 1246480433, 0),
(218, 226, 216, 'Utilisation diférente de php, en plénière ? pas sur.', 1246480492, 0),
(219, 335, 216, '"très grands volumes de données (plus d''un millions de ligne en base)" -> faut pas pousser :)\r\n\r\nRetour d''expérience, ca marche toujours', 1246480575, 0),
(220, 286, 216, 'pas compris', 1246480594, 0),
(221, 336, 216, 'Oeuf corse', 1246480610, 0),
(222, 178, 216, 'Comme perrick', 1246480650, 0),
(223, 225, 216, 'Clairement (en réponse au commentaire de perrick)', 1246480685, 0),
(224, 337, 216, 'Il maitrise clairement  le sujet (en plus c''est un invité je suppose :)', 1246480737, 0),
(225, 159, 216, 'Un match de catch ?', 1246480757, 0),
(226, 236, 216, 'Points évoqués dans leurs autres propositions, il va falloir choisir :)', 1246480989, 0),
(227, 235, 216, 'Les autres sont mieux', 1246481013, 0),
(228, 157, 216, 'bof', 1246481052, 0),
(229, 326, 216, 'Ce sont des machines !', 1246481072, 0),
(230, 147, 216, 'non', 1246481090, 0),
(231, 147, 173, 'non aussi pour ma part', 1246633582, 0),
(232, 334, 173, 'ViPHP de PHPFrance, et développeur de jeux web + de Créajeu.net (http://creajeu.net)\r\n[et ancien lillois]', 1246633793, 0),
(233, 231, 173, 'Cette année (si j''ai bien lu le dossier) le sponsor Gold a droit à 1 track et pis c''est tout.', 1246634914, 0),
(234, 269, 173, 'Montrer qu''on peut mettre php sous Windows malgré les avertissements de la doc php.net', 1246635551, 0),
(235, 332, 173, 'Trop simple', 1246635568, 0),
(236, 324, 173, 'A renvoyer vers le MUG ?', 1246635581, 0),
(237, 326, 173, 'Non pour moi.', 1246635588, 0),
(238, 323, 173, 'On les envoie au MUG ?', 1246635599, 0),
(239, 286, 173, 'Retour XP sur le dév d''une appli si j''ai bien compris', 1246635633, 0),
(240, 333, 173, 'Retour d''XP sur le montage de Créajeu et le dév en équipe.\r\n\r\nPascal est ViPHP sur PHPFrance, codeurs de jeux web alternatifs, et dév de Créajeu.net', 1246635788, 0),
(241, 178, 173, 'Sujet annexe bcp proposé cette année, et on n''a pas abordé le référenceement les 2 années précéentez.', 1246635825, 0),
(242, 150, 173, 'Lua permet de configurer Emacs :)', 1246636094, 0),
(243, 322, 173, 'Trop orienté MySQL : on les envoie au MUG ?', 1246636112, 0),
(244, 234, 173, 'OWASP est inconnu en France, il va falloir modifier le titre ou mieux décrire la conf', 1246636142, 0),
(245, 328, 173, 'Ausy ?\r\n\r\nPHP dans un contexte pro, monitoring d''applications, reporting \r\n=> alternative à la Zend Platform ?', 1246636180, 0),
(246, 336, 173, 'On en a aussi une en français non ?', 1246636197, 0),
(247, 227, 173, 'Alexandre Morgaut est connu aussi de Cyril :)', 1246636217, 0),
(248, 315, 173, 'On peut demander à Pascal et Olivier de bosser ensemble ?\r\nCe serait enrichissant pour le public :)', 1246636247, 0),
(249, 238, 173, 'Il s''agit d''une démo d''une solution ajax\r\n\r\nje cite "une méthode de composition de pages au sein du navigateur dans laquelle chaque composant d''une même page fait l''objet d''une requête indépendante, et qui permet ainsi de pallier à ces deux difficultés. Couplé à une gestion fine des entêtes de cache HTTP, le serveur est ainsi complètement déchargé de la gestion des ressources qui n''ont pas changées"', 1246636277, 0),
(250, 330, 173, 'Plus de crédibilité sur ce track pour Stéphane Combaudon, il est référencé comme expert MySQL.', 1246636325, 0),
(251, 292, 173, 'bien pour le track sur les outils', 1246636383, 0),
(252, 144, 406, 'Sujet intéressant et Philippe est reconnu dans ce domaine.', 1247328461, 0),
(253, 326, 406, 'Le forum PHP Paris est avant tout là pour faire état de PHP en France par ses nombreux retours d''expérience de clients grand comptes. Je vois mal une session sur l''environnement de PHP en Inde.', 1247328619, 0),
(254, 327, 406, 'Sujet plus qu''intéressant et les deux conférenciers sont bien connus.', 1247328700, 0),
(255, 145, 406, 'En atelier pourquoi pas mais je reste sceptique sur le véritable intérêt du sujet.', 1247328849, 0),
(256, 330, 406, '+1 pour cet aspect d''audit en live pour un atelier technique ou une conférence plénière. Le côté atelier aura l''avantage d''être plus interactif vis à vis de l''auditoire contrairement à la session plénière. Je suis pour cette session.', 1247328958, 0),
(257, 323, 406, 'On a nos experts français pour ce type de session.', 1247329023, 0),
(258, 321, 406, 'Session qui plaira surtout aux connaisseurs de Magento mais pourquoi pas vu que c''est un retour d''expérience.', 1247329117, 0),
(259, 332, 406, 'Le site de symfony dispose d''une excellente doc pour ça, je n''en vois pas véritablement l''intérêt...', 1247329159, 0),
(260, 143, 406, 'Pourquoi pas, c''est toujours intéressant et impressionnant de voir des personnes qui maîtrisent VIM.', 1247329204, 0),
(261, 238, 406, 'Le sujet est intéressant mais s''adresse à toutes les technologies serveur. On y retrouve pas véritablement PHP.', 1247329354, 0),
(262, 338, 406, 'Pourquoi pas.', 1247329404, 0),
(263, 249, 406, 'Parler de Flex je suis pour à condition que la session se destine à présenter les communications Flex / PHP au travers du protocole AMF et des APIs PHP 5 type Zend_AMF ou Sabre. Sinon ça n''a que peu d''intérêt pour un forum PHP...', 1247329542, 0),
(264, 236, 406, 'Le sujet semble particulièrement intéressant de par sa technicité. J''approuve pour ce type de session :)', 1247329674, 0),
(265, 227, 406, 'La description est plus un teaser qu''autre chose bien que l''on connaisse les conférenciers. Je serai curieux de savoir de quoi parlerai cette session parce que "produit surprise et top secret" ça ne me motive pas forcément à voter pour cette session.', 1247329841, 0),
(266, 335, 406, 'Le retour d''expérience semble intéressant notamment pour la migration sous ZF.', 1247330008, 0),
(267, 159, 406, 'Une confrontation ZF / symfony ça fait trollesque je trouve, du moins ça tournera forcément au troll et c''est dommage. Une présentation technique de ZF par Julien serait la bienvenue, notamment pour les dernières fonctionnalités du framework comme Zend_Tools.', 1247330256, 0),
(268, 205, 406, 'Eric est connu pour ça et sa session l''an dernier avait très bien marché, donc je pense que l''on ne prend pas de risque à replacer sa nouvelle conférence cette année.', 1247330319, 0),
(269, 178, 406, '+1 pour moi.', 1247330372, 0),
(270, 146, 406, 'Pourquoi pas :)', 1247330433, 0),
(271, 147, 406, 'Je suis sceptique...', 1247330531, 0),
(272, 286, 406, 'pas spécialement très intéressant.', 1247330668, 0),
(273, 269, 406, 'Je suis partagé mais je dirai pourquoi pas à la rigueur.', 1247330854, 0),
(274, 204, 406, '+1 pour moi pour cette session. Cela me semble plus qu''obligatoire que d''avoir une session dédiée à PHP 5.3.', 1247330972, 0),
(275, 231, 406, 'Session qui mérite d''être présentée. Les composants Symfony sont des outils Open Source, gratuit et de qualité.', 1247331076, 0),
(276, 237, 406, 'Excellent sujet technique, ça m''intéresse plutôt pas mal.', 1247331128, 0),
(277, 333, 406, 'Je ne suis pas super motivé par cette session.', 1247331197, 0),
(278, 315, 406, 'Session très intéressante et on connait bien le conférencier.', 1247331294, 0),
(279, 148, 406, 'Même avis que Perrick.', 1247331352, 0),
(280, 291, 406, 'Conférence très intéressante et speaker de qualité.', 1247331464, 0),
(281, 316, 406, 'Pourquoi pas mais le rapport avec PHP se trouve où?', 1247331749, 0),
(282, 225, 406, '+1 pour moi :)', 1247331803, 0),
(283, 329, 406, 'Le sujet est intéressant mais motivera-t-il tout le monde, j''en doute...', 1247331851, 0),
(284, 226, 406, 'En atelier ce serait intéressant.', 1247331899, 0),
(285, 337, 406, '+1 !', 1247331958, 0),
(286, 337, 151, 'il n''est pas prévu dans les invités\r\nmais il peut le devenir :)', 1247581205, 0),
(287, 339, 12, 'Trop la classe de la faire venir. C''est sympa !', 1247675681, 0),
(288, 321, 44, 'Hummm\r\n"Zend Server est en abonnement annuel, et possède 3 niveaux de support : Silver, Gold et Platinum."\r\n\r\nBref cela ressemble beaucoup à une pub produit et il me semble que ce type de session était à proscrire...\r\n\r\nDonc oui de mon point de vue :\r\n- si le sujet traite d''autres plateformes Web\r\nou\r\n- si Zend est sponsor auquel cas c''est considéré comme une conférence promotion.\r\n', 1247749542, 0),
(289, 340, 44, 'TOP !!!', 1247749846, 0),
(290, 405, 173, '<p>spam</p>', 1277198465, 0),
(291, 374, 173, '<p>Par le cr&eacute;ateur du produit, des nouveaut&eacute;s</p>', 1277198581, 0),
(292, 374, 44, '<p>J''aime beaucoup.</p>', 1277200571, 0),
(293, 388, 44, '<p>C''est parfait pour un workshop si on d&eacute;cide d''en faire plusieurs.</p>', 1277200614, 0),
(294, 375, 44, '<p>Le r&eacute;f&eacute;rencement est un sujet que l''on doit traiter. Par contre j''aurais tendance &agrave; aller chercher une pointure sur le sujet.</p>\r\n<p>Je ne connais pas les conf&eacute;renciers propos&eacute;s assez pour dire s''ils le sont. Il nous faudrait un <em>Olivier</em> Andrieu de Webrankinfo par exemple.</p>', 1277200721, 0),
(295, 397, 44, '<p>Le sujet est int&eacute;r&eacute;ssant mais je pense trop sp&eacute;cifique. Une conf&eacute;rence sur l''optimisation serait plus adapt&eacute;e.</p>', 1277200763, 0),
(296, 407, 44, '<p>Trop pointu je pense.</p>', 1277200790, 0),
(297, 409, 44, '<p>Une conf&eacute;rence / workshop sur le couplage PHP / G&eacute;olocalisation pourrait &ecirc;tre fort sympathique.</p>', 1277200830, 0),
(298, 393, 44, '<p>Si on le fait ce serait dans les workshop. Pour ma part je suis pas fan.</p>', 1277200873, 0),
(299, 412, 44, '<p>C''est une bonne id&eacute;e de faire une conf&eacute;rence sur l''"innovation".</p>', 1277200903, 0),
(300, 379, 44, '<p>Je suis pas sur de bien comprendre le sujet. Fred est un bon orateur, ce serait bien de lui demander de pr&eacute;ciser.</p>', 1277200940, 0),
(301, 418, 44, '<p>Trop sp&eacute;cifique non ?</p>', 1277200967, 0),
(302, 391, 44, '<p>C''est un sujet et un conf&eacute;rencier qui am&egrave;nera du traffic.</p>', 1277201035, 0),
(303, 416, 44, '<p>La th&eacute;matique s&eacute;curit&eacute; doit &ecirc;tre trait&eacute;e.</p>', 1277201100, 0),
(304, 413, 44, '<p>Magento en workshop serait pas mal mais je doute qu''en 1-2h on ait le temps d''apprendre et de tester l''outil...</p>', 1277201150, 0),
(305, 389, 44, '<p>Un workshop, nickel.</p>', 1277201167, 0),
(306, 377, 44, '<p>Une conf&eacute;rence de la part de Fred serait une bonne chose. Le sujet PHP 6 sera peut &ecirc;tre trait&eacute; par Rasmus ?</p>', 1277201263, 0),
(307, 411, 44, '<p>Une conf sur Xdebug serait sympa.</p>', 1277201280, 0),
(308, 374, 12, '<p>En plus la r&eacute;f&eacute;rence est sympa : eTF1, ce n''est pas rien !</p>', 1277201537, 0),
(309, 407, 12, '<p>En m&ecirc;me temps, c''est Derick qui parle. Donc &ccedil;a peut avoir son int&eacute;r&ecirc;t. Surtout que depuis qu''il a quitt&eacute; EZ, je ne sais pas trop sur quoi il hacke.</p>', 1277201589, 0),
(310, 406, 12, '<p>J&eacute;r&ocirc;me a pas mal rouler sa bosse sur de grosses infras : &agrave; mon avis ce sera pas mal du tout.</p>', 1277201636, 0),
(311, 389, 12, '<p>Et en plus il pourrait &ecirc;tre sponsor (&agrave; moins que je me trompe).</p>', 1277201660, 0),
(312, 393, 12, '<p>Bien s&ucirc;r les tests ont droit &agrave; une proposition au moins depuis 5 ans d&eacute;sormais... Incapable de savoir si l''auteur est &agrave; la hauteur.</p>', 1277201740, 0),
(313, 410, 12, '<p>Plus rare que son intervention sur Date/Time : &agrave; mon avis cette session est plus "pratique", et donc plus int&eacute;ressante.</p>', 1277201786, 0),
(314, 387, 12, '<p>Pourquoi pas, dommage que ce soit en anglais.</p>', 1277201815, 0),
(315, 416, 12, '<p>Et Liip, c''est la bo&icirc;te de Lukas Smith : donc potentiellement, c''est un bon.</p>', 1277201842, 0),
(316, 394, 12, '<p>Connais pas trop le gars. Et une conf. sur Git a d&eacute;j&agrave; eu lieu l''ann&eacute;e derni&egrave;re (est-ce encore dans le pipe, peut-&ecirc;tre).</p>', 1277201882, 0),
(317, 376, 12, '<p>Cela ressemble &agrave; un pitch produit.</p>', 1277201904, 0),
(318, 396, 12, '<p>Un autre "testeur" qui arrive avec qq ann&eacute;es de retard pour faire du buzz.</p>', 1277201934, 0),
(319, 390, 12, '<p>J''aime bien le sujet : aller triturer en marge de PHP, c''est souvent int&eacute;ressant.</p>', 1277201978, 0),
(320, 398, 12, '<p>Ah enfin Ilia qui viendrait : COOL !</p>', 1277201999, 0),
(321, 412, 12, '<p>Effectivement c''est assez foure-tout mais &ccedil;a peut faire un tour d''horizon pertinent, surtout s''il y a des d&eacute;mos dans tous les sens.</p>', 1277202088, 0),
(322, 388, 12, '<p>Surtout si on met &agrave; la suite un workshop par framework et une session avec chacun &agrave; la fin.</p>', 1277202116, 0),
(323, 391, 12, '<p>Dans la suite des sessions "framework" ?</p>', 1277202134, 0),
(324, 397, 12, '<p>Et puis il y a d&eacute;j&agrave; Ilia qui couvre le sujet.</p>', 1277202153, 0),
(325, 411, 12, '<p>3 ans apr&egrave;s, why not..</p>', 1277202173, 0),
(326, 417, 12, '<p>Dans la petite salle en annexe ? Avec les trucs sur le r&eacute;f&eacute;rencement ou le protocle HTTP...</p>', 1277202216, 0),
(327, 386, 12, '<p>Toujours la suite "framework"</p>', 1277202232, 0),
(328, 409, 12, '<p>Int&eacute;ressant effectivement : on voit grossir la track "annexe" avec r&eacute;f&eacute;rencement, javascript, HTTP, etc.</p>', 1277202272, 0),
(329, 392, 12, '<p>Peut-&ecirc;tre la conf&eacute;rence la plus int&eacute;ressante du gars en question. Avec les "queues" et autres variantes, il y a du potentiel.</p>', 1277202309, 0),
(330, 379, 12, '<p>En tout cas, si c''est pour montrer les usages des m&eacute;thodes magiques, je me ferai un plaisir d''aller montrer que c''est souvent loin d''une "simplicit&eacute;".</p>', 1277202370, 0),
(331, 375, 12, '<p>Dans la track "annexe" ?</p>', 1277202406, 0),
(332, 377, 12, '<p>En tout cas il faut qq''un sur le sujet.</p>', 1277202433, 0),
(333, 386, 516, '<p>Cette conf est assez g&eacute;n&eacute;raliste : une pr&eacute;sentation globale de Cake et de son fonctionnement. Du coup, pas s&ucirc;r que ce soit int&eacute;ressant comme pl&eacute;ni&egrave;re... sauf si on fait une conf ou un d&eacute;bat autour des 3 frameworks, comme &eacute;voqu&eacute; en r&eacute;union pr&eacute;paratoire.</p>', 1277209221, 0),
(334, 374, 406, '<p>Fran&ccedil;ois est un excellent speaker en plus !</p>', 1277209272, 0),
(335, 375, 406, '<p>M&ecirc;me avis que Cyril !</p>', 1277209320, 0),
(336, 387, 516, '<p>Conf tr&egrave;s pointue sur Cake et son &eacute;volution vers du full PHP5... l&agrave; encore pas s&ucirc;r que ce soit int&eacute;ressant en pl&eacute;ni&egrave;re. Cela aurait plus sa place dans un &eacute;v&eacute;nement purement Cake.</p>', 1277209346, 0),
(337, 376, 406, '<p>On ne sait m&ecirc;me pas si c''est Open Source. Si c''est le cas, &ccedil;a a plus sa place dans la salle des projets Open Source.</p>', 1277209377, 0),
(338, 388, 516, '<p>Oui pour un workshop, mais il annonce clairement que cela dure 2 heures ! Tout d&eacute;pend donc ce que l''on entend cette ann&eacute;e comme "workshop"...</p>', 1277209409, 0),
(339, 377, 406, '<p>On connait tous l''auteur &agrave; travers son blog. Quelqu''un de s&eacute;rieux et qui ma&icirc;trise techniquement. Ca me va.</p>', 1277209436, 0),
(340, 378, 406, '<p>En tant que d&eacute;veloppeur, c''est un sujet qui m''int&eacute;resse et qui pour l''instant n''est pas encore trop trait&eacute;. Ca devrait int&eacute;resser d''autres d&eacute;veloppeurs.</p>', 1277209497, 0),
(341, 389, 516, '<p>Si on part sur l''id&eacute;e de workshop autour des 3 frameworks, je pense qu''il faut retenir l''hypoth&egrave;se de les proposer 2 fois chacun ou &agrave; des horaires bien diff&eacute;rents, pour que tout le monde puisse se faire une comparaison</p>', 1277209500, 0),
(342, 390, 516, '<p>Super int&eacute;ressant !</p>', 1277209621, 0),
(343, 391, 516, '<p>c''est pas en doublon avec "Introduction &agrave; Symfony 2" ?</p>', 1277209657, 0),
(344, 393, 516, '<p>C''est des tests sur PHP ou pas ? il ne parle pas de PHPUnit... mais le sujet est hyper important, c''est vrai !</p>', 1277209763, 0),
(345, 391, 406, '<p>Mon avis sur la question est un peu biais&eacute; mais Symfony2 a v&eacute;ritablement fait un grand pas en avant par rapport &agrave; symfony. Symfony2 a d''autant plus le m&eacute;rite de respecter davantage les standards et bonnes pratiques.</p>\r\n<p>Parmi les composants les plus int&eacute;ressants de Symfony2 &agrave; d&eacute;couvrir :</p>\r\n<p>&nbsp;&nbsp;* L''utilisation du cache HTTP pour de meilleures performances<br />&nbsp;&nbsp;* Les tests unitaires et fonctionnels (avec PHP Unit)<br />&nbsp;&nbsp;* Le DOM Crawler<br />&nbsp;&nbsp;* Le composant Finder<br />&nbsp;&nbsp;* Le nouveau framework de formulaires</p>\r\n<p>C''est le genre de conf qui va certainement plaire &agrave; la fois aux d&eacute;veloppeurs, comme aux directeurs techniques et autres d&eacute;cideurs.&nbsp;</p>', 1277209768, 0),
(346, 394, 516, '<p>Git devient incontournable et pose pas mal de souci aux gens (comme moi) qui ont l''habitude de SVN... mais bon, &ccedil;&agrave; ne fait pas une pl&eacute;ni&egrave;re, plut&ocirc;t un atelier.</p>', 1277209822, 0),
(347, 379, 406, '<p>Je suis du m&ecirc;me avis que Cyril. Fred est un excellent d&eacute;veloppeur PHP mais son sujet m&eacute;rite d''&ecirc;tre &eacute;clairci pour savoir dans quelle direction il veut aller.</p>', 1277209867, 0),
(348, 388, 406, '<p>Session de deux heures &ccedil;a risque d''&ecirc;tre d&eacute;licat &agrave; programmer au planning non ?</p>', 1277210024, 0),
(349, 389, 406, '<p>+1 pour un atelier sur Symfony2. Ca permettrait aux techniques de se faire une v&eacute;ritable id&eacute;e des possibilit&eacute;s du framework.</p>', 1277210093, 0),
(350, 395, 516, '<p>D&eacute;cid&eacute;ment cette bo&icirc;te &agrave; vraiment envie de participer ;o)</p>\r\n<p>Ils ont propos&eacute; beaucoup de conf &agrave; deux, n''y a-t-il pas anguille sous roche ?</p>', 1277210116, 0),
(351, 390, 406, '<p>Fabien m''a d&eacute;j&agrave; pr&eacute;sent&eacute; un peu le principe du cache HTTP. C''est un sujet tr&egrave;s int&eacute;ressant qui ne touche pas que PHP mais toutes les applications web. C''est un sujet qui int&eacute;ressera certainement beaucoup de monde.</p>', 1277210201, 0),
(352, 408, 516, '<p>Sujet un peu plus original, mais n''y avait-il pas un truc identique l''an dernier ?</p>', 1277210270, 0),
(353, 392, 406, '<p>Geoffrey est un ancien coll&egrave;gue &agrave; Sensio. C''est un excellent d&eacute;veloppeur. Comme le souligne Perrick, il y''a du potentiel.</p>', 1277210305, 0),
(354, 393, 406, '<p>Geoffrey est un adepte des tests car il travaillait chez Sensio. C''est un sujet qui le passionne et qu''il ma&icirc;trise. C''est s&ucirc;r que Sebastian Bergmann et PHPUnit serait encore mieux :)</p>\r\n<p>En tout cas, sensibiliser les visiteurs &agrave; la pratique des tests est selon moi tr&egrave;s important. Une conf&eacute;rence ou un atelier sur ce sujet doit &ecirc;tre propos&eacute; au planning.&nbsp;</p>', 1277210473, 0),
(355, 394, 406, '<p>Je ne suis pas super chaud.</p>', 1277210508, 0),
(356, 395, 406, '<p>Je suis mitig&eacute; sur le retour d''XP. C''est certainement int&eacute;ressant mais il faudrait plut&ocirc;t avoir le retour d''un grand compte ou d''une institution publique.</p>', 1277210604, 0),
(357, 418, 516, '<p>Je ne trouve pas... les WebServices int&eacute;ressent pas mal de monde avec la mode des r&eacute;seaux sociaux, &ccedil;&agrave; peut rejoindre l''autre conf sur la s&eacute;curit&eacute;. En tout cas, pas en pl&eacute;ni&egrave;re je pense.</p>', 1277210605, 0),
(358, 396, 406, '<p>J''ai envie de dire pourquoi pas parce que Julien est un bon orateur et connait tr&egrave;s bien son sujet.</p>', 1277210659, 0),
(359, 397, 406, '<p>C''est le genre de sujets qui a &eacute;t&eacute; largement d&eacute;velopp&eacute; ces derni&egrave;res ann&eacute;es.</p>', 1277210716, 0),
(360, 398, 406, '<p>L''orateur est une pointure donc clairement je dis oui pour cette pr&eacute;sentation.</p>', 1277210763, 0),
(361, 406, 406, '<p>Le sujet est int&eacute;ressant mais je serai curieux de savoir plus exactement ce qu''il va aborder.</p>', 1277210823, 0),
(362, 408, 406, '<p>Sujet int&eacute;ressant et conf&eacute;rencier &agrave; la hauteur :)</p>', 1277210873, 0),
(363, 409, 406, '<p>+1 pour moi !</p>', 1277210907, 0),
(364, 410, 406, '<p>Sujet pointu et tr&egrave;s int&eacute;ressant. Speaker de qualit&eacute; aussi. Je suis pour !</p>', 1277210975, 0),
(365, 411, 406, '<p>+1 car XDebug reste encore un outil assez marginal chez les d&eacute;veloppeurs PHP lambdas.</p>', 1277211042, 0),
(366, 412, 406, '<p>Une conf de veille technologique, je suis preneur.</p>', 1277211091, 0),
(367, 413, 406, '<p>Magento est bien trop complexe pour apprendre &agrave; l''installer et l''utiliser en si peu de temps.</p>', 1277211143, 0);
INSERT INTO `afup_forum_sessions_commentaires` (`id`, `id_session`, `id_personne_physique`, `commentaire`, `date`, `public`) VALUES
(368, 416, 406, '<p>Jordi a fait une conf au symfony live de juin avec Lukas. Dans la langue de Shakespeare, ce n''est pas un super bon speaker mais il semble &ecirc;tre tr&egrave;s bon techniquement malgr&eacute; tout.</p>', 1277211253, 0),
(369, 417, 406, '<p>Je ne suis pas fan des conf JS lors d''un forum PHP... C''est plus le genre de conf qui a sa place &agrave; Paris Web selon moi.</p>', 1277211311, 0),
(370, 418, 406, '<p>C''est le genre de conf qui m''int&eacute;resserait. Il para&icirc;t que le conf&eacute;rencier est une pointure aussi.</p>', 1277211413, 0),
(371, 375, 356, '<p>Aur&eacute;lien G&eacute;rits a donn&eacute; une conf sur ce sujet au dernier Forum. Qqn l''a vu ? Un avis ?</p>', 1277283509, 0),
(372, 376, 356, '<p>D''accord avec vous 2.</p>', 1277283664, 0),
(373, 393, 356, '<p>Un retour d''exp&eacute; sur la mise en place d''une usine de dev autour d''une base de code existante est un sujet potentiellement tr&egrave;s int&eacute;ressant (j''ai v&eacute;cu) mais tr&egrave;s difficile &agrave; bien traiter IMHO. J''ai peur que cela fasse partie de ces confs dont le sujet est all&eacute;chant mais le r&eacute;sultat d&eacute;cevant...</p>', 1277283993, 0),
(374, 377, 356, '<p>Pareil que Cyril. Si Rasmus ne traite pas du sujet, Fred est la bonne personne.</p>', 1277284095, 0),
(375, 378, 356, '<p>Idem.</p>', 1277284144, 0),
(376, 386, 356, '<p>Je pr&eacute;f&egrave;rerais des confs "framework-agnostiques", o&ugrave; l''on aborde de vrais sujets, du genre comment organiser sa couche m&eacute;tier.</p>', 1277284301, 0),
(377, 379, 356, '<p>D''accord avec Cyril et Hugo.</p>', 1277284350, 0),
(378, 389, 356, '<p>Difficile de se passer de Fabien et d''un atelier sur Symfony 2 :D</p>', 1277284483, 0),
(379, 387, 356, '<p>Effectivement, je serais plus partant pour un atelier, surtout s''il y en a un sur Symfony 2. Mais au risque de me r&eacute;p&eacute;ter, je pr&eacute;f&egrave;rerais des confs "framework-agnostiques"...</p>', 1277284678, 0),
(380, 390, 356, '<p>+1 !</p>', 1277284758, 0),
(381, 392, 356, '<p>Tr&egrave;s bon sujet.</p>', 1277284844, 0),
(382, 394, 356, '<p>Bof...</p>', 1277284888, 0),
(383, 395, 356, '<p>D''accord avec Hugo : sujet potentiellement int&eacute;ressant, mais difficile &agrave; bien traiter. Je ne suis pas trop pour.</p>', 1277284983, 0),
(384, 418, 356, '<p>C''est un sujet int&eacute;ressant et tr&egrave;s peu (jamais ?) trait&eacute;. Le seul truc qui me retient c''est qu''il ne parle que de SOAP dans le descriptif, et pas de REST, ce qui serait fort dommage &eacute;tant donn&eacute; le nb d''APIs REST qui existent.</p>', 1277285219, 0),
(385, 398, 356, '<p>+1</p>', 1277285264, 0),
(386, 419, 516, '<p>Peu de rapport avec nos th&eacute;matiques... &eacute;ventuellement en workshop ou en "off-conf&eacute;rences" ?</p>', 1277377861, 0),
(387, 421, 516, '<p>Ca colle avec les sessions Cake et Symfony d&eacute;j&agrave; propos&eacute;es, tient on enfin notre package ateliers sur les 3 frameworks ? Par contre, je ne sais pas si cet orateur est (re)connu et bon...</p>', 1277378014, 0),
(388, 422, 516, '<p>Peut-&ecirc;tre trop sp&eacute;cifique &agrave; ZF ?</p>', 1277378048, 0),
(389, 421, 12, '<p>Connais pas non plus. Reste &agrave; voir si Zend sera sponsor cette ann&eacute;e. JMF, Julien, vous le connaissez ?</p>', 1277379797, 0),
(390, 420, 12, '<p>Des technos int&eacute;ressantes sur une th&eacute;matique de pointe : le cloud ! J''aime bien le concept.</p>', 1277379904, 0),
(391, 377, 173, '<p>Rasmus tarde &agrave; me r&eacute;pondre :( j''en suis tr&egrave;s triste...</p>', 1277798706, 0),
(392, 428, 173, '<p>E-commerce : ok</p>\r\n<p>Magento : outils bas&eacute; sur PHP</p>\r\n<p>je dis Bingo !</p>', 1277798734, 0),
(393, 395, 173, '<p>Pour conna&icirc;tre un peu cette soci&eacute;t&eacute; (Lyon), leurs clients ce sont les h&ocirc;pitaux publics en France.</p>', 1277798784, 0),
(394, 390, 173, '<p>Plus sympa que l''&eacute;ternelle conf sur Symfony, je pense.</p>', 1277798812, 0),
(395, 376, 173, '<p>+1 pour avis de Hugo : dans les projets open source</p>', 1277798841, 0),
(396, 420, 173, '<p>Avec pr&eacute;sentation du produit maison FineFS : mais il est open source, il me semble</p>', 1277798881, 0),
(397, 417, 173, '<p>Liip c''est la soci&eacute;t&eacute; suisse dans laquelle bosse Lukas Smith.</p>\r\n<p>Sinon, je dis oui, c''est typiquement le genre de sujet que j''irais voir...</p>\r\n<p>Pour Hugo : cela rentre dans ce que <strong>nous</strong> avons demand&eacute;</p>\r\n<p>Technologies autour de PHP : Javascript, HTML 5, microformats...</p>', 1277798961, 0),
(398, 393, 173, '<p>A voir, Geoffrey a d&eacute;j&agrave; conf&eacute;renc&eacute; (avec Hugo...ou sans Hugo, ahem) - mais pkoi pas en atelier ?</p>', 1277799090, 0),
(399, 396, 173, '<p>Je voyais plus Julien sur un autre th&egrave;me (Apache ? HTTP pour les d&eacute;vs) mais s''il a envie de parler de tests, je ne sais pas quoi en dire.</p>', 1277799163, 0),
(400, 410, 173, '<p>Derick a propos&eacute; plein de sujets, et celui ci me para&icirc;t fort int&eacute;ressant.</p>', 1277799267, 0),
(401, 379, 173, '<p>Je me le note &agrave; recontacter donc :D</p>', 1277799290, 0),
(402, 388, 173, '<p>Un workshop en 2 parties ?</p>\r\n<p>Il me semble que c''est le Mr Cake US, donc int&eacute;ressant de le faire venir.</p>', 1277799328, 0),
(403, 387, 173, '<p>Rapha&euml;l : c''est mort pr le framework agnostique, puisqu''on fait venir une personne de chaque communaut&eacute;.</p>\r\n<p>&nbsp;</p>\r\n<p>Si on a une conf Sf, une conf ZF, pourquoi pas CakePHP ?</p>', 1277799487, 0),
(404, 406, 173, '<p>J''aime le c&ocirc;t&eacute; ''pas de solution toute pr&ecirc;te et pistes''. J''ai confiance en qwix comme orateur.</p>\r\n<p>Surout que c''est un atelier : plus d''interactions avec le public.</p>', 1277799635, 0),
(405, 411, 173, '<p>Ouip, Perrick, tu es au top, mais si on prend notre th&egrave;me "PHP de A &agrave; Z", je pense que le X comme XDebug se justifie.</p>', 1277799692, 0),
(406, 389, 173, '<p>En atelier, ok, mais il a aussi une bonne conf&eacute;rence sur le cache...</p>', 1277800123, 0),
(407, 424, 173, '<p>Je suis personnellement fan de William Candillon en tant qu''orateur (souvenez vous de la POA) : sinon XQuery n''est pas nouveau, pas propre &agrave; PHP, XML non plus.</p>\r\n<p>Alors est-ce que le X de "PHP de A &agrave; Z" sera XML ?</p>', 1277800213, 0),
(408, 418, 173, '<p>Tr&egrave;s pointu mais plut&ocirc;t int&eacute;ressant.</p>\r\n<p>S&eacute;curit&eacute; et web services : ne devrait pas juste expliquer ce qu''est un web service mais aussi parler de s&eacute;curit&eacute;...</p>', 1277802472, 0),
(409, 408, 173, '<p>PHP GTK ? Really ?</p>\r\n<p>Sinon, plut&ocirc;t int&eacute;ressant pour le c&ocirc;t&eacute; "PHP c''est pas que l web."</p>\r\n<p>Derick a propos&eacute; plusieurs sujets et ce n''est pas celui ci mon favori.</p>', 1277802527, 0),
(410, 431, 173, '<p>spam</p>', 1277802549, 0),
(411, 412, 173, '<p>Une session qui vous donne envie d''essayer en rentrant chez vous ? Qui ouvre le dialogue, c''est plut&ocirc;t prometteur.</p>', 1277802590, 0),
(412, 425, 173, '<p>Apr&egrave;s Anna Filina l''an dernier, un autre sujet sur les conditions de travail : pourquoi pas?</p>', 1277802630, 0),
(413, 394, 173, '<p>Plut&ocirc;t en atelier, pour faire du pratique qu''en conf th&eacute;orique</p>', 1277802663, 0),
(414, 419, 173, '<p>En rapport avec son blog Geek2CTO&nbsp;<br />http://www.geek-directeur-technique.com/</p>', 1277802715, 0),
(415, 378, 173, '<p>Plut&ocirc;t original et pointu : int&eacute;ressant</p>', 1277802762, 0),
(416, 423, 173, '<p>Tr&egrave;s pointu, soit en th&eacute;orie en conf, soit en interaction en atelier.<br />Il s''agit d''un syst&egrave;me de g&eacute;n&eacute;ration de tests unitaires &agrave; partir de commentaires dans le code.</p>\r\n<p>L''orateur est un membre connu de PHPFrance, et un barbu - chercheur en sandales qui a boss&eacute; chez Mozilla.</p>\r\n<p>Il est tr&egrave;s fort et va tr&egrave;s loin : niveau avanc&eacute; pour cette conf, donc.</p>', 1277802921, 0),
(417, 375, 173, '<p>Je note de contacter WRI.</p>', 1277802933, 0),
(418, 397, 173, '<p>Et dans le track PHP de A &agrave; Z ?</p>\r\n<p>Reprendre l''importance du cache ?</p>', 1277802955, 0),
(419, 409, 173, '<p>Mobilit&eacute;, g&eacute;oloc, services : que du buzzword.</p>\r\n<p>Et si vous avez d&eacute;j&agrave; tent&eacute; avec PHP, vous savez que &ccedil;a vaut le coup !</p>', 1277803006, 0),
(420, 429, 173, '<p>spam</p>', 1277803012, 0),
(421, 433, 173, '<p>http://dk.linkedin.com/in/kallephp</p>\r\n<p><em>Core Developer of the PHP Language, specialized in Windows builds and port. Doc-geek, documenting and fixing major bugs and extensions in the official documentation. Developer of the Alternative PHP Cache extension. Contributor to various projects under the php umbrella like GD, PhD, GTK, PECL and Documentation translations.&nbsp;</em></p>\r\n<p>http://ca.linkedin.com/in/pierrickcharron</p>\r\n<p><em>Contributeur et d&eacute;veloppeur du langage PHP, notamment bug fixing, documentation, et auteur de l''extension PHP Stomp qui permet de communiquer avec la plupart des Message Broker comme ActiveMQ &agrave; travers le protocole Stomp.&nbsp;</em></p>\r\n<p>&nbsp;</p>', 1277803124, 0),
(422, 421, 173, '<p>Il est venu en 2009, il est ZCE...Quid de sa r&eacute;putation dans la communaut&eacute; ZF ?</p>', 1277803189, 0),
(423, 392, 173, '<p>Pr&eacute;cis et bien dans notre track PHP est industriel. Je fais confiance &agrave; Geoffrey en tant que conf&eacute;rencier.</p>', 1277803240, 0),
(424, 427, 173, '<p>J''ignore &agrave; quoi sert ce produit, il me semble que c''est de la GED.</p>\r\n<p>Le c&ocirc;t&eacute; promotionnel est un peu g&ecirc;nant mais...pas plus qu''un framework par une soci&eacute;t&eacute; ??</p>', 1277803401, 0),
(425, 413, 173, '<p>c''est un spam non ?</p>', 1277803418, 0),
(426, 430, 173, '<p>spam</p>', 1277803424, 0),
(427, 391, 173, '<p>en atelier ?</p>', 1277803442, 0),
(428, 416, 173, '<p>Il est peut-&ecirc;tre meilleur francophone ?</p>\r\n<p>&nbsp;</p>\r\n<p>Sinon, s&eacute;curit&eacute; : oui</p>\r\n<p>OWASP Top 10 : oui</p>\r\n<p>Liip : oui par leur exigence de qualit&eacute;</p>', 1277803508, 0),
(429, 398, 173, '<p>PHP de A &agrave; Z, avec A comme APC ?</p>', 1277803687, 0),
(430, 422, 173, '<p>Il para&icirc;t que ce truc est affreux (Zend Form) mais de l&agrave; &agrave; proposer une conf&eacute;rence sp&eacute;cifique...je ne sais pas...</p>', 1277803715, 0),
(431, 386, 173, '<p>Frameworks again</p>', 1277803734, 0),
(432, 426, 173, '<p>En atelier, je dis ok.<br />Serez-vous int&eacute;ress&eacute;s ?</p>', 1277803761, 0),
(433, 432, 173, '<p>Orateur en qui j''ai confiance (et qui n''aime pas PHP, hihi).</p>\r\n<p>Microformats est un des th&egrave;mes qu''on propose dans notre appel &agrave; conf&eacute;renciers : on a une proposition dessus.</p>\r\n<p>Bien pour le track "Annexe"</p>', 1277803818, 0),
(434, 427, 12, '<p>La diff&eacute;rence vient du label "open source". L&agrave; ce n''est pas le cas. Donc il faudrait passer par la case "sponsor" pour eux.</p>', 1277805021, 0),
(435, 433, 12, '<p>Tiens un frenchy qu''on ne connait pas bien. Peut-&ecirc;tre une v&eacute;ritable alternative en France &agrave; notre manque de "star". En tout cas il faudrait les promouvoir.</p>', 1277805086, 0),
(436, 425, 406, '<p>Pourquoi pas et on conna&icirc;t tous Eric :)</p>', 1277882035, 0),
(437, 426, 406, '<p>+1</p>', 1277882053, 0),
(438, 427, 406, '<p>Ou bien dans les projets Open Source si c''est le cas mais pas en conf pl&eacute;ni&egrave;re ni atelier pour moi.</p>', 1277882115, 0),
(439, 428, 406, '<p>Why not !</p>', 1277882142, 0),
(440, 432, 406, '<p>Sujet int&eacute;ressant m&ecirc;me si pas li&eacute; &agrave; PHP.</p>', 1277882178, 0),
(441, 433, 406, '<p>+1</p>', 1277882225, 0),
(442, 434, 406, '<p>Je dis pourquoi pas &agrave; condition que ce ne soit pas une publicit&eacute; d&eacute;guis&eacute;e pour leurs services d''h&eacute;bergement.</p>', 1277882282, 0),
(443, 435, 406, '<p>C''est une conf&eacute;rence qui m''int&eacute;resserait, mais Julien semble toujours proposer les m&ecirc;mes sujets non ?</p>', 1277882344, 0),
(444, 436, 406, '<p>La qualit&eacute; &ccedil;a me parle :)</p>', 1277882387, 0),
(445, 418, 137, '<p>Pour avoir d&eacute;j&agrave; vu cette pr&eacute;sentation, je vous la recommande chaudement !</p>\r\n<p>Le contenu est tr&egrave;s pointu, nouveau (en tout cas j''ai appris pas mal de choses personnellement) et Renaud est tr&egrave;s bon orateur. Par son attitude simple et son humour, il contre-balance le c&ocirc;t&eacute; technique pointu.</p>\r\n<p>Par ailleurs, les web services et leur s&eacute;curisation est un probl&egrave;me r&eacute;current chez les grands comptes o&ugrave; les SI sont tr&egrave;s h&eacute;t&eacute;rog&egrave;nes.</p>', 1277906372, 0),
(446, 435, 173, '<p>Ah bah pour le coup, j''ai super confiance en Julien sur ce th&egrave;me pr&eacute;cis.</p>', 1277907987, 0),
(447, 375, 429, '<p>perso je ne suis pas tr&egrave;s fan du sujet - il me semble que des conf&eacute;rences d&eacute;di&eacute;es plus explicitement au monde du web s''occupent d&eacute;j&agrave; de &ccedil;a...</p>\r\n<p>en r&eacute;sum&eacute;, ce n''est pas du PHP, mais du pur web. Il me semble qu''il serait bon de prendre un peu de recul avec le web pour renforcer l''image plus "corporate" que l''on s''efforce de donner &agrave; PHP.</p>', 1277910275, 0),
(448, 417, 429, '<p>suite &agrave; mon premier commentaire (sur le r&eacute;f&eacute;rencement), je ne peux que "plussoyer" Hugo :)</p>\r\n<p>m&ecirc;me constat -&gt; Forum PHP !== Forum JS</p>\r\n<p>@sarah il est vrai que ces technologies ont &eacute;t&eacute; &eacute;voqu&eacute;es, mais on peut les imaginer traiter dans des confs transverses (du type Zend_Dojo, ou comment faire du Javascript avec PHP - exemple un peu "extr&ecirc;me" je te l''accorde cependant)</p>', 1277910442, 0),
(449, 427, 429, '<p>ou peut-&ecirc;tre en mode "retour d''exp&eacute;rience" si le produit est r&eacute;solument commercial (malgr&eacute; son code a priori ouvert) ?</p>', 1277910540, 0),
(450, 391, 429, '<p>moi je dis que c''est bien de donner leur chance aux petits jeunes qui d&eacute;butent :)</p>\r\n<p>s&eacute;rieusement, il me semble incontournable de mettre en avant les frameworks du calibre de Symfony2&nbsp;</p>', 1277910600, 0),
(451, 409, 429, '<p>pas trop d''avis - j''admets que le sujet m''int&eacute;resse, mais je suis un peu monomaniaque je dois l''admettre - ou plus exactement PHP-centric / donc plus on s''&eacute;loigne du sujet moins &ccedil;a me semble sexy.</p>\r\n<p>&nbsp;</p>', 1277910691, 0),
(452, 386, 429, '<p>les frameworks c''est bien, mangez-en :)</p>\r\n<p>L''option d&eacute;bat sur les 3 frameworks me para&icirc;t vraiment tr&egrave;s bien - j''avais moi aussi &eacute;voqu&eacute; l''id&eacute;e d''organiser des d&eacute;bats, &ccedil;a me semble toujours plus int&eacute;ressant d''avoir une interaction dans l''argumentation, histoire d''&eacute;viter les "publi-conf&eacute;rences".</p>\r\n<p>Dans le cadre d''un d&eacute;bat, on peut esp&eacute;rer que les repr&eacute;sentants des projets fassent un peu mieux comprendre les int&eacute;r&ecirc;ts des solutions qu''ils d&eacute;fendent, par opposition aux autres fa&ccedil;ons de faire, plut&ocirc;t que "dans le vide".</p>\r\n<p>Bref, le d&eacute;bat c''est +++ pour moi</p>', 1277910929, 0),
(453, 435, 429, '<p>le sujet me semble vraiment pertinent, mais il est plut&ocirc;t casse-gueule, non ? On a vite fait de tomber dans le cours magistral sur ce type de sujet...</p>\r\n<p>Cela dit, je pense que Julien a maintenant suffisamment d''exp&eacute;rience pour &eacute;viter cet &eacute;cueil... ou pas ??</p>', 1277911008, 0),
(454, 397, 429, '<p>d''accord avec Sarah - le sujet du cache est plus appropri&eacute; dans le cadre du A-Z</p>', 1277911111, 0),
(455, 393, 429, '<p>les tests unitaires sont vraiment un gros sujet - mais l&agrave; encore, tr&egrave;s dangereux (du point de vue de l''int&ecirc;ret).</p>\r\n<p>AMHA, il ne faut pas faire une conf sur le th&egrave;me "comment &ccedil;a marche", mais sur "&agrave; quoi &ccedil;a sert". Tant que l''on a pas convaincu l''interlocuteur que les tests unitaires (ou pas d''ailleurs) sont essentiels pour le bon d&eacute;roulement d''un projet &agrave; long terme, le sujet est royalement barbant !</p>', 1277912507, 0),
(456, 434, 12, '<p>Effectivement si Oxalide est sponsor alors la question ne se pose pas. Le conf&eacute;rencier est bon, le sujet aussi. Reste donc le point d''interrogation que soul&egrave;ve Hugo.</p>', 1277912695, 0),
(457, 377, 429, '<div style="color: #000000; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 10px; background-image: initial; background-attachment: initial; background-origin: initial; background-clip: initial; background-color: #ffffff; margin: 8px;">\r\n<p>les tests unitaires sont vraiment un gros sujet - mais l&agrave; encore, tr&egrave;s dangereux (du point de vue de l''int&ecirc;ret).</p>\r\n<p>AMHA, il ne faut pas faire une conf sur le th&egrave;me "comment &ccedil;a marche", mais sur "&agrave; quoi &ccedil;a sert". Tant que l''on a pas convaincu l''interlocuteur que les tests unitaires (ou pas d''ailleurs) sont essentiels pour le bon d&eacute;roulement d''un projet &agrave; long terme, le sujet est royalement barbant !</p>\r\n</div>', 1277912934, 0),
(458, 421, 429, '<p>Je connais un peu Micka&euml;l, qui est responsable de la version fran&ccedil;aise de dla doc de Zend Framework, et mon "successeur" en tant qu''animateur de webinars sur le sujet pour Zend France.</p>\r\n<p>J''ai assist&eacute; &agrave; son dernier webinar, et il m''a sembl&eacute; plut&ocirc;t bien, sachant que c''est un exercice qui n''est vraiment pas &eacute;vident.</p>\r\n<p>En tout cas, je pense qu''il a la l&eacute;gitimit&eacute; du point de vue de la communaut&eacute; francophone.&nbsp;</p>\r\n<p>Mais &ccedil;a n''emp&ecirc;cherait pas de solliciter Zend pour leur demander s''ils n''ont pass envie de nous envoyer Andi pour marquer le coup ;)</p>', 1277913135, 0),
(459, 436, 429, '<p>qualit&eacute; / s&eacute;r&eacute;nit&eacute; =&gt; dans notre monde c''est &agrave; la limite de l''oxymore :) Alors justement, s''ils ont vraiment r&eacute;ussi cette association contre-nature, &ccedil;a m''int&eacute;resse aussi beaucoup !</p>', 1277913252, 0),
(460, 386, 516, '<p>Le probl&egrave;me avec un d&eacute;bat g&eacute;n&eacute;raliste autour des 3 frameworks principaux, c''est que &ccedil;&agrave; risque de tourner autour de : le mien est mieux que le tien !</p>\r\n<p>L''id&eacute;e d''un d&eacute;bat CMS vs Framework &eacute;voqu&eacute; sur la ML me para&icirc;t mieux, car on metterait les repr&eacute;sentants Cake, ZF et Symfo au m&ecirc;me niveau.</p>', 1277917276, 0),
(461, 388, 516, '<p>Je rectifie, ce n''est pas "Mr Cake US", disons que c''est l''un des membres importants de la Core Team, qui a l''habitude de parler.</p>\r\n<p>Mais si on le fait venir, pour rentabiliser, il peut participer &agrave; un d&eacute;bat (voir les commentaires sur l''autre session et la proposition faite sur la ML) ET animer un atelier sp&eacute;cifique Cake, mais dans ce dernier cas, il faudra choisir entre cette session et celle qu''il propose sur Cake 2.0...</p>', 1277917467, 0),
(462, 434, 44, '<p>En les cadrant cela peut &ecirc;tre sympa.</p>', 1277917565, 0),
(463, 393, 516, '<p>Plus int&eacute;ressant en atelier qu''en conf pl&eacute;ni&egrave;re dans ce cas, non ?</p>', 1277917567, 0),
(464, 375, 44, '<p>@sarah: je pense que c''est une th&eacute;matique tres importante qui nous permettra d''int&eacute;resser un public plus large.</p>\r\n<p>&nbsp;</p>\r\n<p>@gauthier: on a justement ouvert aux technos connexes et la partie r&eacute;f&eacute;rencement est importante pour tous les web d&eacute;veloppeurs.</p>', 1277918051, 0),
(465, 426, 516, '<p>Oui int&eacute;ressant, mais Eric propose aussi une autre conf...</p>', 1277918292, 0),
(466, 428, 516, '<p>Il y a une autre proposition Magento, peut-&ecirc;tre pourrions-nous demander plus d''infos &agrave; chacun ?</p>', 1277918381, 0),
(467, 434, 516, '<p>Avons-nous pens&eacute; &agrave; contacter les "gros" h&eacute;bergeurs pour le sponsoring : OVH, 1&amp;1 par ex ?</p>', 1277918468, 0),
(468, 374, 429, '<p>j''ach&egrave;te aussi !</p>', 1277970135, 0),
(469, 420, 429, '<p>au risque de me r&eacute;p&eacute;ter je trouve ce type de sujet un peu trop &eacute;loign&eacute; de PHP - bien entendu la probl&eacute;matique de mont&eacute;e en charge est importante, mais je pense que du point de vue du d&eacute;veloppeur, il est plus important d''&eacute;voquer l''impact que le d&eacute;ploiement d''une application sur une telle architecture peut avoir pour l''application elle-m&ecirc;me.</p>\r\n<p>je ne pense pas que ce soit le r&ocirc;le des d&eacute;veloppeurs que de mettre en place une architecture distribu&eacute;e, cela rel&egrave;ve de l''administration syst&egrave;me et r&eacute;seau plut&ocirc;t.</p>', 1277970279, 0),
(470, 426, 429, '<p>la m&eacute;thodologie reste le parent pauvre du monde PHP - en reparler encore une fois ne peut pas faire de mal, loin de l&agrave; !</p>', 1277970365, 0),
(471, 379, 429, '<p>je ne suis pas s&ucirc;r qu''il ne veuille parler que des m&eacute;thodes magiques... j''avoue que je tiens souvent un discours similaire : "ce n''est pas parce que PHP permet de faire n''importe quoi qu''il faut le faire, mais ce n''est pas non plus parce qu''on peut appliquer en PHP la m&ecirc;me rigueur qu''avec les autres langages qu''il faut se priver de ses sp&eacute;cificit&eacute;s plus ... rock''n roll :)"</p>\r\n<p>demander des pr&eacute;cisions me semble &ecirc;tre une bonne id&eacute;e</p>', 1277970497, 0),
(472, 376, 429, '<p>Jean et Aur&eacute;lien &eacute;taient d&eacute;j&agrave; l&agrave; l''ann&eacute;e pass&eacute;e... mais je n''avais pas assist&eacute; &agrave; leur pr&eacute;s...&nbsp;</p>\r\n<p>toutefois &ccedil;a fait un CMS de plus, donc bof :)</p>', 1277970682, 0),
(473, 412, 429, '<p>+1</p>', 1277970702, 0),
(474, 418, 429, '<p>je m''en remets &agrave; l''avis de Jean-Marc en ajoutant que la s&eacute;curit&eacute; est effectivement peu repr&eacute;sent&eacute;e, et malheureusement souvent pas tr&egrave;s bien (trop abstrait, difficile d''acc&egrave;s - si l''orateur est bon vulgarisateur, c''est un tr&egrave;s bon point)</p>', 1277970816, 0),
(475, 395, 429, '<p>je n''ose pas accepter l''id&eacute;e que ce sujet soit encore d''actualit&eacute; :(</p>\r\n<p>y a-t-il encore tant de (gros) projets qui tournent en PHP4 et qui n&eacute;cessitent des conseils pour migrer ?</p>', 1277970897, 0),
(476, 411, 429, '<p>je confirme par ailleurs, d''exp&eacute;rience, que la proportion de d&eacute;veloppeur n''utilisant pas de debugger est proprement incroyable. Dans la plupart des cas, ils ne savent quasi pas ce que c''est et encore moins comment le mettre en oeuvre.</p>\r\n<p>donc +1 pour cette conf</p>', 1277970974, 0),
(477, 437, 429, '<p>une rapide recherche google ne m''a retourn&eacute; aucune entr&eacute;e concernant cornac... s''agit-il d''un produit interne et closed ?? Si oui, int&eacute;r&ecirc;t extr&ecirc;mement limit&eacute;.&nbsp;</p>\r\n<p>Autre point, je serai assez favorable &agrave; prioriser la mise en avant de nouveaux orateurs...</p>', 1277971144, 0),
(478, 437, 137, '<p>Le code a &eacute;t&eacute; ouvert r&eacute;cemment : http://github.com/dseguy/cornac</p>\r\n<p>Par contre, il fait (tr&egrave;s) peur. ;)</p>', 1277991677, 0),
(479, 411, 44, '<p>Si on la choisit je propose de s&eacute;l&eacute;ctionner plusieurs conf&eacute;rences de derick. Cela permettra d''optimiser les co&ucirc;ts.</p>\r\n<p>Cette conf&eacute;rence pourrait &ecirc;tre int&eacute;r&eacute;ssante en mode atelier.</p>', 1277996716, 0),
(480, 419, 44, '<p>Pas forc&eacute;ment stupide. Ca va d&eacute;pendre du choix et de la place.</p>', 1277996929, 0),
(481, 420, 44, '<p>Un peu trop sp&eacute;cifique ?</p>', 1277996962, 0),
(482, 421, 44, '<p>Yes ca peut &ecirc;tre interessant dans notre track "frameworks".</p>', 1277997009, 0),
(483, 422, 44, '<p>Trop sp&eacute;cifique &agrave; priori</p>', 1277997029, 0),
(484, 425, 44, '<p>Un peu trop d&eacute;cal&eacute; &agrave; mon avis. J''aurais pr&eacute;f&eacute;r&eacute; Eric sur un sujet comme l''optimisation cot&eacute; client plut&ocirc;t qu''une conf&eacute;rence sur le bien &ecirc;tre en entrereprise ;)</p>', 1277997193, 0),
(485, 395, 44, '<p>Un retour d''experience projet sur une migration cela me semble pas mal et pourrait &ecirc;tre un retour sur la mise en place de "symfony".</p>', 1277997312, 0),
(486, 433, 44, '<p>Il me semble que frederic Hardy serait un extremement bon candidat pour faire le point sur l''avanc&eacute; de PHP vu qu''il la suit quotidiennement.</p>', 1277997417, 0),
(487, 426, 44, '<p>Je ne suis pas sur que cela draine beaucoup de monde.</p>\r\n<p>Dans la pratique vous utilisez Scrum et les m&eacute;thodes agiles de fa&ccedil;on stricte ? (je veux dire pas juste en prendre des petits bouts). Bon je sais, ici, c''est pas le meilleur endroit pour poser la question :)</p>\r\n<p>&nbsp;</p>\r\n<p>Bref je suis pas fan mais pkoi pas dans le track d&eacute;couverte annexe mais une session sur le r&eacute;f&eacute;rencement, sur HTML 5, sur l''opti cot&eacute; client, ... me semble avoir plus sa place.</p>', 1277997659, 0),
(488, 428, 44, '<p>Il faudrait effectivement un track sur le e-commerce avec Magento ET Prestashop (au moins).</p>\r\n<p>Par contre ces conf&eacute;rences ne devraient pas &ecirc;tre sp&eacute;cialis&eacute;es sous peine de se limiter le nombre de personnes int&eacute;r&eacute;ss&eacute;es.</p>', 1277997744, 0),
(489, 387, 44, '<p>Dans le track framework une session sur les nouveaut&eacute;s de cakePHP me semble bien (20 min)</p>\r\n<p>Suivi d''une conf&eacute;rence sur la mise en place de cakePHP &agrave; la fa&ccedil;on tuto (40-60 min) serait une bonne marche pour commencer.</p>', 1277997840, 0),
(490, 434, 44, '<p>Sebastien avait d&eacute;ja fait une conf&eacute;rence comme cela lors de Solution Linux &agrave; ma demande, il s''en &eacute;tait bien tir&eacute;.</p>\r\n<p>On pourrait &eacute;ventuellement solliciter des gros mais ils seraient moins facile &agrave; cadrer qu''un petit que l''on connait (il a mis en place 20minutes et connait nicolas et arnaud).</p>', 1277997942, 0),
(491, 435, 44, '<p>Why not mais connaissant julien ce sera pointu :)</p>', 1277997991, 0),
(492, 436, 44, '<p>Tiens ce nom me dit quelque chose :)</p>\r\n<p>Le sujet &agrave; l''air bon mais j''aimerais qu''on lui demande de le refactoriser/expliciter car je ne suis pas sur de bien voir quelle sera l''articulation de la pres.</p>', 1277998116, 0),
(493, 437, 44, '<p>Le sujet &agrave; l''air tres sympa. Peut on voir des sorties de ce script ? (sans l''installer)</p>', 1277998233, 0),
(494, 438, 44, '<p>Damien ou quelqu''un d''autre il me semble qu''une conf&eacute;rence sur la gestion de projet en &eacute;quipe est une th&eacute;matique qui revient. A cr&eacute;er ou s&eacute;l&eacute;ctionner.</p>', 1277998279, 0),
(495, 398, 44, '<p>+1 &eacute;galement. Si Ilia vient (idem derrick) ce serait bien de leur demander plusieurs conf&eacute;rences. On a pas la chance de les avoir tous les jours.</p>', 1277998332, 0),
(496, 439, 44, '<p>St&eacute;phane est intervenu l''ann&eacute;e derni&egrave;re via lemug (si j''ai bon souvenir et que je ne confonds pas) et sa conf&eacute;rence &eacute;tait pleine.</p>\r\n<p>+1 pour moi si on a pas conflit avec une autre conf/orateur</p>', 1277998400, 0),
(497, 440, 44, '<p>Bien mais ilia propose la m&ecirc;me non ? :)</p>', 1277998471, 0),
(498, 444, 44, '<p>Qqun connait l''outil ? Ca pourrait &ecirc;tre sympa</p>', 1277998618, 0),
(499, 450, 44, '<p>Cette conf&eacute;rence &agrave; l''avantage de faire un tour global des probl&eacute;matiques de s&eacute;curit&eacute;. En plus Pascal ma&icirc;trise</p>', 1277998910, 0),
(500, 451, 44, '<p>Cela me semble une bonne conf&eacute;rence annexe en mode atelier.</p>', 1277998975, 0),
(501, 455, 44, '<p>+ 1 !</p>', 1277999103, 0),
(502, 458, 44, '<p>Cela peut &ecirc;tre int&eacute;r&eacute;ssant de parler du couplage PHP / VoIP.</p>', 1277999177, 0),
(503, 437, 137, '<p>@Cyril: Il faut demander des exemples &agrave; Damien (je n''ai pas encore r&eacute;ussi &agrave; le lancer).</p>', 1278060562, 0),
(504, 424, 429, '<p>pas fan du tout perso... Sarah &agrave; raison, il n''y a quasi rien de propre &agrave; PHP dans tout &ccedil;a. Il doit exister des conf sur XML, non ?? :)</p>', 1278061402, 0),
(505, 452, 429, '<p>Pascal est un type s&eacute;rieux et pointu (pour autant queje le connaisse), mais le sujet ne me semble pas forc&eacute;ment sexy (et un peu ressass&eacute;).</p>\r\n<p>En revanche, une retrospective des 15 ans de PHP ax&eacute;e sur le th&egrave;me "oui, PHP a VRAIMENT chang&eacute; en 15 ans" et d&eacute;taillant tout ce qui fait que la plateforme a &eacute;volu&eacute; dans le bon sens pour &ecirc;tre aujourd''hui &agrave; la hauteur des exigence de l''entreprise serait vraiment bien.&nbsp;</p>\r\n<p>Cela a-t-il &eacute;t&eacute; propos&eacute; ?</p>', 1278061570, 0),
(506, 428, 429, '<p>@Cyril il ne m''avait pas sembl&eacute; que Prestashop jouait dans la m&ecirc;me cour que Magento, si ? Cela dit, je suis d''accord sur l''id&eacute;e qu''une conf sur l''e-commerce serait pr&eacute;f&eacute;rable.</p>\r\n<p>De mani&egrave;re g&eacute;n&eacute;rale, je pense d''ailleurs que les sujets centr&eacute;s sur un produit (quel qu''il soit) devrait syst&eacute;matiquement &ecirc;tre trait&eacute;s sous forme d''atelier tandis que les confs seraient d&eacute;di&eacute;es &agrave; des pr&eacute;sentations th&eacute;matiques (genre une conf sur les frameworks nouvelle g&eacute;n&eacute;ration plut&ocirc;t qu''une sur Symfony2, une autre sur ZF2, une autre sur cake2 une autre sur TotoFramework 8, etc.)</p>\r\n<p>Il serait fort tard pour instaurer une telle r&egrave;gle, mais ce pourrait &ecirc;tre une id&eacute;e pour les prochaines &eacute;ditions, non ?</p>', 1278061784, 0),
(507, 421, 225, '<p>Vous pouvez y aller pour Mickael sans aucun probl&egrave;me, c''est du s&eacute;rieux</p>\r\n<p>&nbsp;</p>\r\n<p>A noter que je participerai &agrave; ses confs et ses ateliers s''il est pris, nous avons projet de faire cela &agrave; 2, il vous le confirmera en personne.</p>', 1278084478, 0),
(508, 418, 225, '<p>Je rejoinds JMF, on a vu la conf&eacute;rence ensemble : &ccedil;a d&eacute;chire</p>', 1278084613, 0),
(509, 422, 225, '<p>On ferait &ccedil;a en atelier Mickael et moi.</p>', 1278084652, 0),
(510, 376, 151, '<p>Il s''agit d''un nouveau CMS, bas&eacute; avant tout pour optimiser le r&eacute;f&eacute;rencement</p>\r\n<p>J''en ai &eacute;cri un article dessus dans PHP Solutions</p>\r\n<p>http://phpsolmag.org/fr/magazine/1110-framework-javascript</p>\r\n<p>Christophe</p>', 1278158377, 0),
(511, 465, 12, '<p>Un peu l&eacute;ger pour une conf&eacute;rence d''une heure. Dans le cadre d''une "d&eacute;mo rapide" &agrave; la limite.</p>', 1278322614, 0),
(512, 457, 12, '<p>C''est effectivement un sujet qui va arriver de plus en plus : l''application web en mode synchrone va mourir !</p>', 1278322648, 0),
(513, 443, 12, '<p>Int&eacute;ressant : l''ann&eacute;e derni&egrave;re il me semble qu''il y avait eu une d&eacute;mo d''un de ces produits. Pourquoi pas un panorama un peu plus complet.</p>', 1278322697, 0),
(514, 426, 12, '<p>Et puis il y a d''autres personnes que Eric pour en parler. Il me para&icirc;t l&eacute;ger sur ce point.</p>', 1278322746, 0),
(515, 441, 12, '<p>Plus pertinent peut-&ecirc;tre que les "m&eacute;thodes agiles", en tout cas une nouveaut&eacute; pour le Forum PHP.</p>', 1278323073, 0),
(516, 432, 12, '<p>Effectivement j''ai entendu beaucoup de bien de ce Fr&eacute;d&eacute;ric l&agrave; !</p>', 1278323103, 0),
(517, 439, 12, '<p>Le top serait que ce soit un v&eacute;ritable retour d''exp&eacute;rience, pas juste un catalogue de recettes.</p>', 1278323135, 0),
(518, 464, 12, '<p>D&eacute;j&agrave; plus int&eacute;ressant ! On reste dans notre track annexe...</p>', 1278323194, 0),
(519, 459, 12, '<p>Ils doivent d''abord travailler &agrave; rendre leur code populaire avant de venir sur le Forum. A moins que la demi-journ&eacute;e de conf. &eacute;clairs leurs conviennent. En l''&eacute;tat, c''est trop immature pour un framework.</p>', 1278323312, 0),
(520, 447, 12, '<p>N''avons nous pas un des dev. qui vient d&eacute;j&agrave; ?</p>', 1278323362, 0),
(521, 456, 12, '<p>Sont-ils sponsors cette ann&eacute;e ?</p>', 1278323397, 0),
(522, 446, 12, '<p>D&eacute;j&agrave; fait l''ann&eacute;e derni&egrave;re si bon me semble.</p>', 1278323491, 0),
(523, 466, 12, '<p>Un retour d''exp&eacute;rience qui arrive un peu tard qui m&eacute;rite toute notre attention. Surtout s''il y a bien qq''un de Canal+ qui vient comme c''est pr&eacute;vu.</p>', 1278331221, 0),
(524, 449, 12, '<p>L''ann&eacute;e derni&egrave;re, il y avait eu l''&eacute;quipe d''Eric qui souhaitait monter une PIC en PHP (dans la zone Open Source). Je ne sais pas o&ugrave; en est leur projet. Sinon, pour les tests unitaires, j''ai l''impression que c''est un peu apr&egrave;s la bataille : cela fait 4 ans d&eacute;sormais que les premi&egrave;res sessions ont eu lieu. A creuser peut-&ecirc;tre pour le retour d''exp&eacute;riences.</p>', 1278662564, 0),
(525, 467, 12, '<p>Un nouveau challenger pour le pool CMS ? Et comme il est frenchy...</p>', 1278662594, 0),
(526, 444, 12, '<p>Et voil&agrave; donc le projet d''Eric de l''ann&eacute;e derni&egrave;re : je suis curieux de voir ce que &ccedil;a donne. En frontal avec l''offre de Smile !</p>', 1278662639, 0),
(527, 445, 12, '<p>Un tr&egrave;s bon technique + un bon orateur. Un duo int&eacute;ressant en tout cas.</p>', 1278662667, 0),
(528, 468, 12, '<p>Il manque encore des billes mais j''ai l''impression que &ccedil;a ferait une bonne section dans le moment CMS !</p>', 1278662757, 0),
(529, 424, 151, '<p>J''ai d&eacute;j&agrave; vu la pr&eacute;sentation dans un &eacute;v&egrave;nement xQuery</p>\r\n<p>C''etait bien dans la milieu xQuery car il montrait comment mixer le tout pour faire un site web et la mise en production</p>\r\n<p>Je pense que c''est quelque chose qui reste pour l''instant marginal</p>\r\n<p>mais pourquoi pas</p>', 1278683289, 0),
(530, 391, 406, '<p>Fabien a encore beaucoup de choses tr&egrave;s techniques &agrave; d&eacute;voiler sur Symfony2. Il y''a deux semaines il a d&eacute;voil&eacute; le composant de cache HTTP. D''autres composants tr&egrave;s int&eacute;ressants sont en cours de pr&eacute;paration. L''assemblage de ces derniers dans Symfony2 en fera certainement LE framework professionnel de PHP.&nbsp;</p>', 1278703212, 0),
(531, 422, 406, '<p>@Julien : Zend_Form vs sfForm ? #troll #paspumenemp&ecirc;cher</p>', 1278703826, 0),
(532, 437, 406, '<p>Une conf pr&eacute;sentant les outils d''analyse comme PHP_MD, PHP CodeSniffer, PDepend, VLD... me semblerait plus judicieuse non ?</p>', 1278704245, 0),
(533, 439, 406, '<p>Le sujet me tente bien en atelier.</p>', 1278704317, 0),
(534, 441, 406, '<p>+1</p>', 1278704346, 0),
(535, 442, 406, '<p>Pourquoi pas ! Conf&eacute;rence int&eacute;ressante pour les d&eacute;cideurs notamment.</p>', 1278704399, 0),
(536, 443, 406, '<p>Gabriele est un excellent d&eacute;veloppeur. C''est un math&eacute;maticien &agrave; la base. Il est contributeur au projet PHP_CodeSniffer et travaille au d&eacute;veloppement du plugin PHP de Sonar. J''ai vu un &eacute;chantillon de sa conf aux RMLLs (20min) et je serai curieux de voir en pratique Sonar et les autres projets d''analyse de code.</p>', 1278704506, 0),
(537, 444, 406, '<p>Sonar on commence &agrave; l''utiliser chez Sensio pour auditer la qualit&eacute; de nos projets. C''est encore un outil exp&eacute;rimental mais tr&egrave;s prometteur ;)</p>', 1278704567, 0),
(538, 445, 406, '<p>C''est qui Cyril Pierre de Geyer ? ^^</p>\r\n<p>&nbsp;</p>\r\n<p>+1 pour moi !</p>', 1278704627, 0),
(539, 446, 406, '<p>Moyennement convaincu.</p>', 1278704662, 0),
(540, 447, 406, '<p>Il faut demander &agrave; Jonathan Wage de venir pour &ccedil;a. Il nous parlera de Doctrine2. Vous souhaitez que je lui demande ?</p>', 1278704701, 0),
(541, 448, 406, '<p>Plus d''informations ?</p>', 1278705008, 0),
(542, 449, 406, '<p>Un comparatif des solutions Open Source de PIC serait certainement plus int&eacute;ressant.</p>', 1278705067, 0),
(543, 450, 406, '<p>C''est un sujet vu et revu. Chaque ann&eacute;e nous avons au moins une conf&eacute;rence sur les XSS, CSRF, SQL Injection... Est ce que &ccedil;a vaut le coup d''en remettre une couche (m&ecirc;me si bien s&ucirc;r c''est important la s&eacute;curit&eacute;). Pascal est un bon conf&eacute;rencier mais le sujet est trop courant...</p>', 1278705218, 0),
(544, 451, 406, '<p>+1</p>', 1278705291, 0),
(545, 452, 406, '<p>@Gauthier ce serait un excellent sujet mais pour &ccedil;a l''id&eacute;al ce serait que Rasmus en parle.</p>', 1278705496, 0),
(546, 453, 406, '<p>Patrick est un bon conf&eacute;rencier. Son sujet semble int&eacute;ressant.</p>', 1278705540, 0),
(547, 454, 406, '<p>Sujet all&eacute;chant ! +1 pour moi :)</p>', 1278705608, 0),
(548, 455, 406, '<p>+10</p>', 1278705695, 0),
(549, 456, 406, '<p>je suis mitig&eacute;...</p>', 1278705744, 0),
(550, 457, 406, '<p>Sujet int&eacute;ressant</p>', 1278705787, 0),
(551, 458, 406, '<p>+1</p>', 1278705811, 0),
(552, 459, 406, '<p>Avec l''arriv&eacute;e de Symfony2, Lithium et ZF2, je doute que leur projet va int&eacute;resser beaucoup de monde.</p>', 1278705890, 0),
(553, 460, 406, '<p>Sa description ne nous dit pas comment la cryptographie sera li&eacute;e &agrave; PHP</p>', 1278705957, 0),
(554, 461, 406, '<p>Pourquoi pas :)</p>', 1278705990, 0),
(555, 462, 406, '<p>+1 m&ecirc;me si c''est un sujet vu et revu. Le conf&eacute;rencier est connu dans ce domaine l&agrave; mais un Damien S&eacute;guy pourrait &eacute;galement faire la conf&eacute;rence.</p>', 1278706040, 0),
(556, 463, 406, '<p>Description trop vague...</p>', 1278706071, 0),
(557, 464, 406, '<p>Je maintiens ma position, HTML5 c''est un sujet qui sera moultes fois d&eacute;velopp&eacute; &agrave; ParisWeb un mois plus t&ocirc;t donc je ne suis pas convaincu pour le forum PHP. Profitons de toutes les conf&eacute;rences PHP propos&eacute;es pour les mettre en avant plut&ocirc;t que de planifier trop de th&eacute;matiques (trop) annexes.</p>', 1278706169, 0),
(558, 465, 406, '<p>Pourquoi pas en atelier.</p>', 1278706216, 0),
(559, 466, 406, '<p>M&eacute;ga +1</p>', 1278706258, 0),
(560, 467, 406, '<p>+1 mais pas en conf&eacute;rence pl&eacute;ni&egrave;re.</p>', 1278706299, 0),
(561, 468, 406, '<p>Les retours d''XP chez FRAM, CANAL+ et France T&eacute;l&eacute;visions, ce serait top :)</p>', 1278706338, 0),
(562, 468, 151, '<p>la conf est interressante surtout qu''on a mont&eacute; un noyau qui a permi de faire Roland Garros, LA coupe du monde et le Tour de France</p>\r\n<p>&nbsp;</p>', 1279203090, 0),
(563, 417, 356, '<p>En tant que d&eacute;veloppeurs web, nous sommes tous plus ou moins amen&eacute;s &agrave; faire du JS, donc je dirais plut&ocirc;t oui, mais je trouve le sujet un peu "&eacute;troit".</p>', 1279209900, 0),
(564, 476, 429, '<p>Nouvel arrivant dans une soci&eacute;t&eacute;, freelance, consultant... nous sommes tous r&eacute;guli&egrave;rement confront&eacute; &agrave; la d&eacute;licate &eacute;preuve de la reprise d''un code h&eacute;rit&eacute;.</p>\r\n<p>Avec pour objectif d''atteindre un niveau de qualit&eacute; en rapport avec les exigences du contexte, il est parfois n&eacute;cessaire de remettre en question ce code.</p>\r\n<p>Voyons les bons r&eacute;flexes qu''il faut avoir, mais aussi les mauvais qu''il faut bannir, pour r&eacute;ussir &agrave; am&eacute;liorer la qualit&eacute; du code et des processus de d&eacute;veloppement sans trop froisser les susceptibilit&eacute;s !</p>', 1283422630, 0),
(565, 476, 429, '<p>me suis tromp&eacute; ; j''ai post&eacute; un comment au lieu d''&eacute;diter la session, je recommence :)</p>', 1283422673, 0);

-- --------------------------------------------------------

--
-- Structure de la table `afup_inscriptions_rappels`
--

CREATE TABLE IF NOT EXISTS `afup_inscriptions_rappels` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `date` int(10) NOT NULL DEFAULT '0',
  `id_forum` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Emails pour le rappel du forum PHP' AUTO_INCREMENT=1148 ;

--
-- Contenu de la table `afup_inscriptions_rappels`
--

INSERT INTO `afup_inscriptions_rappels` (`id`, `email`, `date`, `id_forum`) VALUES
(6, ' eb@nanocode.fr', 0, 1),
(4, 'an.remy@wanadoo.fr', 0, 1),
(5, 'afup@noiretblanc.org', 0, 1),
(7, 'hai_elkaim@hotmail.com', 0, 1),
(8, 'nchambrier@igloographix.net', 0, 1),
(9, 'pbonnet@igloographix.net', 0, 1),
(10, 'juanma@numericable.fr', 0, 1),
(11, 'houariinfo@yahoo.fr', 0, 1),
(12, 'aolimas@yahoo.fr', 0, 1),
(13, 'mohamed.raziki@gmail.com', 0, 1),
(14, 'enalpas@yahoo.fr', 0, 1),
(15, 'kenneth@himschoot.be', 0, 1),
(16, 'pavel.bliznakov@bvra.univ-st-etienne.fr', 0, 1),
(18, 'x.philbert@pixandlog.net', 0, 1),
(19, 'jeckel@jeckel-dev.net', 0, 1),
(20, 'admin@aides.org', 0, 1),
(21, 'patrick.premartin@olfsoft.com', 0, 1),
(22, 'sofiane7781@gmail.com', 0, 1),
(23, 'sm@leh.fr', 0, 1),
(24, 'remi.perroud@adhersis.com', 0, 1),
(27, 'dvince44@yahoo.fr', 0, 1),
(26, 'david@1st-affiliation.com', 0, 1),
(28, 'h_hassan_fr@yahoo.fr', 0, 1),
(29, 'ruth.milner@gmail.com', 0, 1),
(31, 'gregory.chevret@unilog.fr', 0, 1),
(32, 'cedric.duverger@unilog.fr', 0, 1),
(33, 'lg.d@laposte.net', 0, 1),
(34, 'paul@fleetriver.com', 0, 1),
(35, 'stephane.crivisier@gmail.com', 0, 1),
(36, 'stephane.blanchard@memobox.fr', 0, 1),
(37, 'stephane.blanchard@memobox.fr', 0, 1),
(38, 'fab@alti-com.fr', 0, 1),
(39, 'nd@lafactory.net', 0, 1),
(41, 'kkraliz@gmail.com', 0, 1),
(1145, 'pascal.rondon@gmail.com', 1286528530, 5),
(45, 'perrick@noparking.net', 1157097090, 1),
(47, 'claire.guilloton@voila.fr', 1157119879, 1),
(50, 'rodolphe@eveilleau.fr', 1157144080, 1),
(51, '', 1157178608, 1),
(52, 'marko@milicevic.fr', 1157233470, 1),
(1141, 'nd@octaveoctave.com', 1286296179, 5),
(54, 'malickbandiaye@gmail.com', 1157316814, 1),
(55, 'noussadk@hotmail.com', 1157317093, 1),
(56, 'brunofr@ioda-net.ch', 1157349061, 1),
(57, 'rezzaki@yahoo.de', 1157368336, 1),
(58, 'freddy.levee@ac-lille.fr', 1157389184, 1),
(59, 'majdi_cherif@yahoo.fr', 1157440080, 1),
(61, 'ngadom@yahoo.fr', 1157450777, 1),
(1142, 'hubert.moutot@gmail.com', 1286444703, 5),
(63, 'dl@activpartners.net', 1157555366, 1),
(64, 'irakozejames1@yahoo.fr', 1157561710, 1),
(65, 'irakozejames1@yahoo.fr', 1157561756, 1),
(66, 'xlesta@yahoo.com', 1157575811, 1),
(67, 'moi@yannicklaurent.info', 1157663473, 1),
(68, 'david.iachetta@ausy.be', 1157701178, 1),
(70, '', 1157704586, 1),
(71, 'ersin_26_@hotmail.com', 1157810950, 1),
(72, 'hammavitch@yahoo.fr', 1157829439, 1),
(73, 'greggabella@hotmail.com', 1157880375, 1),
(74, 'bafcomp@hotmail.com', 1157901170, 1),
(75, 'ptestud@caesar-web.com', 1157911680, 1),
(76, 'sgk9012@naver.com', 1157949774, 1),
(80, 'freddy.levee@ac-lille.fr', 1157993208, 1),
(81, 'ersin_26_@hotmail.com', 1158014856, 1),
(1144, 'didifatou1@yahoo.com', 1286484099, 5),
(84, 'magro71983@hotmail.com', 1158139109, 1),
(85, 'saomaidiemhen_85', 1158196529, 1),
(86, 'samettopcu@hotmail.com', 1158221847, 1),
(87, 'afup@fplanque.net', 1158252636, 1),
(88, 'brou_albret@yahoo.fr', 1158265653, 1),
(89, 'nana_djosseu@yahoo.fr', 1158310031, 1),
(91, 'me_spiritos@hotmail.com', 1158322865, 1),
(1147, 'depalmm@etu.u-cergy.fr', 1286786479, 5),
(93, 'francis.goubet@gmail.com', 1158327712, 1),
(94, 'datz@free.fr', 1158335410, 1),
(97, 'raphael@herody.com', 1158336716, 1),
(98, 'doyvali_2008@yahoo.com', 1158397144, 1),
(99, 'mih@icap.fr', 1158408033, 1),
(100, 'uldmail@gmail.com', 1158442017, 1),
(101, 'm.cornez@piritech.com', 1158487585, 1),
(102, 'herve.dubois@gmail.com', 1158514337, 1),
(104, 'fcardinaux@gmail.com', 1158555856, 1),
(106, 'bernede.eric@ccmsa.msa.fr', 1158562201, 1),
(108, 'Terry.Fahy@chrr.osu.edu', 1158586563, 1),
(109, 'gauthier@rivalis.fr', 1158590814, 1),
(110, 'ptestud@caesar-web.com', 1158605522, 1),
(111, 'ronan.denoual@hospimedia.fr', 1158609818, 1),
(113, 'fabien.catteau@skema.fr', 1158648836, 1),
(1146, 'fcaduc@gmail.com', 1286786479, 5),
(115, 'jmora@uoc.edu', 1158664523, 1),
(116, 'chalopin@syspertec.com', 1158668740, 1),
(117, 'loic.duvernay@synertrade.com', 1158677362, 1),
(118, 'mbravo@omegasolder.com.mx', 1158677895, 1),
(119, 'thierry.bertrand@equipement.gouv.fr', 1158682133, 1),
(120, 'info@55thinking.com', 1158684835, 1),
(1138, 'zaliyanna@yahoo.fr', 1286181237, 5),
(122, 'frederic.marchal@aktor.fr', 1158743823, 1),
(123, 'tmartin@capsule05.net', 1158751131, 1),
(124, 'smahe@univ-montp2.fr', 1158762801, 1),
(125, 'ludovic.lacaze@gmail.com', 1158831794, 1),
(126, 'vincent@scali.fr', 1158839821, 1),
(129, 'kenneth@himschoot.com', 1158860829, 1),
(130, 'bachcotsau@gmail.com', 1158880754, 1),
(137, 'valywebnet@yahoo.com', 1159052833, 1),
(138, 'v.briet@idf-services.fr', 1159086686, 1),
(139, 'jerome.charron@gmail.com', 1159129920, 1),
(140, 'arnaud@ligny.org', 1159136895, 1),
(141, 'poulainstephane@yahoo.fr', 1159170682, 1),
(142, 'fabien.potencier@symfony-project.com', 1159171250, 1),
(143, 'francis.nart@heliopsis.net', 1159196931, 1),
(144, 'sebastien.mannino@cadic.fr', 1159197997, 1),
(145, 'jeremie.patonnier@cetelem.fr', 1159257348, 1),
(146, 'wcandillon@gmail.com', 1159257911, 1),
(147, 'bernede.eric@ccmsa.msa.fr', 1159260077, 1),
(148, 'jgourmel@free.fr', 1159262978, 1),
(149, 'jgourmel@free.fr', 1159263010, 1),
(151, 'drissc@gmail.com', 1159282509, 1),
(152, 'eric.daspet@survol.net', 1159283512, 1),
(154, 'shadowkris@gmail.com', 1159301047, 1),
(155, 'guinesis@gmail.com', 1159301056, 1),
(156, 'guinesis@gmail.com', 1159301338, 1),
(157, 'kahina.idir@gmail.com', 1159352858, 1),
(158, 'vloquet@alx-communication.com', 1159370167, 1),
(159, 'cdurovray@free.fr', 1159450916, 1),
(1136, 'johann@applibox.com', 1285951081, 5),
(162, 'c.meynet@zeblue.com', 1159541968, 1),
(163, 'irakozejames1@yahoo.fr', 1159546130, 1),
(164, 'tnguyen@o2sources.com', 1159611835, 1),
(167, 'forumphp2006@chez.org', 1159689217, 1),
(168, 's2b@hotmail.com', 1159697549, 1),
(169, 'f.derfeuille@laposte.net', 1159703155, 1),
(170, 'h.mouhssine@gmail.com', 1159721862, 1),
(171, 'Terry.Fahy@chrr.osu.edu', 1159806271, 1),
(172, 'mathieu.laurent@gmail.com', 1159954213, 1),
(173, 'alexa@bluecode.cc', 1159955098, 1),
(174, 'BR@MULTI-ACTIVE.COM', 1159957464, 1),
(175, 'rock_gokhan@hotmail.com', 1159959540, 1),
(176, 'guillaume@internim.com', 1159971918, 1),
(177, 'hsefiani@free.fr', 1159975493, 1),
(178, 'greggabella@hotmail.com', 1159986414, 1),
(181, 'x.salama@yahoo.com', 1160004004, 1),
(182, 'francois.baligant@orange-ftgroup.com', 1160006207, 1),
(183, '', 1160011275, 1),
(187, 'sunnynhan@gmail.com', 1160031832, 1),
(189, 'pascal.coraboeuf@capgemini.com', 1160033808, 1),
(190, 'eric.daspet@survol.net', 1160034822, 1),
(191, 'amrtarek_2006@hotmail.com', 1160037713, 1),
(192, 'luca@pragmamedia.net', 1160038818, 1),
(1143, 'jailbreak@teamre.fr', 1286460121, 5),
(194, 'ibdlike@yahoo.fr', 1160040512, 1),
(195, 'albat@phpfrance.com', 1160045406, 1),
(196, 'bahargozlum341@hotmail.com', 1160046499, 1),
(197, 'alltrocs@yahoo.fr', 1160046764, 1),
(199, 'carlografica@yahoo.it', 1160053171, 1),
(200, 'ukentho@gmail.com', 1160061311, 1),
(201, 'stephane.blanchard@memobox.fr', 1160063744, 1),
(202, 'picharmol_angle@hotmail.com', 1160106038, 1),
(203, 'picharmol_angle@hotmail.com', 1160106235, 1),
(205, 'ricky10252000@yahoo.com.tw', 1160114257, 1),
(206, 'dinhhuy9983@yahoo.com', 1160117443, 1),
(208, 'julien_casanova@yahoo.fr', 1160126033, 1),
(209, '', 1160127021, 1),
(210, '', 1160127031, 1),
(211, 'aymenjradi@yahoo.fr', 1160127956, 1),
(212, 'aymenjradi@yahoo.fr', 1160128034, 1),
(213, 'dlesgourgues@free.fr', 1160131657, 1),
(214, 'ynave@directinfoservice.com', 1160139275, 1),
(215, 'jude_bazelais@yahoo.fr', 1160144481, 1),
(216, 'sb58@inbox.ru', 1160144730, 1),
(218, 'jacbrignon@online.fr', 1160150821, 1),
(219, 'moinleute@arcor.de', 1160162899, 1),
(222, 'ch_larbi@yahoo.fr', 1160185316, 1),
(224, 'jelaurent@wanadoo.fr', 1160211434, 1),
(225, 'ahmed.doua@gmail.com', 1160212559, 1),
(226, 'ahmed.doua@gmail.com', 1160212567, 1),
(227, 'nabagopalsaha@yahoo.com', 1160217261, 1),
(228, 'deanariel@gmail.com', 1160217663, 1),
(229, 'sedat_tiryaki_01@hotmail.com', 1160229908, 1),
(231, 'nokia.52000@hotmail.com', 1160235771, 1),
(232, 'samsonlo2004@yahoo.com.hk', 1160236996, 1),
(233, 'sunnyprincess@gmail.com', 1160237174, 1),
(234, 'icybob@gmail.com', 1160244404, 1),
(235, 'mandreletters@gmail.com', 1160253359, 1),
(236, 'aqs2999aqs@yahoo.com', 1160274487, 1),
(237, 'babayuksel@hotmail.com', 1160275687, 1),
(239, '', 1160316353, 1),
(240, 'marlena172@interia.pl', 1160318568, 1),
(241, 'scott9232004@yahoo.com.tw', 1160320183, 1),
(242, 'satapol2025@hotmail.com', 1160320620, 1),
(243, 'satapol2025@hotmail.com', 1160320750, 1),
(244, 'slayt_09', 1160326027, 1),
(245, 'fc@ambika.fr', 1160330662, 1),
(246, 'dadoubasange@hotmail.com', 1160339211, 1),
(249, '', 1160351415, 1),
(252, 'thehardway3000@YAHOO>COM', 1160358587, 1),
(253, '', 1160374471, 1),
(256, 'sobbooh@gmail.com', 1160384759, 1),
(257, 'kalelesl@hotmail.com', 1160394297, 1),
(258, '', 1160400661, 1),
(259, 'rbruyas@grandlyon.org', 1160401249, 1),
(260, 'rbruyas@grandlyon.org', 1160401629, 1),
(261, 'fcapelli@free.fr', 1160402943, 1),
(262, '', 1160403950, 1),
(263, 'ainreffas@yahoo.fr', 1160403957, 1),
(264, 'ainreffas@yahoo.fr', 1160404006, 1),
(266, 'vcaron@bearstech.com', 1160409498, 1),
(267, 'astarod@free.fr', 1160410660, 1),
(268, 'osadoun@gmail.com', 1160410764, 1),
(269, 'ombr@ombr.net', 1160414278, 1),
(270, 'tissou18@hotmail.fr', 1160423720, 1),
(271, 'raptor211', 1160431370, 1),
(272, 'raptor211@hotmail.com', 1160431386, 1),
(273, 'raptor211@hotmail.com', 1160431408, 1),
(274, '352302', 1160431674, 1),
(275, 'mekan_41_vatan_67@hotmail.com', 1160442068, 1),
(276, '', 1160449106, 1),
(277, 'vanphuong_dm@yahoo.com.vn', 1160454840, 1),
(278, 'vanphuong_dm@yahoo.com.vn', 1160454877, 1),
(279, 'vanphuong_dm@yahoo.com.vn', 1160454954, 1),
(280, '', 1160457630, 1),
(281, 'x', 1160457631, 1),
(283, 'haspinder@gmail.com', 1160469445, 1),
(285, 'ilies.halfaoui@gmail.com', 1160479212, 1),
(1140, 'sbeaupuis@lamaisondevalerie.com', 1286269673, 5),
(287, 'nicolas.zielinski@transatel.com', 1160490108, 1),
(288, 'khaled.labidi@transatel.com', 1160490149, 1),
(289, 'tn22tn@hotmail.com', 1160495622, 1),
(291, '', 1160501738, 1),
(292, 'inscr@meow.fr', 1160506182, 1),
(293, 'contact@pobrun.com', 1160519376, 1),
(294, 'kokai_1_corner@yahoo.com', 1160529449, 1),
(295, '', 1160534056, 1),
(296, '', 1160542874, 1),
(297, 'sie_liegt@hotmail.com', 1160548976, 1),
(299, 'raphael.veyrin-forrer@navx.com', 1160556920, 1),
(301, 'mehmet.demirkap@gmail.com', 1160564468, 1),
(304, 'nicolas.fabre@groupereflect.net', 1160567033, 1),
(305, 'denverporia18@yahoo.com.ph', 1160567547, 1),
(306, 'ftvgyhu', 1160569545, 1),
(307, 'ftvgyhu', 1160569680, 1),
(308, 'madi_mohamed72@yahoo.fr', 1160570711, 1),
(309, 'kiki_542@hotmail.com', 1160572003, 1),
(311, 'jvieilledent@lolart.net', 1160581742, 1),
(312, '', 1160582312, 1),
(314, 'cedric.anes@twenga.com', 1160587123, 1),
(315, 'wargla@gmail.com', 1160589127, 1),
(318, 'ladjos@msn.com', 1160609116, 1),
(319, 'ladjos@msn.com', 1160609157, 1),
(322, 'mohamed.moghrani@free.fr', 1160618574, 1),
(323, 'theson267@yahoo.com', 1160631988, 1),
(324, 'hoangtu269@yahoo.com', 1160632047, 1),
(325, 'zhoujijian8', 1160634799, 1),
(326, 'zhoujijian8', 1160634891, 1),
(327, 'yidaki@wanadoo.fr', 1160635215, 1),
(328, 'myhanh_it@yahoo.com', 1160636231, 1),
(329, 'usr@vp.pl', 1160641306, 1),
(330, 'cturbelin@free.fr', 1160643844, 1),
(331, 'jolin60540@yahoo.com.tw', 1160663361, 1),
(332, '', 1160663405, 1),
(333, 'zhangganxiang@163.com', 1160664206, 1),
(334, 'lhenry@lhenry.com', 1160667150, 1),
(336, 's.arnoult@theatrealacarte.fr', 1160668898, 1),
(337, 'pameline@uccife.org', 1160684886, 1),
(339, 'caq1005', 1160690465, 1),
(340, 'sabeti1@gmail.com', 1160694348, 1),
(341, 'xartotal@hotmail.com', 1160700151, 1),
(342, '1232321', 1160710780, 1),
(343, 'www.juventus.or.id', 1160715788, 1),
(344, 'isabelle.gerard@oneaccess-net.com', 1160723598, 1),
(345, 'hermann@abaxe.net', 1160723671, 1),
(347, 'informatique@piscineservice.com', 1160728640, 1),
(348, '&#3649;&#3612;&#3609;&#3607;&#3637;&#3656;&#3650;&#3621;&#3585;&#3612;&#3656;&#3634;&#3609;&#3604;&#3634;&#3623;&#3648;&#3607;&#3637;&#3618;&#3617;', 1160728711, 1),
(349, 'taner_black@hotmail.com', 1160730264, 1),
(351, 'cengiz1727@hotmail.com', 1160742690, 1),
(352, 'tom@fashion-job.com', 1160745171, 1),
(353, '', 1160746705, 1),
(355, '', 1160750349, 1),
(356, 'william.verdeil@amdm.fr', 1160750495, 1),
(357, 'herick@terra.com.co', 1160750648, 1),
(358, '', 1160751548, 1),
(360, 'c.spy@fotovista.com', 1160756638, 1),
(362, '', 1160759562, 1),
(363, '', 1160759563, 1),
(364, 'christophe.collot@akka.fr', 1160763088, 1),
(366, 'VEZE_Pascal@sdis24.fr', 1160765038, 1),
(367, 'VEZE_Pascal@sdis24.fr', 1160765048, 1),
(368, 'jacqueline.houpin@wanadoo.fr', 1160765389, 1),
(369, 'eric.jbn@piwiz.com', 1160768280, 1),
(370, 'rachid.el.hilali@caramail.com', 1160770772, 1),
(371, '', 1160774061, 1),
(372, 'nyx@cxibe.com', 1160775049, 1),
(373, 'burock9@hotmail.com', 1160779057, 1),
(374, 'elmha@free.fr', 1160785818, 1),
(375, 'qamar_ali23@yahoo.com', 1160788433, 1),
(376, 'waini5151', 1160803617, 1),
(377, '', 1160822156, 1),
(378, '', 1160822291, 1),
(379, 'lydri@free.fr', 1160826607, 1),
(380, 'yinxuezhivip@yahoo.com.cn', 1160839781, 1),
(382, 'gula.islam@mynet.com', 1160852547, 1),
(385, '', 1160872433, 1),
(387, 'mehmetparlakyigit@yahoo.com', 1160899424, 1),
(388, 'thierry.semo@gmail.com', 1160904552, 1),
(390, 'dlg.nguyen@gmail.com', 1160916548, 1),
(391, 'aaa@mynet.com', 1160920489, 1),
(392, 'mynont@gmail.com', 1160927399, 1),
(393, 'thindstudio@yahoo.com', 1160931086, 1),
(394, 'sam.perrot@free.fr', 1160934125, 1),
(396, 'axead  z', 1160965342, 1),
(397, 'alaswany_9@yahoo.com', 1160965826, 1),
(399, '', 1160976705, 1),
(402, '', 1160987993, 1),
(403, 'satapol2025@hotmail.com', 1160988777, 1),
(404, 'pierre@sampit.be', 1160993284, 1),
(405, 'zekeriyaersan_58@hotmail.com', 1160997049, 1),
(406, 'zlj3633@163.com', 1161000515, 1),
(407, 'springal527@hotmail.com', 1161002210, 1),
(408, 'vlambert@microapp.com', 1161006278, 1),
(410, 'romulus41@free.fr', 1161009947, 1),
(411, 'arnomasse@hotmail.com', 1161009979, 1),
(412, 'debbabi@enst.fr', 1161012114, 1),
(414, '', 1161017595, 1),
(415, '', 1161018673, 1),
(416, 'jokkymen@yahoo.com', 1161021485, 1),
(417, 'petitpare@yahoo.fr', 1161037101, 1),
(419, 'forum@programshop.com', 1161041246, 1),
(420, 'essaidoubihi@gmail.com', 1161042902, 1),
(421, '', 1161049074, 1),
(422, 'prometheus_turuncu_kafaa@hotmail.com', 1161059955, 1),
(423, '', 1161061640, 1),
(424, 'hasnat.tanvir@gmail.com', 1161065694, 1),
(425, 'rotbe1@gmail.com', 1161069433, 1),
(426, 'wajih.ouertani@gmail.com', 1161072841, 1),
(427, 'anonymousguy007@yahoo.com', 1161076236, 1),
(428, 'benjamin.lacaze@entic.fr', 1161078885, 1),
(429, 'benjamin.lacaze@entic.fr', 1161078903, 1),
(430, 'rubel666@o2.pl', 1161081958, 1),
(431, 'zlj3633@163.com', 1161088436, 1),
(432, 'daniel_colin31@yahoo.fr', 1161088717, 1),
(433, 'akash_rockstar@yahoo.com', 1161088981, 1),
(434, 'zlj3633@163.com', 1161090076, 1),
(435, 'zlj3633@163.com', 1161090769, 1),
(436, '', 1161092075, 1),
(438, 'xulaoyintou@163.com', 1161095567, 1),
(439, 'lehoan55', 1161097937, 1),
(441, 'kami', 1161101089, 1),
(442, 'opropsoh@yahoo.fr', 1161105955, 1),
(443, '', 1161112600, 1),
(444, '', 1161115139, 1),
(445, '', 1161133186, 1),
(446, 'c', 1161133590, 1),
(449, '', 1161153950, 1),
(450, 'hoangnghiactcusc@yahoo.com', 1161156811, 1),
(451, 'hoangnghiactcusc@yahoo.com', 1161156957, 1),
(452, 'sahar-moussa@hotmail.com', 1161158266, 1),
(454, 'g.dumas@sportlab.fr', 1161160931, 1),
(456, 'stephane.dekeyzer@irm-kmi.be', 1161173810, 1),
(457, 'alan_kat2000@yahoo.fr', 1161175773, 1),
(458, 'herve@infonetik.fr', 1161177008, 1),
(460, 'priyadarsh.shaurya@gmail.com', 1161177450, 1),
(461, 'priyadarsh.shaurya@gmail.com', 1161177484, 1),
(462, 'saad_hosam@hotmail.com', 1161193551, 1),
(463, 'jord_sapan@hotmail.com', 1161233844, 1),
(465, 'yaissaoui@yahoo.fr', 1161241119, 1),
(467, 'realpix@wanadoo.fr', 1161256066, 1),
(468, 'nguyenhoangjacques@yahoo.fr', 1161271071, 1),
(469, '', 1161278515, 1),
(470, '4je4jwr', 1161279379, 1),
(471, 'shabnam_ro2006@yahoo.com', 1161279604, 1),
(472, 'kevin@botstats.com', 1161280787, 1),
(473, 'webmaster@ventdange.com', 1161280808, 1),
(474, 'psou.listes@free.fr', 1161325165, 1),
(475, 'priscillia.bigorgne@gmail.com', 1161333338, 1),
(476, 'petra.drechsel@t-online.de', 1161348432, 1),
(478, 'fer', 1161368390, 1),
(479, 'ferhat_agit_1@hotmail.com', 1161368420, 1),
(480, '', 1161374541, 1),
(481, 'tulbea@mail.ru', 1161428071, 1),
(482, 'ne.eeckhout@pandora.be', 1161436969, 1),
(483, 'sergio_duran_132@hotmail.com', 1161493259, 1),
(484, 'm_casavecchia@yahoo.com', 1161500964, 1),
(485, 'rose.guillaume@free.fr', 1161522135, 1),
(487, 'youssef', 1161549183, 1),
(488, '2coco@chello.nl', 1161551440, 1),
(490, 'd_zanardo3@yahoo.fr', 1161590741, 1),
(491, 'epoisson@gaitesh.org', 1161591571, 1),
(492, 'moad2mf', 1161602032, 1),
(495, 'marcelhaudentz@yahoo.fr', 1161726453, 1),
(497, 'julien.sanchez@insa-lyon.fr', 1161773011, 1),
(498, 'julien@formagora.fr', 1161774362, 1),
(500, 'marcel--supa@hotmail.com', 1161793931, 1),
(501, 'u789u90', 1161795067, 1),
(502, 'fourat@gmail.com', 1161855996, 1),
(503, '', 1161864031, 1),
(504, 'bellezay@yahoo.fr', 1161949629, 1),
(505, 'lolus92@free.fr', 1161953492, 1),
(506, 'sebastien@lesgarsdulabo.com', 1161954946, 1),
(507, '', 1161959398, 1),
(508, 'david.oulhen@univ-mlv.fr', 1161961121, 1),
(509, 'baconseil@yahoo.com', 1161962725, 1),
(510, 'david.oulhen@univ-mlv.fr', 1161962760, 1),
(512, 'gresmini@webnet.fr', 1161986477, 1),
(513, 'darksitar@gmail.com', 1162028688, 1),
(515, 'r_rojgar@yahoo.com', 1162044180, 1),
(516, 'oussamahannou@yahoo.fr', 1162051409, 1),
(517, 'taguemount.nacer@free.fr', 1162072663, 1),
(518, '', 1162119749, 1),
(519, 'deo54@caramail.com', 1162200016, 1),
(520, 'info@swisscad.com', 1162202042, 1),
(521, 'remi.le-lous@wanadoo.fr', 1162213539, 1),
(522, 'php@norman-godwin.com', 1162238511, 1),
(524, 'mic@microprose.be', 1162286249, 1),
(525, 'mic@microprose.be', 1162286275, 1),
(526, 'mic@microprose.be', 1162286364, 1),
(527, '', 1162290450, 1),
(529, 'dmonet@pontmirabeau.com', 1162320457, 1),
(530, 'ebesobe_6@hotmail.com', 1162374806, 1),
(531, 'bahloulitsi@hotmail.com', 1162397623, 1),
(532, '', 1162432609, 1),
(533, '', 1162465567, 1),
(534, 'tuoihoctro_20_04@yahoo.com', 1162473524, 1),
(535, 'christophe.moine_afup@gadz.org', 1162476087, 1),
(536, 'garciomar@yahoo.fr', 1162491751, 1),
(537, 'remate0088@hotmail.com', 1162493224, 1),
(538, 'jmpreira@yahoo.fr', 1162499720, 1),
(539, 'jmpreira@yahoo.fr', 1162499767, 1),
(540, '', 1162557796, 1),
(541, 'bernard.barral@francetelecom.com', 1162594166, 1),
(542, 'kenneth@himschoot.com', 1162645207, 1),
(543, '', 1162681102, 1),
(544, 'yan2506@gmail.com', 1162696301, 1),
(545, 'ahmedlaafta@yahoo.com', 1162712888, 1),
(546, 'ahmedlaafta@yahoo.com', 1162712972, 1),
(547, 'lyazidk@dial.oleane.com', 1162720782, 1),
(548, '98', 1162723668, 1),
(549, 'david.oulhen@univ-mlv.fr', 1162732629, 1),
(550, 'sihem-insim@hotmail.com', 1162735331, 1),
(551, 'lacetuce@yahoo.fr', 1162736328, 1),
(555, 'v.quino@free.fr', 1191068470, 2),
(556, 'tiago_fr@hotmail.com', 1191077116, 2),
(557, 'lhenry@lhenry.com', 1191087133, 2),
(558, 'webmaster@apprendre-php.com', 1191139021, 2),
(561, 'laurent@eroket.com', 1191187755, 2),
(562, 'joachimdesa@gmail.com', 1191221877, 2),
(563, 'sj@chewing-com.com', 1191227677, 2),
(564, 'arnaud.ligny@baobaz.com', 1191227917, 2),
(565, 'mathieu.laurent@gmail.com', 1191229936, 2),
(566, 'jean-pierre.leclezio@bnpparibas.com', 1191239175, 2),
(567, 'martin@supiot.net', 1191242716, 2),
(568, 'cardinaux@uicc.org', 1191246301, 2),
(569, 'Terry.Fahy@chrr.osu.edu', 1191271208, 2),
(570, 'gregory.chevret@unilog.logicacmg.com', 1191276903, 2),
(571, 'dasenkat@gmail.com', 1191312818, 2),
(572, 'gilles.fevrier@bull.net', 1191313584, 2),
(573, 'jujusuper54@gmail.com', 1191322699, 2),
(574, 'chalopin@syspertec.com', 1191325618, 2),
(591, 'mr.thiriot@gmail.com', 1191668850, 2),
(590, '', 1191638636, 2),
(1137, 'dr.biynze@bnd-consulting.com', 1285988636, 5),
(579, 'jmathis@merethis.com', 1191357987, 2),
(580, 'paul.michalet@gmail.com', 1191367710, 2),
(581, 'sasiela@u707.jussieu.fr', 1191401563, 2),
(582, 'p.gautier@astellia.com', 1191406519, 2),
(583, 'benjamin.bouche@supinfo.com', 1191413668, 2),
(584, 'contact@vairet.net', 1191421020, 2),
(585, 'ltsn@free.fr', 1191442606, 2),
(586, 'porhan@ceasycom.com', 1191484465, 2),
(587, 'johnson.1933@osu.edu', 1191505148, 2),
(588, 'jeanmichel.delehaye@qsms.fr', 1191506477, 2),
(592, 'eolenomade@yahoo.fr', 1191750295, 2),
(593, 'm.olivier@devbasic.net', 1191875988, 2),
(594, 'marc_lamour@hotmail.com', 1191914869, 2),
(596, 'pascal@lunebleue.org', 1191965624, 2),
(597, 'webmaster@ventdange.com', 1191967057, 2),
(598, 'now868@gmail.com', 1191980918, 2),
(599, 'jjj@tech3j.com', 1192008817, 2),
(600, 'mdujardin@aliantisinvest.com', 1192021994, 2),
(601, 'matthieu.doresse@abcube.com', 1192037129, 2),
(602, 'francois@fourrier.com', 1192047616, 2),
(603, 'maskas@c9radio.fr', 1192088195, 2),
(604, 'rena200377@yahoo.com', 1192177349, 2),
(607, 'b2ba@hotmail.com', 1192181061, 2),
(608, 'francois.greze.mail@free.fr', 1192212888, 2),
(609, 'eric@corsicaweb.fr', 1192392557, 2),
(610, 'crimso@crimso.com', 1192396288, 2),
(611, 'emmanuel.triballier@free.fr', 1192398624, 2),
(612, 'laligatz@gmail.com', 1192399360, 2),
(613, 'antoine.delvaux@adfinance.org', 1192435471, 2),
(615, '', 1192756202, 2),
(616, 'olivier.clavel@popfactory.fr', 1192783269, 2),
(617, 'bourda2@hotmail.fr', 1192906685, 2),
(618, 'sziemele@yahoo.fr', 1192953624, 2),
(619, '', 1193081479, 2),
(620, 'yolande.lebouteiller@urssaf.fr', 1193219197, 2),
(621, 'elisabeth.colombo@sacijo.fr', 1193222445, 2),
(623, 'lysbeth@hotmail.fr', 1193298274, 2),
(625, 'ejonas@webjonas.com', 1193319261, 2),
(626, 'kty@hotmail.com', 1193336937, 2),
(629, 'gffg@aol.com', 1193348191, 2),
(636, 'aivo.schults%40mail.ee', 1193403055, 2),
(637, 'cecile.chatellier@gmail.com', 1193404918, 2),
(638, 'vvb@yahoo.com', 1193452163, 2),
(639, 'antoine.sottiau@gmail.com', 1193562236, 2),
(640, '\\''', 1193569301, 2),
(641, 'tru565@mvm.com', 1193594237, 2),
(643, 'arnaud.siminski@infotel.com', 1193646973, 2),
(644, 'alexis.antoinat@neuf.fr', 1193665257, 2),
(645, 'rachid.el.hilali@caramail.com', 1193741953, 2),
(647, 'jeanbaptiste.goupille@gmail.com', 1193816889, 2),
(648, 'contact@formagora.fr', 1193841411, 2),
(649, 'm.giry@epixelic.com', 1193862239, 2),
(650, 'vincedo@gmail.com', 1193868950, 2),
(651, 'jean-yves@4x4rdv.com', 1193937320, 2),
(652, 'christian@berthomieu.fr', 1193959334, 2),
(656, 'dridounet@hotmail.com', 1194114405, 2),
(657, 'laurent.masclet@masclet-associates.com', 1194123256, 2),
(658, 'chetcheverry@wanadoo.fr', 1194165036, 2),
(659, 'yytty@hotmail.com', 1194196522, 2),
(660, 'nhy45@yahoo.com', 1194246121, 2),
(661, 'neobaub@gmail.com', 1194257769, 2),
(662, '', 1194264020, 2),
(663, '', 1194264029, 2),
(665, 'azs34@aol.com', 1194309025, 2),
(666, 'sgu@aql.fr', 1194336604, 2),
(667, 'bvv6@nc.tb', 1194337151, 2),
(668, 'ffdf@op.net', 1194363254, 2),
(669, 'manuel.ducruet@gmail.com', 1194423757, 2),
(671, 'kgh@poj.lp', 1194454484, 2),
(672, 'ludovic.lacaze@gmail.com', 1194455932, 2),
(673, 'jhjh@aol.com', 1194475257, 2),
(674, 'fatahanfar@hotmail.com', 1194527955, 2),
(675, 'yuy@hotmail.com', 1194590636, 2),
(677, 'mayfarine@hotmail.com', 1194596820, 2),
(678, 'eristeve@hotmail.com', 1194615482, 2),
(679, 'petrus_ph@yahoo.fr', 1194621501, 2),
(680, '', 1194623181, 2),
(681, 'jlsavary@printsoft.fr', 1194632955, 2),
(683, 'viagra@yandex.com', 1194655038, 2),
(684, 'franck.tissier@neuf.fr', 1194682298, 2),
(685, 'rr_style@yahoo.fr', 1194690137, 2),
(686, 'vvb@yahoo.com', 1194700376, 2),
(687, 'yuy@hotmail.com', 1194760772, 2),
(689, 'valgemaja.ehitus@40mail.ee', 1194779903, 2),
(690, 'xcc@nvn.kz', 1194810932, 2),
(691, 'kadavites@yahoo.fr', 1194873400, 2),
(692, 'yuy@hotmail.com', 1194880274, 2),
(693, 'adel_first@yahoo.fr', 1194943867, 2),
(694, 'biros09@free.fr', 1194946446, 2),
(695, 'ltsn@free.fr', 1194949103, 2),
(696, 'sami@net-sam.com', 1194961930, 2),
(697, 'arnaud.tisset@gmail.com', 1194969286, 2),
(698, 'emmanuel.triballier@free.fr', 1194998349, 2),
(700, 'sflores@opensistemas.com', 1195054655, 2),
(701, '848ut@was.com', 1195084015, 2),
(702, 'zetoutou@yahoo.fr', 1195122223, 2),
(703, 'fboury@lequipe.fr', 1195128496, 2),
(706, 'tyyt@hotmail.com', 1195185459, 2),
(707, 'saael@hotmail.fr', 1195209781, 2),
(708, 'plasnier@jouve.fr', 1195222317, 2),
(709, 'tyyt@hotmail.com', 1195245628, 2),
(711, 'bnn@hotmail.com', 1195351898, 2),
(712, 'oyyu78@aol.com', 1195367798, 2),
(713, 'bourda2@hotmail.fr', 1195383658, 2),
(714, 'manuel.ducruet@gmail.com', 1195421470, 2),
(715, 'd.eser@hotmail.fr', 1195452686, 2),
(716, 'gds@hotmail.com', 1195455140, 2),
(718, 'yuy@hotmail.com', 1195561760, 2),
(1139, 'vincent_brisse@hotmail.com', 1286200532, 5),
(720, 'xcc@nvn.kz', 1195652248, 2),
(721, 'azs34@aol.com', 1195670790, 2),
(722, 'mnbm@nbvm.net', 1195690638, 2),
(723, '', 1195696311, 2),
(725, 'lyazidk@dial.oleane.com', 1195725059, 2),
(728, 'perrick@noparking.net', 1211039730, 3),
(729, 'guillaume.turri@gmail.com', 1211204945, 3),
(730, 'melvin.kianmanesh@hotmail.fr', 1211216671, 3),
(731, 'technique@restoclub.fr', 1211228020, 3),
(732, 'sebastien.lucas@oxalide.com', 1211228064, 3),
(733, 'contact@yannicklaurent.info', 1211230435, 3),
(734, 'willfriednguessan@yahoo.fr', 1211262502, 3),
(735, 'stephane.dekeyzer@irm-kmi.be', 1211282036, 3),
(736, 'exuper.ok@gmail.com', 1211282511, 3),
(738, 'ctri2008@hotmail.com', 1211389867, 3),
(739, 'gordonf69@free.fr', 1211449606, 3),
(855, 'guillaume@gmi-connectivity.com', 1220447720, 3),
(744, 'rodolphe@pdaproject.com', 1211899966, 3),
(746, 'sebastien.gastard@eurorscg.fr', 1211979905, 3),
(747, 'luddic@gmail.com', 1212002864, 3),
(748, 'jeff@deepbass.net', 1212058789, 3),
(749, 'laurent.minguet@gadz.org', 1212075079, 3),
(751, 'angelabello80@alice.it', 1212260539, 3),
(752, 'aguyon@churchill.fr', 1212411943, 3),
(753, 'andrewsilka@gmail.com', 1212488625, 3),
(754, 'ivanohe22@gmail.com', 1212621947, 3),
(755, 'therond@idris.fr', 1212671578, 3),
(756, 'e.bougerolle@gmail.com', 1212672328, 3),
(757, 'mister2tense@gmail.com', 1212720655, 3),
(758, 'mickael.kwasnik@anakeen.com', 1212758225, 3),
(759, 'webmaster@apprendre-php.com', 1212779688, 3),
(760, 'webmaster@apprendre-php.com', 1212779688, 3),
(761, 'gilbert.musnik@fr.adp.com', 1212781985, 3),
(762, 'ndesaleux+afup@gmail.com', 1212920669, 3),
(763, 'sebastien.dudek@slashon.com', 1212931095, 3),
(764, 'christine.deffaix-remy@ociensa.com', 1212947117, 3),
(765, 'baradjibares@yahoo.fr', 1213003022, 3),
(766, 'forumphp2008@lamouret.net', 1213194707, 3),
(767, 'tonio607@yahoo.fr', 1213566251, 3),
(768, 'matthieu@bienavous.be', 1213589807, 3),
(769, 'e.daniel@export-entreprises.com', 1213603269, 3),
(770, 'sgu@aql.fr', 1213702591, 3),
(771, 'tsyr2ko-divers@yahoo.fr', 1213803726, 3),
(772, 'ffesch@digitas.com', 1213973188, 3),
(774, 'hoareau.olivier@gmail.com', 1214425315, 3),
(775, 'mehdizsoft@hotmail.com', 1214428911, 3),
(776, 'mehdizsoft@hotmail.com', 1214428935, 3),
(777, 'thomas.nico@free.fr', 1214863502, 3),
(778, 'b.agier@les-gd.com', 1215090630, 3),
(779, 'syrus.levirus@gmail.com', 1215098804, 3),
(780, 'orionzfire@gmail.com', 1215177057, 3),
(781, 'smathon@phpquebec.org', 1215286350, 3),
(782, 'philippe_raoul4@yahoo.fr', 1215362084, 3),
(783, 'enyfr@yahoo.fr', 1215440543, 3),
(784, 'd0__@hotmail.fr', 1215461890, 3),
(785, 'methylbro@titaxium.org', 1215503418, 3),
(786, 'francrodriguez@gmail.com', 1215523883, 3),
(787, 'anismam@gmail.com', 1215608146, 3),
(788, 'deep-snow@hotmail.fr', 1215628054, 3),
(789, 'leleu.victorien@gmail.com', 1215698941, 3),
(790, 'barthelemy.seb@gmail.com', 1215716038, 3),
(791, 'gregory.capelle@gmail.com', 1216051993, 3),
(792, 'php@lamouret.net', 1216107920, 3),
(793, 'olivier.kingdavid@gmail.com', 1216111295, 3),
(794, 'info@tagexpert.be', 1216130042, 3),
(795, 'fdantinne@clef2web.be', 1216188625, 3),
(796, 'm.levy@mrj-corp.fr', 1216222669, 3),
(797, 'neopheus@gmail.com', 1216280227, 3),
(798, 'julien.prigent@dbmail.com', 1216282282, 3),
(799, 'sebastien.dudek@slashon.com', 1216298495, 3),
(800, 'fran.cornu@free.fr', 1216505050, 3),
(801, 'marc.lopes.pro@gmail.com', 1216565035, 3),
(802, 'sbool666@gmail.com', 1216592264, 3),
(803, 'contact@creamotion.com', 1216595465, 3),
(854, 'eric.mezerette@unicaen.fr', 1220447350, 3),
(805, 'philippe_raoul4@yahoo.fr', 1216736022, 3),
(806, 'th3.scorpi0n@gmail.com', 1216759890, 3),
(807, 'rrvijaykumar@gmail.com', 1217046718, 3),
(808, 'hameshiv@gmail.com', 1217183374, 3),
(809, 'gauthier@rivalis.fr', 1217400064, 3),
(810, 'm.collomb@abileo.com', 1217431175, 3),
(811, 'contact@julienbreux.com', 1217442577, 3),
(812, 'gilles_demaret@yahoo.fr', 1217511226, 3),
(814, 'samuel.verdier@pyxis.org', 1217714390, 3),
(815, 'erwan.grooters@alphanetworks.be', 1217839187, 3),
(816, 'datalion@gmail.com', 1218012304, 3),
(817, 'jfm@yakafaire.be', 1218013553, 3),
(818, 'jfm@yakafaire.be', 1218013677, 3),
(819, 'nicolas.semczyk@gmail.com', 1218015941, 3),
(820, 'contact@julienbreux.com', 1218027879, 3),
(821, 'pierre.hanselmann@smallbiz.ch', 1218182302, 3),
(822, '40106@supinfo.com', 1218205433, 3),
(823, 'gustsoub@yahoo.fr', 1218221651, 3),
(824, 'gustsoub@yahoo.fr', 1218221684, 3),
(825, 'referencement@thesiteoueb.net', 1218287759, 3),
(826, 'wadzar@gmail.com', 1218321473, 3),
(828, 'mickael.maison@gmail.com', 1218629649, 3),
(829, 'chiker_k@yahoo.fr', 1218658171, 3),
(830, 'sbool666@gmail.com', 1218728470, 3),
(831, 'meknesrachide@yahoo.fr', 1218811306, 3),
(832, 'eric.morvan@gmail.com', 1219046862, 3),
(833, 'fcardinaux@gmail.com', 1219121998, 3),
(834, 'maskas@free.fr', 1219133505, 3),
(835, 'mrambil@gmail.com', 1219146722, 3),
(836, 'olivier@grandmougin.net', 1219147263, 3),
(837, 'rquintin@sqli.com', 1219236321, 3),
(838, 'nicols.blin@sensio.com', 1219249329, 3),
(839, 'thomas.gasc@methylbro.fr', 1219263092, 3),
(840, 'dinidu_su@yahoo.com', 1219267226, 3),
(841, 'romain.sarels@pubeco.fr', 1219305450, 3),
(842, 'dmeance@gmail.com', 1219308198, 3),
(843, 'francoisgallienne@gmail.com', 1219322147, 3),
(844, 'mohamed.jemai@agencekarismatik.com', 1219501688, 3),
(845, 'g.rossolini@gmail.com', 1219689698, 3),
(846, 'kevin@saliou.name', 1219738665, 3),
(847, 'fradet.kevin@gmail.com', 1219764180, 3),
(848, 'fradet.kevin@gmail.com', 1219764265, 3),
(849, 'yann@hypolais.fr', 1219839550, 3),
(850, 'contact@thomasbeaucourt.com', 1219844120, 3),
(851, 'barthelemy.seb@gmail.com', 1219937814, 3),
(852, 'pierre@sampit.be', 1219997450, 3),
(853, 'jfbustarret@wat.tv', 1220272317, 3),
(856, 'ludovic.lacaze@gmail.com', 1220469269, 3),
(857, 'sylvain.joncour@gmail.com', 1220513809, 3),
(858, 'pylb@anao.fr', 1220542807, 3),
(859, 'olivier.gouzien@fr.nurun.com', 1220602451, 3),
(860, 'eveilleau.rodolphe@gmail.com', 1220606644, 3),
(861, 'bguerin@sqli.com', 1220620960, 3),
(862, 'ndesaleux@gmail.com', 1220692088, 3),
(863, 'haknaton@gmail.com', 1220824471, 3),
(864, 'manuel.ducruet@gmail.com', 1220856790, 3),
(865, 'erwan.grooters@alphanetworks.be', 1220865400, 3),
(867, 'samuel.verdier@gmail.com', 1220882158, 3),
(868, 'jeremy.barthe@gmail.com', 1220882182, 3),
(870, 'guillaume@internim.com', 1220890368, 3),
(871, 'frank.dillenseger@interieur.gouv.fr', 1220948348, 3),
(872, 'barthelemy.seb@gmail.com', 1220948659, 3),
(873, 'jcerdan@tecob.com', 1220949273, 3),
(874, 'florent.messa@gmail.com', 1220980388, 3),
(875, 'developpement@ociensa.com', 1221038351, 3),
(876, 'x.briand@communiquez-plus.com', 1221048849, 3),
(877, 'vincent@callut.be', 1221049793, 3),
(878, 'lbolzer@eskalad.net', 1221051935, 3),
(879, 'lbolzer@eskalad.net', 1221051957, 3),
(880, 'audreyroch.houssou@gmail.com', 1221056157, 3),
(881, 'royneau@gmail.com', 1221058984, 3),
(882, 'fabrice.terrasson@gmail.com', 1221062570, 3),
(883, 'oclavel@kaliop.com', 1221118603, 3),
(884, 'joachimarditti@yahoo.fr', 1221122234, 3),
(885, 'nicolaslesconnec@gmail.com', 1221143592, 3),
(886, 'mathieu@visual-link.fr', 1221152325, 3),
(887, 'dayota@gmail.com', 1221153649, 3),
(888, 'thomas.gasc@methylbro.fr', 1221255262, 3),
(890, 'msenterprice05@yahoo.com', 1221478977, 3),
(891, 'kevin@saliou.name', 1221480238, 3),
(892, 'florian.seuret@he-arc.ch', 1221542590, 3),
(893, 'maja@wowm.org', 1221558515, 3),
(894, 'benoit.capallere@gmail.com', 1221566997, 3),
(895, 'lhenry@lhenry.com', 1221571944, 3),
(896, 'sgu@aql.fr', 1221573172, 3),
(897, 'aolier@microsoft.com', 1221577441, 3),
(898, 'parisdns@gmail.com', 1221652724, 3),
(899, 'david.rechatin@zoomacom.org', 1221659585, 3),
(900, 'marc.vachette@gmail.com', 1221721685, 3),
(901, 'n.namont@uniteam.fr', 1221739916, 3),
(902, 'turk-genci@hotmail.fr', 1221857094, 3),
(904, 'hurdleur@yahoo.fr', 1221941904, 3),
(905, 'contact@vaisonet.com', 1221978607, 3),
(907, 'joachimdesa@gmail.com', 1222150583, 3),
(909, 'afup.org@barresi.ch', 1222166470, 3),
(910, 'jpecqueur@gmail.com', 1222181303, 3),
(911, 'madislak@yahoo.fr', 1222191614, 3),
(912, 'bernard.barral@orange-ftgroup.com', 1222241974, 3),
(913, 'grey.fabien@gmail.com', 1222245748, 3),
(914, 'xavier.vancrombrugghe@team.skynet.be', 1222249001, 3),
(915, 'vincent.delaval@mediasmart.fr', 1222268535, 3),
(916, 'jcerdan@tecob.com', 1222325511, 3),
(917, 'morgaut@hotmail.com', 1222331041, 3),
(918, 'maskas@free.fr', 1222339152, 3),
(919, 'laurent@believe.fr', 1222347058, 3),
(920, 'pa.lesaignoux@thecodingmachine.com', 1222417770, 3),
(921, 'olivier.larcheveque@gmail.com', 1222463325, 3),
(922, 'loloontheair@me.com', 1222518826, 3),
(924, 'doc_hash@hotmail.com', 1222785509, 3),
(925, 'simon@kornog-computing.com', 1222850403, 3),
(926, 'jy@lozach.com', 1222855500, 3),
(927, 'tnivot@eurocortex.fr', 1222855533, 3),
(928, 'yannick.lalleau@pubeco.fr', 1222889592, 3),
(929, 'luddic@gmail.com', 1222901373, 3),
(930, 'mr.thiriot@gmail.com', 1222926890, 3),
(931, 'mehdi.kahtane@mundigo.com', 1222937870, 3),
(932, 'shordeaux@waterproof.fr', 1222958193, 3),
(933, 'fabien.pennequin@gmail.com', 1222972898, 3),
(934, 'seb@claroline.net', 1223022717, 3),
(935, 'vincent@callut.be', 1223024756, 3),
(936, '', 1223039643, 3),
(937, 'roller-girl@hotmail.fr', 1223152781, 3),
(938, 'francois@fourrier.com', 1223300179, 3),
(939, 'noe.froidevaux@gmail.com', 1223364390, 3),
(940, 'neveldo@gmail.com', 1223372593, 3),
(941, 'tlongis@tf1.fr', 1223475892, 3),
(942, 'tonio607@yahoo.fr', 1223484149, 3),
(943, 'oliviernsiku@yahoo.fr', 1223493294, 3),
(945, 'webinadiv@gmail.com', 1223551954, 3),
(946, 'ffesch@digitas.com', 1223580625, 3),
(947, '', 1223637121, 3),
(948, 'brice.favre@gmail.com', 1223814725, 3),
(949, 'antoine@origan.fdn.fr', 1223927890, 3),
(950, 'naerleth@gmail.com', 1223973522, 3),
(951, '', 1224062125, 3),
(952, 'tho78tlse@yahoo.fr', 1224076283, 3),
(953, 'barthelemy.seb@gmail.com', 1224189394, 3),
(954, 'osarrat@urd.org', 1224243504, 3),
(955, 'marclaporte@tikiwiki.org', 1224387477, 3),
(956, 'pacogliss@yahoo.fr', 1224506328, 3),
(957, 'dborel@orupaca.fr', 1224507310, 3),
(958, 'hello@hello-design.fr', 1224544711, 3),
(959, 'contact@hakadel.com', 1224587732, 3),
(960, 'francois.barbut@chapatiz.com', 1224664523, 3),
(961, 'audrey.delaet@genopole.fr', 1224768600, 3),
(962, 'martin@supiot.net', 1224769979, 3),
(963, 's-pottier@laposte.net', 1224922962, 3),
(964, 'pierre.pene@sibeo.fr', 1225012889, 3),
(965, 'lacetuce@yahoo.fr', 1225095512, 3),
(966, 'mehdi@mundigo.com', 1225103791, 3),
(967, 'neveldo@gmail.com', 1225116969, 3),
(968, 'evoilliot@micropole-univers.com', 1225118851, 3),
(969, 'elrod@free.fr', 1225129667, 3),
(970, 'iorga@iorga.com', 1225132504, 3),
(971, 'kazira.b@live.fr', 1225183437, 3),
(973, 'contact@concept-internet.net', 1225213063, 3),
(974, 'ronan.denoual@hospimedia.fr', 1225217595, 3),
(975, 'm.collomb@abileo.com', 1225271838, 3),
(977, 'lgiorgi@algam.net', 1225384802, 3),
(978, 'pierre.beaumadier@gmail.com', 1225447346, 3),
(979, 'benjamin.bouche@supinfo.com', 1225451980, 3),
(980, 'martin@supiot.net', 1225537018, 3),
(981, 'bangOvince@hotmail.fr', 1225709712, 3),
(982, '', 1225734400, 3),
(983, 'romain.boyer@gmail.com', 1225822064, 3),
(984, 'ajad-it@orange.fr', 1225876050, 3),
(985, 'laligatz@gmail.com', 1225923280, 3),
(987, '', 1226229383, 3),
(988, 'marina.zelwer@univ-st-etienne.fr', 1226484305, 3),
(989, 'jjakubowski@octo.com', 1226501731, 3),
(990, 'marc.frerebeau@agama.fr', 1226569186, 3),
(991, 'sbridelance@auchan.com', 1226573462, 3),
(992, 'e.daniel@export-entreprises.com', 1226576176, 3),
(993, 'guillaume.pungeot@mappy.com', 1226910477, 3),
(994, 'mehdi_dhaouadi2002@yahoo.fr', 1227016544, 3),
(995, 'webmaster@apprendre-php.com', 1227039323, 3),
(996, 'ndesaleux+afup@gmail.com', 1227049001, 3),
(998, 'vincent.mary@yahoo.fr', 1227284384, 3),
(999, 'laurentjegouzo@gmail.com', 1227359053, 3),
(1000, 'bruno.chevalier20@gmail.com', 1227360441, 3),
(1001, 'sachbak@hotmail.fr', 1227382322, 3),
(1002, 'samuel.roze@aliceadsl.fr', 1227426690, 3),
(1003, '', 1227449947, 3),
(1004, '', 1227522381, 3),
(1005, 'matsimouna@idris.fr', 1227523049, 3),
(1006, 'alpherz@gmail.com', 1227559413, 3),
(1007, 'inscriptions@afup.org', 1227613125, 3),
(1008, 'contact@webotheque.fr', 1227620343, 3),
(1009, 'vporretti@hotmail.fr', 1227622502, 3),
(1010, 'vporretti@hotmail.fr', 1227622582, 3),
(1011, 'pally.aurelien@free.fr', 1227623591, 3),
(1012, 'thivant@univ-lyon3.fr', 1227625221, 3),
(1013, 'christophe@zend.com', 1227805537, 3),
(1014, 'mraymond@iceb.com', 1227882707, 3),
(1015, 'rosalina007@live.fr', 1227898954, 3),
(1016, 'j.lecomte@arawak.fr', 1227902801, 3),
(1017, 'bruno.rotrou@free.fr', 1228138098, 3),
(1018, 'laure.pillet@gmail.com', 1228210599, 3),
(1019, 'sebastien.helan@gmail.com', 1228224034, 3),
(1020, 'amanigot@gmail.com', 1228301668, 3),
(1021, 'pfz@pfzone.org', 1228326765, 3),
(1022, 'contact@laurent-laville.org', 1228427245, 3),
(1023, 'tissaoui@hotmail.com', 1228513261, 3),
(1024, 'xgorse@elao.com', 1240164748, 4),
(1025, 'contact@tecob.com', 1240469629, 4),
(1026, 'mathieu.laurent@gmail.com', 1240513879, 4),
(1027, 'adrien.carbonne@hop-cube.com', 1240554774, 4),
(1028, 'n.lenepveu@gmail.com', 1240590118, 4),
(1029, 'samuel.roze@aliceadsl.fr', 1240652134, 4),
(1030, 'mennebeuf.a@mipih.fr', 1241009044, 4),
(1031, 'khalilup@gmail.com', 1241019269, 4),
(1032, 'patrice.mayet@greencove.fr', 1242198495, 4),
(1033, 'michael@numinvest.com', 1242484753, 4),
(1034, 'nabil@abweb.ma', 1243688576, 4),
(1035, 'ianbogda@gmail.com', 1243961400, 4),
(1036, '', 1244015969, 4),
(1037, 'riyankajar@yahoo.co.id', 1244213771, 4),
(1038, 'terry.fahy@chrr.osu.edu', 1244219355, 4),
(1039, 'maskas@free.fr', 1245244633, 4),
(1040, 'pierre-alain.mignot@revues.org', 1246272467, 4),
(1041, 'eric.morvan@gmail.com', 1246801178, 4),
(1042, 'h.lepeut@gmail.com', 1247133683, 4),
(1043, 'm.vanhalst@adenova.fr', 1247672635, 4),
(1044, 'audrey.delaet@genopole.fr', 1248100555, 4),
(1045, 'abo@anthony-stephan.com', 1248164216, 4),
(1046, 'contact@pascal-martin.fr', 1248868572, 4),
(1047, '', 1249063421, 4),
(1048, 'philippecazabonne@yahoo.fr', 1249204382, 4),
(1049, 'jerome.macias@gmail.com', 1250005049, 4),
(1050, 'marc.vachette@gmail.com', 1250538788, 4),
(1051, 'clotaire.renaud@laposte.net', 1250848862, 4),
(1052, 'sangele@groupe-exp.com', 1251125360, 4),
(1053, 'marielle.henon@ajilon.fr', 1251179787, 4),
(1054, 'vincent.fleury@tv5monde.org', 1251194179, 4),
(1055, 'royneau@gmail.com', 1251208594, 4),
(1056, 'selvi2@hotmail.com', 1251466375, 4),
(1057, 'afup.org@barresi.ch', 1251487599, 4),
(1058, 'srenard@ruses.com', 1251715316, 4),
(1059, 'webmaster@esraonline.com', 1252306743, 4),
(1060, 'eric.morvan@gmail.com', 1252397612, 4),
(1061, 'francois@fourrier.com', 1252505395, 4),
(1062, 'adrien@oblady.com', 1252510542, 4),
(1063, 'guillaume@internim.com', 1253104734, 4),
(1064, 'stephane.combaudon@gmail.com', 1253109138, 4),
(1065, 'thibaud.a@gmail.com', 1253114339, 4),
(1066, 'cedric@daneel.net', 1253117015, 4),
(1067, 'forumphp2009@yopmail.com', 1253189656, 4),
(1068, 'frederic.minne@uclouvain.be', 1253267858, 4),
(1069, 'shezouani@gmail.com', 1253309235, 4),
(1070, 'mostacchi.serge@orange.fr', 1253444243, 4),
(1071, 'christophe.voirin@europecamions-interactive.com', 1253533116, 4),
(1072, 'bruyere.fred@assess-group.be', 1253542334, 4),
(1073, 'g.beauny@gmail.com', 1253793743, 4),
(1074, 'hcl@descartes.fr', 1253892126, 4),
(1075, 'ivan.enderlin@hoa-project.net', 1253969704, 4),
(1076, 'martin@supiot.net', 1254228763, 4),
(1077, 'd.khnafo@epiconcept.fr', 1254251889, 4),
(1078, 'py.claitte@agoranet.fr', 1254413929, 4),
(1079, 'skander_hammami@yahoo.fr', 1254471522, 4),
(1080, 'francoisgallienne@gmail.com', 1254477902, 4),
(1081, 'zmehanna@gmail.com', 1254681186, 4),
(1082, 'marc.lemercier@utt.fr', 1254727837, 4),
(1083, 'geoffroy.pierret@numericable.fr', 1254742861, 4),
(1084, 'matthieu@bienavous.be', 1254750489, 4),
(1085, 'php@r2rien.net', 1254788688, 4),
(1086, 'x.millies-lacroix@arianespace.fr', 1254820836, 4),
(1087, 'marcelhaudentz@yahoo.fr', 1254886890, 4),
(1088, 'a.wagner@agoranet.fr', 1254904247, 4),
(1089, 'stephen_perin@yahoo.fr', 1254920679, 4),
(1091, 'hi-logik@hotmail.fr', 1255025117, 4),
(1092, 'frederic.salley@gmail.com', 1255345862, 4),
(1093, 'm.maache@ide-environnement.com', 1255525279, 4),
(1094, 'fschmutz@premaccess.com', 1255534713, 4),
(1095, 'tlongis@tf1.fr', 1255617935, 4),
(1096, 'arnaud.ligny@baobaz.com', 1255865096, 4),
(1097, 'hursaint@yahoo.fr', 1255942196, 4),
(1098, 'vco@oxalide.com', 1255946940, 4),
(1099, 'e.daniel@export-entreprises.com', 1255956160, 4),
(1100, 'olivier.bache@grita.fr', 1256016696, 4),
(1101, 'gerault.thomas@gmail.com', 1256028119, 4),
(1102, 'tetardo', 1256046674, 4),
(1103, 'contact@netiva.fr', 1256046682, 4),
(1104, 'sebti19000@gmail.com', 1256058069, 4),
(1105, 'rsolnais@constantin.fr', 1256117918, 4),
(1106, 'sallmaritraore@yahoo.fr', 1256126383, 4),
(1107, 'gfully22@gmail.com', 1256127126, 4),
(1108, 'chiker_k@yahoo.fr', 1256132624, 4),
(1109, 'achmon_enjoy@hotmail.com', 1256215003, 4),
(1110, 'laurent.marchoux@cpam-melun.cnamts.fr', 1256218089, 4),
(1111, 'didier.galland@gmail.com', 1256252889, 4),
(1112, 'sm@leh.fr', 1256313071, 4),
(1113, 'wahibabf@yahoo.fr', 1256413152, 4),
(1114, 'soussoujoel@yahoo.fr', 1256486900, 4),
(1115, 'ajad-it@orange.fr', 1256560431, 4),
(1116, 'pierregerrier@hotmail.com', 1256653579, 4),
(1117, 'abdeslem.menacere@deltalog-dz.com', 1256657113, 4),
(1118, 'stbphoto@free.fr', 1256658427, 4),
(1119, 'hordez.antoine@gmail.com', 1256718857, 4),
(1120, 'guiraudou@osimatic.com', 1256720534, 4),
(1121, 'jean-marc.macias@grita.fr', 1256900116, 4),
(1122, 'guewen.faivre@lyriance.com', 1256901235, 4),
(1123, 'valesre@gmail.com', 1256993197, 4),
(1124, 'nresnikow@gmail.com', 1257154603, 4),
(1125, 'dsamuel@courantmultimedia.fr', 1257257590, 4),
(1126, 'tanthoine@actiane.fr', 1257266904, 4),
(1127, 'mmento@3-com.be', 1257322029, 4),
(1128, 'emelki@infoclip.fr', 1257353683, 4),
(1129, 'dmandouit@appactive.fr', 1257412378, 4),
(1132, 'jerome.desboeufs@gmail.com', 1257451819, 4),
(1133, 'davidmaignan@gmail.com', 1257617175, 4),
(1134, 'cp_daouda@yahoo.fr', 1257873657, 4);

-- --------------------------------------------------------

--
-- Structure de la table `afup_inscription_forum`
--

CREATE TABLE IF NOT EXISTS `afup_inscription_forum` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(11) unsigned NOT NULL DEFAULT '0',
  `reference` varchar(255) NOT NULL DEFAULT '',
  `coupon` varchar(255) NOT NULL DEFAULT '',
  `type_inscription` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `montant` float NOT NULL DEFAULT '0',
  `informations_reglement` varchar(255) DEFAULT NULL,
  `civilite` varchar(4) NOT NULL DEFAULT '',
  `nom` varchar(40) NOT NULL DEFAULT '',
  `prenom` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `telephone` varchar(40) DEFAULT NULL,
  `citer_societe` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `newsletter_afup` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `newsletter_nexen` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `commentaires` text,
  `etat` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `facturation` tinyint(4) NOT NULL DEFAULT '0',
  `id_forum` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_forum` (`id_forum`),
  KEY `reference` (`reference`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Inscriptions au forum PHP' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_logs`
--

CREATE TABLE IF NOT EXISTS `afup_logs` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(11) unsigned NOT NULL DEFAULT '0',
  `id_personne_physique` smallint(5) unsigned NOT NULL DEFAULT '0',
  `texte` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_personne_physique` (`id_personne_physique`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Logs des actions' AUTO_INCREMENT=2 ;

--
-- Contenu de la table `afup_logs`
--

INSERT INTO `afup_logs` (`id`, `date`, `id_personne_physique`, `texte`) VALUES
(1, 1298719994, 1, 'Modification de la personne physique Admin Admin (1)');

-- --------------------------------------------------------

--
-- Structure de la table `afup_niveau_partenariat`
--

CREATE TABLE IF NOT EXISTS `afup_niveau_partenariat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `afup_niveau_partenariat`
--

INSERT INTO `afup_niveau_partenariat` (`id`, `titre`) VALUES
(1, 'Platinum'),
(2, 'Gold'),
(3, 'Silver'),
(4, 'Bronze'),
(5, 'Partenaires');

-- --------------------------------------------------------

--
-- Structure de la table `afup_oeuvres`
--

CREATE TABLE IF NOT EXISTS `afup_oeuvres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_personne_physique` smallint(5) unsigned DEFAULT NULL,
  `categorie` varchar(255) DEFAULT NULL,
  `valeur` smallint(5) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_pays`
--

CREATE TABLE IF NOT EXISTS `afup_pays` (
  `id` char(2) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL DEFAULT '',
  `nom` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Pays';

--
-- Contenu de la table `afup_pays`
--

INSERT INTO `afup_pays` (`id`, `nom`) VALUES
('AD', 'Andorre'),
('AE', 'Émirats Arabes Unis'),
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
('BH', 'Bahreïn'),
('BI', 'Burundi'),
('BJ', 'Bénin'),
('BM', 'Bermudes'),
('BN', 'Brunei'),
('BO', 'Bolivie'),
('BR', 'Brésil'),
('BS', 'Bahamas'),
('BT', 'Bhoutan'),
('BV', 'Îles Bouvet'),
('BW', 'Botswana'),
('BY', 'Biélorussie'),
('BZ', 'Belize'),
('CA', 'Canada'),
('CC', 'Îles Cocos-Keeling'),
('CD', 'République démocratique du Congo'),
('CF', 'République Centrafricaine'),
('CG', 'Congo'),
('CH', 'Suisse'),
('CI', 'Côte D''Ivoire'),
('CK', 'Îles Cook'),
('CL', 'Chili'),
('CM', 'Cameroun'),
('CN', 'Chine'),
('CO', 'Colombie'),
('CR', 'Costa Rica'),
('CU', 'Cuba'),
('CV', 'Cap-Vert'),
('CX', 'Île Christmas'),
('CY', 'Chypre'),
('CZ', 'République tchèque'),
('DE', 'Allemagne'),
('DJ', 'Djibouti'),
('DK', 'Danemark'),
('DM', 'Dominique(la)'),
('DO', 'République Dominicaine'),
('DZ', 'Algérie'),
('EC', 'Équateur (République de l'')'),
('EE', 'Estonie'),
('EG', 'Égypte'),
('ER', 'Érythrée'),
('ES', 'Espagne'),
('ET', 'Éthiopie'),
('FI', 'Finlande'),
('FJ', 'Îles Fidji'),
('FK', 'Îles Malouines'),
('FM', 'Micronésie'),
('FO', 'Îles Féroé'),
('FR', 'France'),
('GA', 'Gabon'),
('GD', 'Grenade'),
('GE', 'Géorgie'),
('GF', 'Guyane française (DOM-TOM)'),
('GH', 'Ghana'),
('GI', 'Gibraltar'),
('GL', 'Groenland'),
('GM', 'Gambie'),
('GN', 'Guinée'),
('GP', 'Guadeloupe (France DOM-TOM)'),
('GQ', 'Guinée Équatoriale'),
('GR', 'Grèce'),
('GS', 'Géorgie du Sud et Sandwich du Sud (ÎIes)'),
('GT', 'Guatemala'),
('GU', 'Guam'),
('GW', 'Guinée-Bissau'),
('GY', 'Guyane'),
('HK', 'Hong Kong (Région administrative spéciale de)'),
('HM', 'Îles Heard et Mc Îles Donald'),
('HN', 'Honduras (le)'),
('HR', 'Croatie(Hrvatska)'),
('HT', 'Haïti'),
('HU', 'Hongrie'),
('ID', 'Indonésie'),
('IE', 'Irlande'),
('IL', 'Israël'),
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
('KN', 'Saint-Christopher et Nevis (Îles)'),
('KP', 'République démocratique populaire de Corée'),
('KR', 'Corée'),
('KW', 'Koweït'),
('KY', 'Îles Caïmans'),
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
('Ma', 'Macao'),
('MC', 'Monaco'),
('MD', 'Moldavie'),
('MG', 'Madagascar'),
('MH', 'Îles Marshall'),
('MK', 'Macédoine'),
('ML', 'Mali'),
('MM', 'Myanmar (Union de)'),
('MN', 'Mongolie'),
('MP', 'Mariannes du Nord(Commonwealth des îles)'),
('MQ', 'Martinique (France DOM-TOM)'),
('MR', 'Mauritanie'),
('MS', 'Montserrat'),
('MT', 'Malte'),
('MU', 'Île Maurice'),
('MV', 'Maldives'),
('MW', 'Malawi'),
('MX', 'Mexique'),
('MY', 'Malaisie'),
('MZ', 'Mozambique'),
('NA', 'Namibie'),
('NC', 'Nouvelle Calédonie'),
('NE', 'Niger'),
('NF', 'Île de Norfolk'),
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
('PG', 'Papouasie Nouvelle-Guinée'),
('PH', 'Philippines'),
('PK', 'Pakistan'),
('PL', 'Pologne'),
('PM', 'Saint-Pierre-et-Miquelon (France DOM-TOM)'),
('PN', 'Pitcairn (Îles)'),
('PR', 'Porto Rico'),
('PT', 'Portugal'),
('PW', 'Palau'),
('PY', 'Paraguay'),
('QA', 'Qatar'),
('RE', 'Réunion (Île de la) - (France DOM-TOM)'),
('RO', 'Roumanie'),
('RU', 'Fédération de Russie'),
('RW', 'Rwanda'),
('SA', 'Arabie Saoudite'),
('SB', 'Îles Salomon'),
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
('ST', 'Sâo Tomé et Prince'),
('SV', 'Salvador'),
('SY', 'République arabe syrienne'),
('SZ', 'Swaziland'),
('TC', 'Îles Turks et Caïcos'),
('TD', 'Tchad'),
('TF', 'Terres Australes françaises (DOM-TOM)'),
('TG', 'Togo'),
('TH', 'Thaïlande'),
('TJ', 'Tajikistan'),
('TK', 'Îles Tokelau'),
('TM', 'Turkménistan'),
('TN', 'Tunisie'),
('TO', 'Tonga'),
('TP', 'Timor oriental'),
('TR', 'Turquie'),
('TT', 'Trinité-et-Tobago'),
('TV', 'Tuvalu (Îles)'),
('TW', 'Taiwan'),
('TZ', 'Tanzanie'),
('UA', 'Ukraine'),
('UG', 'Ouganda'),
('UK', 'Royaume-Uni'),
('UM', 'Dépendances américaines du Pacifique'),
('US', 'États-Unis'),
('UY', 'Uruguay'),
('UZ', 'Ouzbékistän'),
('VA', 'État de la cité du Vatican'),
('VC', 'Saint-Vincent et les Grenadines'),
('VE', 'Venezuela'),
('VG', 'Îles Vierges britanniques'),
('VI', 'Îles Vierges américaines'),
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

CREATE TABLE IF NOT EXISTS `afup_personnes_morales` (
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
  `etat` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_relance` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pays` (`id_pays`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Personnes morales' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_personnes_physiques`
--

CREATE TABLE IF NOT EXISTS `afup_personnes_physiques` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_personne_morale` smallint(5) unsigned DEFAULT NULL,
  `login` varchar(30) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `mot_de_passe` varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `niveau` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `niveau_modules` char(10) COLLATE latin1_general_ci NOT NULL DEFAULT '',
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
  `etat` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_relance` int(11) unsigned DEFAULT NULL,
  `compte_svn` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pays` (`id_pays`),
  KEY `personne_morale` (`id_personne_morale`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Personnes physiques' AUTO_INCREMENT=2 ;

--
-- Contenu de la table `afup_personnes_physiques`
--

INSERT INTO `afup_personnes_physiques` (`id`, `id_personne_morale`, `login`, `mot_de_passe`, `niveau`, `niveau_modules`, `civilite`, `nom`, `prenom`, `email`, `adresse`, `code_postal`, `ville`, `id_pays`, `telephone_fixe`, `telephone_portable`, `etat`, `date_relance`, `compte_svn`) VALUES
(1, 0, 'admin', '21232f297a57a5a743894a0e4a801fc3', 2, '222', '0', 'Admin', 'Admin', 'admin@afup.org', 'Admin', '59000', 'Lille', 'FR', '', '', 1, NULL, '');

-- --------------------------------------------------------

--
-- Structure de la table `afup_planete_billet`
--

CREATE TABLE IF NOT EXISTS `afup_planete_billet` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_planete_flux`
--

CREATE TABLE IF NOT EXISTS `afup_planete_flux` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `feed` varchar(255) DEFAULT NULL,
  `etat` tinyint(4) DEFAULT NULL,
  `id_personne_physique` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_presences_assemblee_generale`
--

CREATE TABLE IF NOT EXISTS `afup_presences_assemblee_generale` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_personne_physique` smallint(5) unsigned DEFAULT NULL,
  `date` int(11) unsigned NOT NULL DEFAULT '0',
  `presence` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_personne_avec_pouvoir` smallint(5) unsigned NOT NULL DEFAULT '0',
  `date_consultation` int(11) unsigned DEFAULT '0',
  `date_modification` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_rendezvous`
--

CREATE TABLE IF NOT EXISTS `afup_rendezvous` (
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_rendezvous_inscrits`
--

CREATE TABLE IF NOT EXISTS `afup_rendezvous_inscrits` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_sessions`
--

CREATE TABLE IF NOT EXISTS `afup_sessions` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `id_forum` smallint(6) NOT NULL DEFAULT '0',
  `date_soumission` date NOT NULL DEFAULT '0000-00-00',
  `titre` varchar(255) NOT NULL DEFAULT '',
  `abstract` text NOT NULL,
  `journee` tinyint(1) NOT NULL DEFAULT '0',
  `genre` tinyint(1) NOT NULL DEFAULT '1',
  `plannifie` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `afup_sessions_note`
--

CREATE TABLE IF NOT EXISTS `afup_sessions_note` (
  `session_id` int(11) NOT NULL DEFAULT '0',
  `note` tinyint(4) NOT NULL DEFAULT '0',
  `salt` char(32) NOT NULL DEFAULT '',
  `date_soumission` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`note`,`session_id`,`salt`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `afup_sessions_vote`
--

CREATE TABLE IF NOT EXISTS `afup_sessions_vote` (
  `id_personne_physique` int(11) NOT NULL DEFAULT '0',
  `id_session` int(11) NOT NULL DEFAULT '0',
  `a_vote` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_session`,`id_personne_physique`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `afup_site_article`
--

CREATE TABLE IF NOT EXISTS `afup_site_article` (
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=442 ;

--
-- Contenu de la table `afup_site_article`
--

INSERT INTO `afup_site_article` (`id`, `id_site_rubrique`, `surtitre`, `titre`, `raccourci`, `descriptif`, `chapeau`, `contenu`, `position`, `date`, `etat`, `id_personne_physique`) VALUES
(1, 4, '', 'Statuts de l''Association Française des Utilisateurs de PHP', 'statuts-de-l-association-francaise-des-utilisateurs-de-php', '<p>Les statuts officiels de l''association.</p>', '', '<h3>Article 1 - Forme</h3>\r\n<p>Il est fond&eacute;, entre les adh&eacute;rents aux pr&eacute;sents statuts, une association r&eacute;gie par la loi du 1er juillet 1901 et le d&eacute;cret du 16 ao&ucirc;t 1901, les pr&eacute;sents statuts et le R&egrave;glement Int&eacute;rieur.</p>\r\n<h3>Article 2 - D&eacute;nomination</h3>\r\n<p>L''Association prend pour d&eacute;nomination : Association Fran&ccedil;aise des Utilisateurs de PHP (AFUP)  Tous les actes et documents &eacute;manant de l''association et destin&eacute;s aux tiers doivent indiquer la d&eacute;nomination sociale pr&eacute;c&eacute;d&eacute;e ou suivie de la mention &laquo; Association r&eacute;gie par la Loi de 1901 &raquo;, ou &laquo; Association Loi 1901 &raquo;.</p>\r\n<h3>Article 3 - Objet</h3>\r\n<p>Cette association a un double objectif : -Assurer la promotion du langage PHP, principalement aupr&egrave;s des professionnels -Aider au d&eacute;veloppement du langage PHP en contribuant &agrave; certains travaux (d&eacute;veloppements, documentation, etc.)  Elle pourra en outre se consacrer &agrave; toute activit&eacute; li&eacute;e au langage PHP et plus largement aux technologies de l''information, notamment en mati&egrave;re de formation et de diffusion de connaissances.</p>\r\n<h3>Article 4 - Dur&eacute;e</h3>\r\n<p>Sa dur&eacute;e est illimit&eacute;e. N&eacute;anmoins elle peut &ecirc;tre dissoute &agrave; tout moment lors d''un vote au cours d''une assembl&eacute;e g&eacute;n&eacute;rale extraordinaire.</p>\r\n<h3>Article 5 - Si&egrave;ge</h3>\r\n<p>Le si&egrave;ge est fix&eacute; &agrave; Paris (75). Le Bureau a le choix de l''immeuble o&ugrave; le si&egrave;ge est &eacute;tabli et peut le transf&eacute;rer dans la m&ecirc;me ville par simple d&eacute;cision.</p>\r\n<h3>Article 6 - Adh&eacute;sion</h3>\r\n<p>Sauf pr&eacute;cision contraire, l''adh&eacute;sion &agrave; l''association est sujette &agrave; cotisation.  L''association se compose de membres actifs  parmis lesquels on distinguera les membres fondateurs et les membres honorifiques.  Sont appel&eacute;s membres fondateurs les personnes ayant fond&eacute; l''association.  Sont appel&eacute;s membres honorifiques les personnes dont le r&ocirc;le historique dans le d&eacute;veloppement et la promotion du langage PHP est notable, d&eacute;sireux d''apporter leur soutien &agrave; l''association. Ils sont invit&eacute;s &agrave; rejoindre l''association sur invitation du Bureau et sont exempt&eacute;s de cotisation.  Sont appel&eacute;s membres actifs tous les membres de l''association, qu''il s''agisse de personnes physiques ou morales, et y compris les membres fondateurs et honorifiques. Une personne morale adh&eacute;rente devra identifier une personne physique la repr&eacute;sentant dans l''Association.  Une personne physique ou morale est consid&eacute;r&eacute;e membre de l''association selon les modalit&eacute;s d&eacute;finies dans le R&egrave;glement Int&eacute;rieur et une fois sa cotisation acquitt&eacute;e. En outre, elle s''engage &agrave; participer solidairement au fonctionnement de l''association et &agrave; sa gestion avec tous les autres membres.  Enfin, ne pourront adh&eacute;rer &agrave; l''association que les personnes ayant d&eacute;clar&eacute; avoir pris connaissance et accepter les pr&eacute;sents statuts ainsi que le r&egrave;glement int&eacute;rieur.</p>\r\n<h3>Article 7 - Cotisation</h3>\r\n<p>Le montant de la cotisation est d&eacute;termin&eacute; dans le R&egrave;glement Int&eacute;rieur. Il est r&eacute;visable annuellement par l''Assembl&eacute;e G&eacute;n&eacute;rale.</p>\r\n<h3>Article 8 - Retrait et exclusion</h3>\r\n<p>Tout membre de l''association peut se retirer &agrave; tout moment &agrave; condition d''avoir rempli ses fonctions statutaires.  Tout membre ne remplissant pas ses obligations vis &agrave; vis de l''association peut &ecirc;tre exclu par d&eacute;cision du Bureau qui statue souverainement, pour faute grave, comportement portant pr&eacute;judice mat&eacute;riel ou moral &agrave; l''association ou de nature &agrave; nuire &agrave; la bonne r&eacute;putation de l''association, infraction aux statuts ou au R&egrave;glement Int&eacute;rieur, ou toute autre raison prononc&eacute;e dans l''int&eacute;r&ecirc;t de l''association.  En cas de proc&eacute;dure d''exclusion, le membre concern&eacute; (ou son repr&eacute;sentant dans le cas d''une personne morale) doit &ecirc;tre entendu en ses explications par le Bureau et, s''il en fait partie, sa voix ne peut &ecirc;tre compt&eacute;e dans le cadre du vote portant sur sa radiation.  Le d&eacute;c&egrave;s ou le d&eacute;p&ocirc;t de bilan entrainera la radiation automatique de la personne physique ou morale concern&eacute;e.  Dans tous les cas, la ou les cotisations d&eacute;j&agrave; pay&eacute;es restent acquises &agrave; l''association.</p>\r\n<h3>Article 9 - Droits des membres</h3>\r\n<p>Chaque membre de l''association b&eacute;n&eacute;ficie des droits et avantages que celle-ci r&eacute;serve &agrave; ses membres et est soumis aux obligations stipul&eacute;es &eacute;ventuellement dans le R&egrave;glement Int&eacute;rieur. Les droits des membres au sein de l''association sont incessibles et intransmissibles. Chaque membre est libre de participer aux Assembl&eacute;es G&eacute;n&eacute;rales.</p>\r\n<h3>Article 10 - Obligations des membres</h3>\r\n<p>Chaque membre s''engage &agrave; respecter les statuts et &agrave; se conformer au R&egrave;glement Int&eacute;rieur. Tout membre dont la situation viendrait &agrave; changer au regard des conditions d''admission s''engage &agrave; en aviser sans d&eacute;lai le Bureau. Enfin, les membres ne sont pas tenus d''assumer les dettes &eacute;ventuelles de l''association, mais s''engagent &agrave; verser leur cotisation.</p>\r\n<h3>Article 11 - Ressources</h3>\r\n<p>Les ressources de l''association comprennent :  -Le montant des cotisations. -Les dons de bienfaiteurs -Les subventions de l''&eacute;tat, des d&eacute;partements, des r&eacute;gions et des communes. -Les subventions d''&eacute;tablissements publics. -Toutes ressources autoris&eacute;es par la Loi.</p>\r\n<h3>Article 12 - Bureau</h3>\r\n<p>L''Assembl&eacute;e G&eacute;n&eacute;rale d&eacute;l&egrave;gue &agrave; un Bureau compos&eacute; d''au moins trois membres &eacute;lus pour une dur&eacute;e d'' un an la responsabilit&eacute; de repr&eacute;senter l''association dans les actes de la vie civile, et de garantir un fonctionnement en parfaite ad&eacute;quation avec les exigences l&eacute;gales et administratives en vigueur, en alertant au besoin les membres de l''Assembl&eacute;e G&eacute;n&eacute;rale en cas de manquement constat&eacute; &agrave; ces exigences.  Le Pr&eacute;sident, ayant pouvoir de repr&eacute;sentation et de signature au nom de l''association, repr&eacute;sente l''association dans tous les actes de la vie civile, administrative, et en justice, s''il y a lieu. Il peut faire toute d&eacute;l&eacute;gation de pouvoirs et de signature totale ou partielle &agrave; un autre membre du Bureau, et pour une question d&eacute;termin&eacute;e et un temps limit&eacute; &agrave; un autre membre du Conseil d''Administration. En cas d''emp&ecirc;chement, le Pr&eacute;sident est remplac&eacute; temporairement par le Tr&eacute;sorier, ou le Secr&eacute;taire qui disposent des m&ecirc;mes pouvoirs.  Le secr&eacute;taire est charg&eacute; en particulier de r&eacute;diger les proc&egrave;s-verbaux des r&eacute;unions du Bureau et de tenir le registre pr&eacute;vu par la Loi. En cas d''emp&ecirc;chement, il est remplac&eacute; par un membre du Bureau ou du Conseil d''Administration d&eacute;sign&eacute; par le Pr&eacute;sident. Le Tr&eacute;sorier est charg&eacute; de tenir ou de faire tenir sous son contr&ocirc;le la comptabilit&eacute; de l''association. Il per&ccedil;oit les recettes. Il effectue tout paiement sous r&eacute;serve des modalit&eacute;s pr&eacute;vues au R&egrave;glement Int&eacute;rieur. Il pr&eacute;sente un arr&ecirc;t&eacute; des comptes annuels en Assembl&eacute;e G&eacute;n&eacute;rale.  En cas d''emp&ecirc;chement, le Tr&eacute;sorier est remplac&eacute; par un autre membre du Bureau ou du Conseil d''Administration d&eacute;sign&eacute; par le Pr&eacute;sident.</p>\r\n<h3>Article 13 - Conseil d''Administration</h3>\r\n<p>Le Conseil d''Administration a pour but d''assurer la p&eacute;r&eacute;nit&eacute; de l''association. Il dispose d''un avis consultatif sur les affaires courantes. Il dispose d''un droit de veto de tout d&eacute;cision du Bureau sous r&eacute;serve de signaler ce v&eacute;to dans les deux semaines et qu''il soit vot&eacute; par les deux tiers plus une voix des membres du Conseil d''Administration.  Les membres du Conseil d''Administration sont &eacute;lus par l''Assembl&eacute;e G&eacute;n&eacute;rale pour une dur&eacute;e renouvelable de 3 ans selon les modalit&eacute;s pr&eacute;vues dans le R&egrave;glement Int&eacute;rieur.  Seul un membre actif de l''association peut faire partie du Conseil d''Admistration. Si &agrave; l''issue du vote de l''Assembl&eacute;e G&eacute;n&eacute;rale le Conseil d''Administration ne comporte pas au moins six membres, la dissolution de l''association sera automatique. Le nombre maximum de membres du Conseil d''administration est de douze.</p>\r\n<h3>Article 14 - Groupes de travail</h3>\r\n<p>Le Bureau peut d&eacute;l&eacute;guer ponctuellement ou pour une dur&eacute;e d&eacute;finie des missions diverses &agrave; certains membres actifs, regroup&eacute;s en groupes de travail. Ces groupes se constituent sur la base du volontariat. Leur fonctionnement est pr&eacute;cis&eacute; dans le R&egrave;glement Int&eacute;rieur. La dissolution d''un groupe de travail peut &ecirc;tre prononc&eacute;e &agrave; tout moment par le Bureau et est automatique d&egrave;s la fin de la mission confi&eacute;e.</p>\r\n<h3>Article 15 - Assembl&eacute;e G&eacute;n&eacute;rale ordinaire</h3>\r\n<p>L''Assembl&eacute;e G&eacute;n&eacute;rale ordinaire comprend tous les membres de l''Association &agrave; jour de leurs cotisations et se r&eacute;unit au moins une fois par an. La date et l''ordre du Jour de l''Assembl&eacute;e sont fix&eacute;s par le Pr&eacute;sident apr&egrave;s consultation du Bureau.  La convocation accompagn&eacute;e de l''ordre du jour est adress&eacute;e aux membres par lettre simple ou par courrier &eacute;lectronique quinze jours au moins avant la date de l''Assembl&eacute;e.  Le Pr&eacute;sident, assist&eacute; des membres du Bureau, pr&eacute;side l''Assembl&eacute;e et expose la situation morale de l''Association.   Le Tr&eacute;sorier rend compte de sa gestion et soumet le bilan &agrave; l''approbation de l''Assembl&eacute;e.   Les membres actifs disposeront d''une semaine &agrave; compter de l''envoi de la convocation pour proposer par &eacute;crit des points &agrave; ajouter &agrave; l''ordre du jour qui seront soumis &agrave; approbation du Bureau.   Seuls les sujets port&eacute;s &agrave; l''ordre du jour peuvent faire l''objet d''un vote.</p>\r\n<h3>Article 16 - Assembl&eacute;e G&eacute;n&eacute;rale extraordinaire</h3>\r\n<p>Sur d&eacute;cision du Bureau, du Conseil d''Administration ou sur demande &eacute;crite de la moiti&eacute; plus un des membres actifs, le Pr&eacute;sident doit convoquer une Assembl&eacute;e G&eacute;n&eacute;rale extraordinaire.   L''Assembl&eacute;e G&eacute;n&eacute;rale extraordinaire ne peut d&eacute;lib&eacute;rer que sur son Ordre du Jour.  La convocation et l''ordre du jour seront adress&eacute;s par lettre simple ou par courrier &eacute;lectronique un mois au moins avant la date pr&eacute;vue de sa r&eacute;union.   L''Assembl&eacute;e G&eacute;n&eacute;rale extraordinaire statue &agrave; la majorit&eacute; des deux tiers des membres pr&eacute;sents repr&eacute;sentant au moins le quorum sur premi&egrave;re convocation.   Sur seconde convocation, aucun quorum n''est exig&eacute; pour la tenue de l''Assembl&eacute;e G&eacute;n&eacute;rale extraordinaire. Elle continue &agrave; statuer &agrave; la majorit&eacute; des deux tiers des membres pr&eacute;sents.   Seuls les sujets port&eacute;s &agrave; l''ordre du jour pourront faire l''objet d''un vote.</p>\r\n<h3>Article 17 - R&egrave;glement Int&eacute;rieur</h3>\r\n<p>L''association se dote d''un R&egrave;glement Int&eacute;rieur destin&eacute; &agrave; pr&eacute;ciser les divers points non pr&eacute;vus par les statuts, notamment ceux ayant trait &agrave; l''administration interne de l''association. En outre il d&eacute;finit les r&egrave;gles en vigueur concernant les prises de d&eacute;cisions inh&eacute;rentes au fonctionnement de l''association, ainsi que toutes modalit&eacute;s additionnelles de d&eacute;signation et de r&eacute;vocation de membres ou groupes de membres &agrave; qui l''association d&eacute;l&egrave;gue certaines responsabilit&eacute;s. Ce R&egrave;glement Int&eacute;rieur peut &eacute;voluer sur proposition d''un groupe de travail sp&eacute;cifique de l''association, apr&egrave;s vote &agrave; la majorit&eacute; absolue de l''Assembl&eacute;e G&eacute;n&eacute;rale.</p>\r\n<h3>Article 18 - Quorum</h3>\r\n<p>Tout vote propos&eacute; &agrave; l''Assembl&eacute;e G&eacute;n&eacute;rale n&eacute;cessite la participation minimale d''au moins un tiers des membres actifs. Dans le cas o&ugrave; une majorit&eacute; ne peut &ecirc;tre d&eacute;gag&eacute;e, le Pr&eacute;sident aura un r&ocirc;le d''arbitrage.</p>\r\n<h3>Article 19 - Dissolution</h3>\r\n<p>La dissolution est prononc&eacute;e par l''Assembl&eacute;e G&eacute;n&eacute;rale qui nomme un liquidateur. L''actif sera d&eacute;volu conform&eacute;ment &agrave; l''article 9 de la loi du 1er juillet 1901 &agrave; une association poursuivant un but identique.       Les pr&eacute;sents statuts ont &eacute;t&eacute; approuv&eacute;s par l''Assembl&eacute;e G&eacute;n&eacute;rale constitutive le 28 d&eacute;cembre 2001.   Fait &agrave; Paris, le 28 d&eacute;cembre 2001.</p>', 6, 1009494000, 1, 0),
(21, 4, '', 'Règlement intérieur', 'reglement-interieur', '<p>Le R&egrave;glement Int&eacute;rieur de l''AFUP</p>', '', '<h3>R&egrave;glement Int&eacute;rieur de l''AFUP</h3>\r\n<h3>Article 1 - Raison d''&ecirc;tre</h3>\r\n<p>Le pr&eacute;sent r&egrave;glemenent int&eacute;rieur vient compl&eacute;ter les Statuts de l''Association Fran&ccedil;aise des Utilisateurs de PHP comme ceux-ci le d&eacute;finissent. Il est rappel&eacute; que, conform&eacute;ment aux Statuts, l''adh&eacute;sion &agrave; l''AFUP est sujette &agrave; approbation pr&eacute;alable du pr&eacute;sent R&egrave;glement Int&eacute;rieur et que son non respect pourra entra&icirc;ner la radiation d''un membre fautif.</p>\r\n<h3>Article 2 - Fonctionnement du Bureau</h3>\r\n<p>Le Bureau est constitu&eacute; du Pr&eacute;sident, du Tr&eacute;sorier et du Secr&eacute;taire de l''AFUP. Ceux-ci peuvent &ecirc;tre remplac&eacute;s en cas d''absence par un suppl&eacute;ant. A d&eacute;faut, comme pr&eacute;cis&eacute; dans les Statuts, le rempla&ccedil;ant sera choisit au sein du Bureau par le Pr&eacute;sident.  Pour tout vote, le Pr&eacute;sident dispose d''une demi voix suppl&eacute;mentaire.  Le Bureau est &eacute;lu &agrave; main lev&eacute;e tous les ans par l''Assembl&eacute;e G&eacute;n&eacute;rale annuelle ordinaire. Un syst&egrave;me de vote &agrave; distance par internet sera disponible pour les personnes ne pouvant se d&eacute;placer.  Cette &eacute;lection est organis&eacute;e sur la base d''un scrutin de liste bloqu&eacute;e sans panachage ni possibilit&eacute; de rayer des noms.   Chaque liste est amen&eacute;e avant le vote &agrave; pr&eacute;senter un programme d''action pour l''ann&eacute;e &agrave; venir illustr&eacute; d''un buget pr&eacute;visionnel pour sa r&eacute;alisation.  La liste qui remporte le plus de voix est &eacute;lue. En cas d''&eacute;galit&eacute;, et comme pr&eacute;cis&eacute; dans l''article Quorum des Statuts, le Pr&eacute;sident sortant du Bureau aura un r&ocirc;le d''arbitrage et tranchera entre les listes se trouvant en position d''&eacute;galit&eacute;.</p>\r\n<h3>Article 3 - Gestion du budget</h3>\r\n<p>Le Tr&eacute;sorier est responsable du budget de l''association. Il peut &agrave; ce titre effectuer tout paiement de moins de 100 (cent) Euros sans autorisation pr&eacute;alable du Bureau.</p>\r\n<h3>Article 4 - Approbation des nouveaux membres</h3>\r\n<p>Toute demande d''adh&eacute;sion &agrave; l''AFUP est soumise &agrave; examen par le Bureau qui peut la rejeter sans justification. L''encaissement de la cotisation par le Tr&eacute;sorier implique l''approbation. Le versement de cette cotisation est un pr&eacute;-requis pour obtenir le statut de membre de l''AFUP.  Il est obligatoire de fournir une adresse &eacute;lectronique valide.</p>\r\n<h3>Article 5 - Membres honorifiques</h3>\r\n<p>Sur d&eacute;cision du Bureau, une personne morale ou physique dont le renom dans le milieu de PHP est &eacute;tablit pourra b&eacute;n&eacute;ficier d''une invitation de 3 ans renouvelables &agrave; faire partie gratuitement de l''AFUP honoris causa. Ces membres sont par ailleurs d&ocirc;t&eacute;s de droits et devoirs identiques aux autres.</p>\r\n<h3>Article 6 - Groupes de travail</h3>\r\n<p>Tout membre souhaitant participer &agrave; un groupe de travail s''engage &agrave; respecter la licence de diffusion et de droits d''auteurs affect&eacute;e au projet auquel il participe. Sauf mention explicite contraire, la license version modifi&eacute;e de la licence BSD s''applique &agrave; tous les travaux des groupes rendus publics par l''AFUP, et en particulier au code source.  Le Bureau d&eacute;cide des orientations &agrave; donner &agrave; l''AFUP sous le contr&ocirc;le du Conseil d''Administration, et d&eacute;finit des missions sp&eacute;cifiques. Il distribue ensuite ces missions par appel &agrave; volontaires. Toute initiative de groupe de travail organis&eacute;e par des membres est encourag&eacute;e par le Bureau sous r&eacute;serve de notification pr&eacute;alable.</p>\r\n<h3>Article 7 - Renouvellement du Conseil d''Administration</h3>\r\n<p>Les membres du Conseil d''Administration sont &eacute;lus &agrave; main lev&eacute;e au cours de l''Assembl&eacute;e G&eacute;n&eacute;rale annuelle ordinaire apr&egrave;s le Bureau.  Les candidats sont &eacute;lus selon leur nombre de voix obtenues. En cas d''&eacute;galit&eacute; et s''il y a plus de candidats que de places disponibles, le nouveau Pr&eacute;sident tranchera souverainement.   Les membres du Conseil d''Administration peuvent en d&eacute;missionner &agrave; tout moment.</p>\r\n<h3>Article 8 - Cotisation</h3>\r\n<p>La cotisation est annuelle. Un rappel sera envoy&eacute; deux semaines avant la fin cette dur&eacute;e, et le jour m&ecirc;me. Le non paiement de la nouvelle cotisation sous un d&eacute;lai de dix jours ouvr&eacute;s entrainera la radiation automatique de l''adh&eacute;rent.   La cotisation est du montant de :</p>\r\n<ul>\r\n<li>Particuliers : 20 (vingt) euro </li>\r\n<li>Personne morale : 50 (cinquante) euro </li>\r\n</ul>\r\n<p>Seul le r&egrave;glement par ch&egrave;que libell&eacute; en euro est accept&eacute;.</p>\r\n<h3>Article 9 - Dons</h3>\r\n<p>Tous les dons autoris&eacute;s par la Loi sont les bienvenus. Le Bureau se r&eacute;serve le droit de refuser un don. Un r&eacute;c&eacute;piss&eacute; sera obligatoirement remis au donateur par le Tr&eacute;sorier.</p>\r\n<h3>Article 10 - Preuves d''existence de l''entreprise</h3>\r\n<p>Toute entreprise souhaitant &ecirc;tre r&eacute;f&eacute;renc&eacute;e comme telle au sein de l''AFUP devra n&eacute;cessairement fournir comme preuve l&eacute;gale son num&eacute;ro de SIREN.</p>\r\n<h3>Article 11 - Adresse</h3>\r\n<p>L''association prend pour adresse celle de son tr&eacute;sorier qui a en charge la r&eacute;ception des paiements de cotisations :  AFUP<br /> 19 rue larrey<br /> 31000 Toulouse</p>', 1, 1232406000, 1, 0),
(22, 6, '', 'Le niveau n''est-il pas trop élevé ?', 'le-niveau-n-est-il-pas-trop-lev', 'Non, le niveau de l''AFUP n''est pas trop élevé. Chacun y trouvera ce qui peut lui être utile. ', '', '<ul>\r\n<li>Pour tout le monde : un flux d''information commentées, des liens vers des sites et ressources en ligne, des liens vers des organismes de formation, des benchmarks.\r\n</li>\r\n</ul>\r\n\r\n<ul>\r\n<li>Pour les développeurs plus avancés : des informations sur les projets d''écriture d''extensions en C de PHP, sur le projet PEAR et comment y participer.\r\n</li>\r\n</ul>\r\n\r\n<ul>\r\n<li>Pour les entreprises : des analyses de cas précis et des conseils pour les guider dans le choix de la technologie la plus adaptée à leur projet, et un annuaire de professionnels du développement PHP.\r\n</li>\r\n</ul>', 0, 1012518000, 1, 0),
(2, 9, '', 'Les atouts de PHP - Utilisation', 'les-atouts-de-php-utilisation', 'Nous allons ici présenter des chiffres relatifs à l''utilisation de PHP en France et dans le Monde.', '', 'PHP est une plateforme de développement dédié aux applications relatives à Internet.\r\n\r\nAu départ simple gestionnaire de script pour faciliter la vie des webmasters, PHP est devenu un language utilisé par et pour tous les types d''entreprises.\r\n\r\n<b>Qui utilise PHP, comment se positionne t il par rapport aux autres technologies dynamiques (JSP, coldfusion, ASP)?</b>\r\n\r\nA ce jour on compte 14 millions de sites utilisant le php.\r\nIl se trouve sur plus de 53% des serveurs Apache(source Netcraft 02/2004), soit sur plus de 30% des serveurs connectés à Internet.\r\n\r\n<CENTER><IMG1|center></CENTER>\r\n\r\n\r\nPHP est maintenant clairement un outil de premier plan, et on ne compte plus les articles et les analyses qui présentent le trio Apache - PHP - MySQL comme la nouvelle formule gagnante du web.\r\nAprès un succès immédiat dans la communauté Linux et Open Source, PHP s''est imposé dans le monde du business et de l''internet professionnel. \r\n\r\nLes technologies employées sur les 10 plus gros sites francais montrent bien la force de PHP.\r\n\r\n<table width="300" border="1" align="center" cellspacing="0" cellpadding="0" bordercolor="#000000">\r\n	<tr> \r\n		<td width="23">&nbsp;</td>\r\n		<td width="159" bgcolor="#666666"><font color="#FFFFFF"><b>Site Web</b></font></td>\r\n		<td width="118" bgcolor="#666666"><b><font color="#FFFFFF">Technologie</font></b></td>\r\n	</tr>\r\n	<tr> \r\n		<td>1</td>\r\n		<td>Wanadoo.fr</td>\r\n		<td bgcolor="#66CC66">PHP</td>\r\n	</tr>\r\n	<tr> \r\n		<td>2</td>\r\n		<td>Lycos</td>\r\n		<td bgcolor="#66CC66">PHP</td>\r\n	</tr>\r\n	<tr> \r\n		<td>3</td>\r\n		<td>Free.fr</td>\r\n		<td bgcolor="#66CC66">PHP</td>\r\n	</tr>\r\n	<tr> \r\n		<td >4</td>\r\n		<td>MSN.fr</td>\r\n		<td >Microsoft/ASP</td>\r\n	</tr>\r\n	<tr> \r\n		<td>5</td>\r\n		<td>Tiscali</td>\r\n		<td bgcolor="#66CC66">PHP</td>\r\n	</tr>\r\n	<tr> \r\n		<td >6</td>\r\n		<td > Yahoo.fr</td>\r\n		<td bgcolor="#66CC66">migre vers PHP</td>\r\n	</tr>\r\n	<tr> \r\n		<td>7</td>\r\n		<td>Microsoft.fr </td>\r\n		<td>Microsoft/ASP</td>\r\n	</tr>\r\n	<tr> \r\n		<td>8</td>\r\n		<td> AOL</td>\r\n		<td>Confidentiel</td>\r\n	</tr>\r\n	<tr> \r\n		<td>9</td>\r\n		<td>Google</td>\r\n		<td>Confidentiel</td>\r\n	</tr>\r\n	<tr> \r\n		<td>10</td>\r\n		<td>Voil&agrave;.fr </td>\r\n		<td bgcolor="#66CC66">PHP</td>\r\n	</tr>\r\n\r\n</table>\r\n<center>classement Jupiter MMXI de Mars 2002 </center>\r\n\r\n[<b>Note :</b> Pour plus d''information sur la methodologie employée <a href="https://afup.org/article.php3?id_article=91">(Lien)</a>]\r\n\r\nLa force de PHP est d''avoir été conçu spécifiquement pour les applications relatives à Internet (rapide, souple et ouvert aux autres technologies).\r\nDes milliers de portails et de sites professionnels utilisent PHP de manière intensive. \r\nDe nombreux consultants analysent les tendances technologiques actuelles et préconisent PHP.\r\nLes grands comptes se mettent à utiliser de plus en plus PHP, parfois même pour leurs applications critiques.\r\n\r\n<b>Témoignage</b>\r\n\r\nGuillaume SIARA travaillant à la Société Générale [2002] :\r\n\r\n" Nous utilisons php pour accéder à nos bases oracle [...] nous devons faire attention à la securité et nos développements sont plus complexes que sur la plupart des sites internet." \r\n\r\n', 0, 1073862000, 1, NULL),
(20, 6, '', 'Pourquoi la priorité aux professionnels ?', 'pourquoi-la-priorit-aux-professionnels', '', 'L''une des principales raisons est que PHP est un outil beaucoup trop puissant pour être limité à une utilisation de type "pages-perso".', '<p>Quel particulier va attaquer une base Oracle/Sybase ou s''intéresser au développement d''une interface PHP vers Lotus Notes ou SAP ?</p>\r\n\r\n<p>Par ailleurs, les sites orientés vers les particuliers ou les développeurs occasionnels et débutants ne manquent pas. L''AFUP n''a pas l''intention de se substituer à leur travail mais de le compléter par ce chaînon manquant.</p>', 0, 1012518000, 1, 0),
(19, 4, '', 'Qu''est-ce que l''AFUP ?', 'qu-est-ce-que-l-afup', 'L''AFUP, Association Française des Utilisateurs de PHP, est une association dont le principal but est de promouvoir le langage PHP auprès des professionnels et de participer à son développement.', '', 'L''AFUP a été créée en réponse à un besoin croissant des entreprises, celui d''avoir un interlocuteur unique pour répondre à leurs questions sur PHP. \r\n\r\nL''AFUP a avant tout une vocation d''information, et fournira les éléments clefs qui permettront de choisir PHP selon les véritables besoins et contraintes d''un projet. \r\n\r\nPar ailleurs, l''AFUP offre un cadre de rencontre et de resources techniques pour les développeurs qui souhaitent faire avancer le langage PHP lui même.\r\n\r\n', 0, 1009407600, 1, NULL),
(231, 22, '', 'Le livre blanc PHP en entreprise', 'le-livre-blanc-php-en-entreprise', 'L''AFUP vous invite à consulter son livre blanc sur PHP. Vous y trouverez toutes les informations nécessaires à l''adoption (ou non) de PHP dans votre entreprise.', '=https://afup.org/docs/livre-blanc-php-en-entreprise-v4.pdf', '', 0, 1076281200, 1, NULL),
(53, 6, '', 'Comment contacter l''AFUP ?', 'comment-contacter-l-afup', '', '', '<p>Vous pouvez contacter le bureau de l''AFUP par e-mail à l''adresse <a mailto="bureau@afup.org">bureau@afup.org</a>, ou par courrier à l''adresse postale suivante :</p>\r\n\r\n<blockquote>\r\nAFUP<br />\r\n119 rue du chemin vert<br />\r\n75011 Paris\r\n</blockquote>', 0, 1012518000, 1, 0),
(54, 6, '', 'Je suis un professionne lié à  PHP, pourquoi devenir membre ?', 'je-suis-un-professionnel-ou-un-institutionnel-li-php-pourquoi-devenir-membre', 'Je suis un professionnel ou un institutionnel lié à  PHP, pourquoi devenir membre ?', '', '<p>Si votre structure utilise, voire même repose en partie sur le langage php, les intérêts à devenir membre sont multiples.</p>\r\n<p>Tout d''abord, pour vous permettre d''échanger avec d''autres acteurs et\r\nutilisateurs provenant d''horizons multiples.</p>\r\n<p>Ensuite, pour apporter dans les objectifs et moyens que se donne l''AFUP la tonalité qui vous est propre.</p>\r\n<p>Et enfin, mais non des moindre, pour participer ou tout du moins soutenir le projet d''une  meilleure visibilité et structuration de la filière PHP, auprès des professionnels et institutionnels français.</p>', 0, 1012518000, 1, 0),
(55, 6, '', 'Je suis un amateur de PHP, pourquoi devenir membre ?', 'je-suis-un-amateur-de-php-pourquoi-devenir-membre', '', '', '<p>Même s''il est clair que la promotion de PHP auprès d''un public professionnel a vraisemblablement peu de chance de réellement vous motiver, le développement du langage lui même par contre peu tout à fait vous intéresser.</p>\r\n\r\n<p>Si vous avez par exemple des compétences en PHP, en C ou des aptitudes à rédiger ou traduire de la documentation, vous pouvez très bien participer aux groupes de travail concernant le développement.</p>\r\n\r\n<p>De plus cela constitue un très bon moyen de cotoyer d''autres personnes partageant également le même engouement pour PHP et donc de progresser via les échanges et la diversité des participants.</p>', 0, 1012518000, 1, 0),
(56, 6, '', 'Comment peut-on devenir membre ?', 'comment-peut-on-devenir-membre', '', '', 'C''est très simple, il existe deux cas de figure :\r\n\r\n<ul>\r\n<li>Pour les personnes Physiques :\r\n</li>\r\n</ul>\r\n\r\nIl vous suffit d''envoyer un courrier à l''AFUP en remplissant le <a href="https://afup.org/docs/bulletin_adhesion_personne_physique.pdf">bulletin d''inscription</a>  indiquant vos noms, prénoms et une adresse mail valide en  joignant le réglement de votre cotisation par chèque bancaire ou postal à l''ordre de l''AFUP. Cette dernière est ensuite valable pour une durée de 12 mois.\r\n\r\nLe montant de la cotisation est actuellement fixé à 20 euro.\r\n\r\n<ul>\r\n<li>Pour les personnes Morales :\r\n</li>\r\n</ul>\r\n\r\n<p>Même chose que précédemment  à ceci près qu''il est également demandé de fournir un numéro SIRET correspondant à votre dénomination sociale en remplissant le <a href="https://afup.org/docs/bulletin_adhesion_personne_morale.pdf">bulletin d''inscription</a> En outre, le montant de la cotisation pour les personnes morales est porté à 50 euro (elle autorise l''inscription de trois employés à la mailing-list AFUP).</p>\r\n\r\n<p>Ensuite, dans tous les cas le Bureau se réserve le droit d''accepter ou non l''inscription, dans la négative il est évident que le règlement de la cotisation ne sera pas pris en compte.</p>\r\n\r\n<p>L''adresse postale de l''AFUP est :\r\n<br />\r\nAFUP<br />\r\n119 rue du chemin vert<br />\r\n75011 Paris</p>\r\n\r\n<p>Important : la souscription d''une cotisation entraîne acception du <a href="https://afup.org/pages/site/?route=vie-associative-afup/21/reglement-interieur">Règlement Intérieur de l''AFUP</a>.</p>\r\n', 0, 1012518000, 1, 0),
(57, 6, '', 'Pourquoi doit-on souscrire une cotisation ?', 'pourquoi-doit-on-souscrire-une-cotisation', '', '', 'L''AFUP est une association, elle est donc amenée à avoir un minimum de frais\rinhérents à son fonctionnement interne.\r\r\n\r\r\nEnsuite, le montant des cotisations est également une source de financement\r(même modeste) permettant de contribuer à la mise en oeuvre de certains objectifs de l''association.', 0, 1012586263, 1, NULL),
(59, 19, '', 'SRM : Les serveurs d''applications en PHP', 'srm-les-serveurs-d-applications-en-php', 'Interview de Derick Rethans, développeur principal du SRM. Le SRM apporte à PHP la persistance des applications (ressources, variables, connexions aux bases de données...) et propulse PHP dans la cours des serveurs d''applications.', '', '</html>\r\n<table>\r\n<tr><td valign="top"><b>Damien Seguy</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nQu&#39;est ce que le SRM?\r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick Rethans\r\n</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top">\r\nSRM est un acronyme pour &#39;Script Running Magic&#39; (script faisant de la magie), ou &#39;Script Running Machine&#39;. Simplement, le SRM rend possible l&#39;utilisation d&#39;instances de classe distantes ; l&#39;appel de fonctions distantes, qui sont d&eacute;j&agrave; compil&eacute;es, et le stockage de donn&eacute;es entre plusieurs pages et plusieurs utilisateurs. Dans ce dernier cas, SRM fournit un syst&egrave;me de variables d&#39;applications. Mais le plus int&eacute;ressant est l&#39;appel d&#39;objets distants aussi facilement que si c&#39;&eacute;tait une instance locale. Ces objets, des bananes, comme nous les appelons, sont &eacute;crits en PHP, et sont conserv&eacute;s en m&eacute;moire entre deux requ&ecirc;tes de page. Toutes les fonctionnalit&eacute;s distantes sont &eacute;crites en PHP.\r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nQu&#39;est ce qui vous a pouss&eacute; a cr&eacute;er le SRM.\r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick\r\n</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top">\r\nUhm.. Et bien…Cela a commenc&eacute; par une longue discussion houleuse, un flame, sur la liste de diffusion PHP-dev. Certains membres de la communaut&eacute; (en particulierles m&eacute;chants allemands) se chamaillaient &agrave; propos des serveurs d&#39;applications. A cette &eacute;poque, personne n&#39;avait de d&eacute;finition bien pr&eacute;cise pour cela, mais une des fonctions les plus importantes &eacute;tait les variables d&#39;application. Alors, James Moore a eu l&#39;id&eacute;e de l&#39;impl&eacute;menter dans PHP lui-m&ecirc;me, mais avec James et Mathieu Kooiman, nous avons d&eacute;cid&eacute; de r&eacute;aliser certaines fonctionnalit&eacute;s, qui n&#39;&eacute;taient pas limit&eacute;es par PHP lui-m&ecirc;me. \r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nA quand remonte le d&eacute;but de ce projet ?\r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick\r\n</b></td><td valign="top">&nbsp;&nbsp;</td><td valign="top">\r\nEn Novembre / D&eacute;cembre 2000. Nous (en particulier moi) avons commenc&eacute; &agrave; programmer, sous la forme d&#39;un projet de fin de scolarit&eacute;. Nous voulions utiliser le SRM pour conserver des &eacute;tats d&#39;authentification, et mettre en cache les r&eacute;sultats de requ&ecirc;tes.\r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nUn an apr&egrave;s, est ce que le SRM ressemble au projet initial ? Est il mieux ? Qu&#39;est ce qui a &eacute;t&eacute; abandonn&eacute; ?\r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick\r\n</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top">\r\nSRM est tr&egrave;s diff&eacute;rent maintenant, et bien sur, il est mieux. Une des fonctionnalit&eacute;s qui reste est les variables persistantes. Mais c&#39;est probablement la seule, &agrave; mon avis. Nous avons abandonn&eacute; l&#39;approche &#39;module&#39; du SRM, et nous avons ajout&eacute; un syst&egrave;me de cache de r&eacute;sultat. Nous ne souhaitions pas reprogrammer le SRM pour chaque type de fonctionnalit&eacute; que PHP propose. Durant nos rencontres de d&eacute;veloppement &agrave; Arnhem, nous avons d&eacute;cid&eacute; d&#39;utiliser PHP/Zend comme un module. Jani Taskinen d&eacute;montra la possibilit&eacute; de ce syst&egrave;me, et Mathieu r&eacute;&eacute;crit l&#39;extension PHP pour qu&#39;elle communique avec le SRM avec un langage Orient&eacute; Objet. J&#39;ai alors &eacute;tudi&eacute; pas mal de programmes, et j&#39;ai rendu possible l&#39;ex&eacute;cution de fonctions distantes (&eacute;crites en PHP, et charg&eacute;e dans le SRM sous forme de script compil&eacute;), et le support des bananes.\r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nSi je comprends bien, il y a des scripts PHP d&#39;un cot&eacute;, et un d&eacute;mon SRM de l&#39;autre. C&#39;est &ccedil;a ? \r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick\r\n</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top">\r\nOui. Le d&eacute;mon ex&eacute;cute les fonctions distantes, et conserve les objets. Les scripts PHP et les fonctions distantes sont &eacute;crites en PHP. \r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nQuels avantages y a t il a se d&eacute;pendre d&#39;un d&eacute;mon externe pour ex&eacute;cuter des scripts PHP ?\r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick\r\n</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top">\r\nCe n&#39;est pas &#39;d&eacute;pendre&#39; mais plut&ocirc;t coop&eacute;rer. En PHP, vous ne pouvez pas faire survivre de variable apr&egrave;s la fin d&#39;un script. Sans parler des ressources comme des connexions LDAP ou un pointeur de fichier. Un autre avantage du SRM est que de multiples utilisateurs peuvent exploiter le m&ecirc;me objet, et communiquer entre eux facilement. De plus, le d&eacute;mon peut ex&eacute;cuter des scripts de lui m&ecirc;me, comme par exemple, rafra&icirc;chir des donn&eacute;es toutes les 5 minutes. C&#39;est excellent pour monter un syst&egrave;me de cache, en coop&eacute;ration avec les ADT de Sterling (Abstract Data Types).\r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nAujourd&#39;hui, qui peut profiter du SRM ?\r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick\r\n</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top">\r\nCeux qui seront le plus int&eacute;ress&eacute;s seront ceux qui ont besoin d&#39;un syst&egrave;me de stockage persistant ; ceux qui ont besoin d&#39;automatisation de leur site (rafra&icirc;chissement automatique des donn&eacute;es) et ceux qui on besoin d&#39;une &#39;application&#39;. Je vais expliquer cela avec l&#39;aide de &#39;Galactic Tales&#39;. \r\n<P>\r\nGalactic Tales est un jeu en ligne allemand, qui ressemble &agrave; civilization. Ici, ils ont besoin d&#39;&#39;application&#39; : les plan&egrave;tes et les stations spatiales g&egrave;re des ressources qui leur sont propres, comme la recherche. C&#39;est tr&egrave;s difficile &agrave; faire avec des scripts PHP, car il n&#39;y a alors pas de concept de &#39;temps&#39;. Avec SRM, Galactic Tales disposaient de plan&egrave;tes automatiques, qui avaient une vie de leur cot&eacute;, sans avoir r&eacute;ellement besoin de sollicitations de la part des utilisateurs. Seulement besoin d&#39;informations de la part d&#39;autres objets du jeu.\r\n<P>\r\nDerni&egrave;rement, j&#39;ai eu une discussion avec Ulf Wendel et Hartmut Holzgraefe &agrave; propos des caches des pages dynamiques. Un des plus grands probl&egrave;mes est de savoir quand reconstruire la page. Laissons le SRM s&#39;en occuper : Si quelque chose du cot&eacute; de l&#39;administration change, placez un bool&eacute;en dans le SRM qui indique que la page a &eacute;t&eacute; mise &agrave; jour, ou bien que la requ&ecirc;te a &eacute;t&eacute; modifi&eacute;e. D&egrave;s que l&#39;application r&eacute;sidente du SRM d&eacute;tecte ce changement (v&eacute;rifications r&eacute;guli&egrave;res), elle peut v&eacute;rifier quelles sont les pages modifi&eacute;es et les reconstruire. Les relations entre ces pages sont conserv&eacute;es en m&eacute;moire, dans le SRM, sous la forme d&#39;un graphe support&eacute; par ADT.\r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nEn r&eacute;sum&eacute;, le SRM renvoie les scripts PHP &agrave; la g&eacute;n&eacute;ration pure de pages HTML. Ils g&egrave;rent les pages web &eacute;ph&eacute;m&egrave;res et assure la connexion avec l&#39;internaute. Le SRM assure la survie de l&#39;application, qui vit ind&eacute;pendamment.\r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nQuelles sont les applications actuelles qui pourraient profiter du SRM ? PHPnuke, IMP, phorum, sont des exemples d&#39;applications OpenSource majeures. Pourraient-elles &ecirc;tre r&eacute;&eacute;crites avec le SRM et am&eacute;lior&eacute;e ?\r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick\r\n</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top">\r\nPrenons IMP. Comme vous le savez, IMP utilise IMAP pour ses fonctions MAIL. IMAP n&#39;a pas de concept de liens persistants, et chaque page ouvre &agrave; nouveau une connexion au serveur. Il est possible de r&eacute;&eacute;crire IMP sous forme de banane, pour qu&#39;il s&#39;ex&eacute;cute automatiquement, c&#39;est &agrave; dire qu&#39;il lise automatiquement le courrier lorsque n&eacute;cessaire, recalcule les threads de messages, etc… Le script PHP (par opposition au SRM), ne s&#39;occupe plus que de mise en page. L&#39;authentification peut se faire sans un r&eacute;el besoin de cookies ou d&#39;autre chose, et les donn&eacute;es d&#39;identification ne doivent pas &ecirc;tre stock&eacute;es dans une session, car le SRM peut le g&eacute;rer (il faudra toute fois un identifiant pour relier un utilisateur &agrave; ses donn&eacute;es). \r\nPHPnuke devrait &ecirc;tre banni de la terre, mais par exemple Phorum pourrait stocker des donn&eacute;es dans une structure de donn&eacute;es interne (un arbre de chez ADT, par exemple). Le script PHP n&#39;aura plus jamais &egrave; recalculer les threads… Il n&#39;est pas possible de tout &eacute;crire dans les bananes du SRM, mais vous pouvez s&eacute;parer l&#39;application de son affichage plut&ocirc;t facilement. Un autre point avec phorum est que tous les messages sont partag&eacute;s en m&eacute;moire par les utilisateurs, et presque aucune requ&ecirc;te externe n&#39;est n&eacute;cessaire, en tous cas pas &agrave; chaque page, car le SRM garde tout en m&eacute;moire.\r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nQuel est le niveau de performance du SRM ?\r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick\r\n</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top">\r\nNous ne l&#39;avons pas encore test&eacute;, mais vous pouvez imaginer que garder des informations comme des forums hi&eacute;rarchis&eacute;s en m&eacute;moire acc&eacute;l&egrave;re votre application, car aucune requ&ecirc;te n&#39;est n&eacute;cessaire et qu&#39;aucune calcul n&#39;est demand&eacute; pour la mise en thread. Cela acc&eacute;l&egrave;re notablement les performances. Utiliser des objets distants sur la m&ecirc;me machine que le SRM (il communique via les sockets UNIX, sous Unix) est quasiment aussi rapide que d&#39;utiliser un objet local. Une des raisons de ces performances est que le script dans le SRM est d&eacute;j&agrave; analys&eacute;, et que l&#39;objet existe d&eacute;j&agrave;.\r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nQuels syst&egrave;mes supporteront le SRM ?\r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick\r\n</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top">\r\nPour le moment, il fonctionne sur Linux, Solaris et OpenBSD (le dernier n&#39;a pas &eacute;t&eacute; test&eacute; derni&egrave;rement). Il y a aussi du monde qui souhaite le porter sur Windows. Le portage vers les autres syst&egrave;mes Unix ne sera pas difficile. Dan Kalowsky travaille sur le portage MacOSX.\r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nSous quelle licence sera plac&eacute; le SRM ?\r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick\r\n</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top">\r\nLa version Beta verra probablement le d&eacute;mon sous licence MPL, et les SAPI SRM et l&#39;extension SRM seront sous licence PHP. Toutes les &eacute;volutions futures du d&eacute;mon seront s&ucirc;rement sous licence Apache. \r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nEst ce que le SRM fera partie de la distribution PHP ?\r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick\r\n</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top">\r\nC&#39;est difficile &agrave; dire. L&#39;interface SAPI et l&#39;extension seront ajout&eacute;es au CVS de PHP, mais le d&eacute;mon ne sera pas int&eacute;gr&eacute; dans PHP. Il y a toujours la possibilit&eacute; que nous le fassions un jour ou l&#39;autre.\r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nQuel sera le support disponible pour le SRM ?\r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick\r\n</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top">\r\nDu support sera fourni pour un usage commercial, sur une base commerciale, tout comme MySQL. Mais nous esp&eacute;rons qu&#39;une communaut&eacute; se formera, tout comme pour PHP. Le support des utilisateurs non-commerciaux (ce qui ne sera probablement pas beaucoup en nombre) se fera avec l&#39;esprit de l&#39;Open Source : si nous le voulons, nous le ferons. J&#39;aime ce style.\r\n</td></tr>\r\n<tr><td colspan=3><hr width=50% align=center></td></tr>\r\n<tr><td valign="top"><b>\r\nDamien</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top"><b><i>\r\nEnfin, quand sera t il publi&eacute; officiellement ?\r\n</i></b></td></tr>\r\n<tr><td valign="top"><b>\r\nDerick\r\n</b></td><td valign="top">&nbsp;:&nbsp;</td><td valign="top">\r\nUn projet comme celui ci n&#39;est jamais fini, tout comme PHP, ou le noyau Linux. La version Beta est pr&eacute;vue pour bient&ocirc;t, mais je ne peux pas pr&eacute;dire les dates de publications, car je n&#39;en sais rien moi-m&ecirc;me.\r\n</td></tr>\r\n<tr><td colspan=3 align=right><a href=http://www.vl-srm.net target=_blank><b>http://www.vl-srm.net</b></A></td></tr>\r\n<tr><td colspan=3 align=right><a href=mailto:damien.seguy@nexen.net><b>Damien Seguy</b></A></td></tr>\r\n</table>\r\n</html>', 0, 1013382000, 1, NULL),
(71, 22, '', 'Mentions légales', 'mentions-l-gales', 'L''AFUP s''engage à être très vigilante sur la fiabilité de l''information mise à la disposition des internautes qui consultent ce site. Elle ne saurait en revanche être tenue pour responsable d''erreurs, d''omissions ou des résultats qui pourraient être obtenus par un usage inapproprié de ces informations.', '', 'L''AFUP s''engage à être très vigilante sur la fiabilité de l''information mise à la disposition des internautes qui consultent ce site. Elle ne saurait en revanche être tenue pour responsable d''erreurs, d''omissions ou des résultats qui pourraient être obtenus par un usage inapproprié de ces informations.\r\n\r\nPour permettre aux visiteurs de compléter leurs recherches, l''AFUP peut être amenée à leur proposer, à travers un lien hypertexte, de consulter un site spécialisé qui lui paraît digne d''intérêt dans un contexte précis, sans pour autant pouvoir garantir le contrôle des informations délivrées sur le site en question.\r\n\r\n<h3>Droits d''auteurs</h3>\r\n \r\nLe Code de la Propriété Intellectuelle et, plus généralement, les traités et accords internationaux comportant des dispositions relatives à la protection des droits d''auteurs, interdisent, quel que soit le procédé utilisé, intégralement ou partiellement, la représentation ou la reproduction de nos pages, pour un usage autre que privé ou la modification sans l''autorisation expresse de l''auteur ou de ses ayants cause.\r\n\r\nIl est important de rappeler que la loi N° 98-536 du 1er juillet 1998 relative aux bases de données n''accorde aucune exception de copie privée.\r\n\r\n<h3>Confidentialité et respect des données relatives à la vie privée</h3>\r\n \r\nConformément à l''article 34 de la loi Informatique et Libertés N° 78-17 du 6 janvier 1978, l''AFUP vous rappelle que vous disposez à tout moment d''un droit d''accès de rectification et de suppression des données nominatives vous concernant.\r\n\r\nPour exercer ce Droit, il suffit de <a href="http://art53">vous adresser à l''AFUP</a>. \r\n', 0, 1020333705, 1, NULL),
(70, 4, '', 'Formulaires d''inscription', 'formulaires-d-inscription', 'Vous trouverez ici le formulaire d''inscription à l''AFUP', '', 'Vous trouverez ici le formulaire à imprimer et envoyer avec votre cotisation pour devenir membre de l''AFUP, que vous soyez une personne physique ou une entreprise.\r\n\r\n<a href="https://afup.org/docs/bulletin_adhesion_personne_physique.pdf"><h3>Formulaire pour les personnes physiques</h3></a>\r\n\r\n\r\n<a href="https://afup.org/docs/bulletin_adhesion_personne_morale.pdf"><h3>Formulaire pour les entreprises</h3></a>\r\n\r\n\r\nLes informations demandées ci-dessus sont indispensables pour enregistrer l''adhésion de votre organisation.\r\n\r\nElles sont exclusivement destinées à l''AFUP.\r\n\r\nVous disposez d''un droit d''accès, de modification, de rectification et de suppression des données qui vous concernent. Pour exercer ce droit écrivez à : \r\n\r\nAFUP<br />\r\n119 rue du chemin vert<br />\r\n75011 Paris\r\n', 0, 1018782698, 1, NULL);
INSERT INTO `afup_site_article` (`id`, `id_site_rubrique`, `surtitre`, `titre`, `raccourci`, `descriptif`, `chapeau`, `contenu`, `position`, `date`, `etat`, `id_personne_physique`) VALUES
(76, 27, '', 'Création d''un système de question réponse pour promouvoir PHP', 'cr-ation-d-un-syst-me-de-question-r-ponse-pour-promouvoir-php', '', '', 'L''objectif est de créer une liste de question réponses que l''on retrouve souvent lorsque l''on essaye de placer PHP dans le cadre d''un projet.\r\nOn adapte le discours aux différents profils.', 0, 1018428400, 1, NULL),
(134, 19, '', 'Yahoo! passe à PHP', 'yahoo-passe-php', '<p>On savait deja que Yahoo! finances utilisait PHP, MySQL et ioncube accelerator (feu PHP Accelerator ). Depuis l''interview de BjornSchotte, on savait aussi que Rasmus Lerdorf &eacute;tait embauch&eacute; par Yahoo!.<br /> Depuis PHP Con, on est sur que Yahoo! passe &agrave; PHP.</p>', '', '<p>A la PHP Con 2002 qui s''est d&eacute;roul&eacute;e les 24 et 25 octobre en Californie, Michael J. Radwin, ing&eacute;nieur chez Yahoo, a fait une annonce choc.  En effet, Yahoo a d&eacute;cid&eacute; de migrer progressivement sous PHP.  Je vous conseille de parcourir attentivement les ''slides'' de la conf&eacute;rence afin de connaitre les diff&eacute;rentes raisons qui ont pouss&eacute; Yahoo &agrave; ce choix.   On y apprend, entre autres, que :</p>\r\n<p>&nbsp;</p>\r\n<ul>\r\n<li>le syst&egrave;me actuel est dispatch&eacute; sur 4500 serveurs, </li>\r\n<li>le syst&egrave;me actuel contient 8.1 millions de lignes de C/C++ et 3 millions de lignes de Perl, </li>\r\n<li>certaines bases Oracle ont &eacute;t&eacute; remplac&eacute;es par MySQL, </li>\r\n<li>pourquoi ASP, ColdFusion, Perl, PerlMason, JSP, J2EE, XSLT ont &eacute;t&eacute; &eacute;cart&eacute;s au profit de PHP, </li>\r\n<li>qu''ils utilisent un acc&eacute;l&eacute;rateur "ionCube PHP Accelerator", </li>\r\n<li>que SMARTY semble avoir &eacute;t&eacute; envisag&eacute;, </li>\r\n<li>etc.</li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p>Le choix des technologies PHP par le site le plus consult&eacute; au monde est un nouveau pas important dans la reconnaissance des qualit&eacute;s de ce langage par le monde professionnel.</p>\r\n<p>Retrouvez ici <a href="http://public.yahoo.com/~radwin/talks/yahoo-phpcon2002.htm" target="_blank">le contenu de l''intervention</a></p>', 0, 1035932400, 1, 0),
(90, 27, '', 'Entretiens', 'entretiens', '', '', 'L''objectif des entretiens du PHP est de permettre de recolter des temoignages d''utilisation du PHP dans un cadre professionel.\r\nLes cibles sont, a priori, soit des personnalités du PHP soit des entreprises connues utilisant PHP.\r\n\r\n\r\n\r\nListe des questions types à poser pour réaliser un entretien du PHP:\r\n-----------\r\n+ Bonjour, est ce que vous pourriez dans un premier temps nous présenter votre profil ainsi que celui de votre société ( nom, prénom,..., nbe employés, chiffre d''affaire,...) \r\n\r\n+ Quelles est plus en détail l''activité de votre département ? \r\n\r\n+ Quelles sont les caractèristiques de votre plateforme technique ? \r\n\r\n+ Apparement la grande majorité de votre plate-forme tourne grace au logiciel Open Source. Pourquoi ? ( choix technique ou financier ? ) \r\n\r\n+ Quel est le premier projet sur lequel vous avez mis en oeuvre du PHP ?\r\n\r\n+ Pouvez-vous lister rapidement les différents projets / applications dans lesquels vous utilisez PHP aujourd''hui ?\r\n\r\n+ Quelle est la volumétrie de ces projets ? (nbr connexions, users simultanés, pages vues, etc.)\r\n\r\n+ Pourquoi avoir retenu ce serveur d''application ? \r\n\r\n+ Avec quoi utilisez vous PHP ? (Oracle, XML, Postgres, Mysql, ...) \r\n\r\n+ Un recent sondage sur hotscripts.com dénote que PHP est le langage préféré des informaticiens (56,9%avec 15500 voies), avez vous ce sentiment chez vous ? \r\n\r\n+ Quel est le ratio de votre equipe technique qui est suceptible de développer en PHP ? pouvez vous le comparer aux autres langages que vous utilisez Perl, C ... ? \r\n\r\n\r\n+ Quelles sont les principales briques logicielles que vous utilisez ? (Phorum, visiteur, ganesha, ...) ? \r\n\r\n\r\n------------', 0, 1018431883, 1, NULL),
(91, 9, '', 'Les 6 sites Web en PHP les plus fréquentés en France : enquête', 'les-6-sites-web-en-php-les-plus-fr-quent-s-en-france-enqu-te', 'Comment savoir si les 6 sites Web les plus fréquentés utilisent le PHP ?', '', '[Article connexe : Les atouts de PHP - Utilisation <a href="https://afup.org/article.php3?id_article=2">(Lien)</a>]\r\n\r\nPour savoir si un site Web utilise le PHP, il y a deux approches :\r\n\r\n<ul>\n<li>Demander au serveur web (via telnet ou un service Web comme Netcraft) ses en-têtes HTTP, les lire et voir s''il y a présence d''une version du PHP (inconvénients : certains sites ne souhaitent pas divulger leur configuration, pour des soucis de confidentialité, le fait d''avoir le module Php ne signifie pas qu''on l''utilise)\r</li>\n</ul>\n\r\n<ul>\n<li>Regarder les extensions des pages du site pour retrouver celles habituelles du php : .phtml .php3 et .php pour les plus courantes, mais aussi des .html?(avec passage de variables)\r</li>\n</ul>\n\r\nLe rapprochement des deux permet en général de définir si le site audité utilise du Php.\r\n\r\nNous avons donc analysé les 10 sites les plus fréquentés en France - classement Jupiter MMXI de février 2002 (1) - et aussi un site qui va problablement devenir très fréquenté dans peu de temps ... c''est la surprise de la fin de cet article !\r\n\r\n\r\n<h3>Méthodologie</h3>\r\n\r\n<ul>\n<li>Les en-têtes HTTP on été interrogées via Netcraft.com (2) le 10/04/2002\r</li>\n<li>Les extensions des pages ont été visualisées sur les sites le 10/04/2002\r</li>\n<li>Les sites avec * utilisent le PHP d''après l''article et le communiqué de l''AFUP.\r</li>\n</ul>\n\r\n<h3>Résultats</h3>\r\n\r\n<ul>\n<li><strong>WANADOO.FR*</strong>\r</li>\n</ul>\n\r\n(remarque : Wanadoo.com est sous Microsoft-IIS/4.0 on NT4/Windows 98)\r\n\r\nRequête sur <strong>www.wanadoo.fr</strong> :\r\n\r\nApache/1.3.14 (Unix) PHP/3.0.17 mod_fastcgi/2.2.9-SNAP-Sep19-13.50 on Solaris.\r\n \r\n<strong>Analyse sur site :</strong>\r\n\r\nDes .phtml dans la rubriques "abonnez-vous"\r\n\r\n\r\n<ul>\n<li><strong>FREE.FR*</strong>\r</li>\n</ul>\n\r\nRequêtes sur :\r\n\r\n<strong>www.free.fr</strong> :\r\n\r\nApache/1.3.20 (Unix) Debian/GNU on Linux\r\n\r\n<strong>pageperso.free.fr</strong> :\r\n\r\nApache/1.3.20 (Unix) Debian/GNU mod_perl/1.25 on Linux.\r\n\r\n<strong>imp.free.fr</strong> :\r\n\r\nApache/1.3.23 (Unix) Debian GNU/Linux PHP/4.1.2 on Linux\r\n\r\n<strong>Analyse sur site :</strong>\r\n\r\nIMP est une application open source en php pour interroger des comptes pop, Free l''utilise pour offrir un webmail à ses abonnés.\r\n\r\nLes pages personnelles de Free sont fournies avec le service php (version 3 et 4)\r\n\r\n\r\n<ul>\n<li><strong>TISCALI.FR*</strong>\r</li>\n</ul>\n\r\n(rem : libertysurf.fr est devenu maintenant la partie\r\nFAI de Tiscali)\r\n\r\nRequêtes sur :\r\n\r\n<strong>www.tiscali.fr</strong> :\r\n\r\nMicrosoft-IIS/4.0 on unknown\r\n\r\n<strong>www.libertysurf.fr</strong> :\r\n\r\nMicrosoft-IIS/4.0 on NT4/Windows 98\r\n\r\n<strong>register.libertysurf.fr</strong>\r\n\r\nApache/1.3.12 (Unix) mod_perl/1.24 PHP/3.0.16 on Linux\r\n\r\n<strong>Analyse sur site :</strong>\r\n\r\nDans la rubrique ACCES INTERNET\r\n\r\nhttp://register.tiscali.fr/forfaits_ls/\r\n\r\nExtension .php3 visible\r\n\r\n\r\n<ul>\n<li><strong>MULTIMANIA.FR*</strong>\r</li>\n</ul>\n\r\n(Remarques : Multimania est maintenant la partie Pages Perso de Lycos.fr)\r\n\r\nRequête sur <strong>www.multimania.fr</strong> :\r\n\r\nApache/1.3.12 (Unix) PHP/3.0.15 on FreeBSD\r\n\r\n<strong>Analyse sur site :</strong>\r\n\r\nExtension .phtml visible sur l''inscription\r\n\r\nhttp://www.multimania.lycos.fr/common/login/login.phtml\r\n\r\n\r\n<ul>\n<li><strong>MSN.FR</strong>\r</li>\n</ul>\n\r\nRequête sur <strong>www.msn.fr</strong> :\r\n\r\nMicrosoft-IIS/5.0 on Windows 2000\r\n\r\n\r\n<ul>\n<li><strong>YAHOO.FR</strong>\r</li>\n</ul>\n\r\nRequête sur <strong>www.yahoo.fr</strong> :\r\n\r\nunknown on FreeBSD.\r\n\r\n\r\n<ul>\n<li><strong>AOL.FR</strong>\r</li>\n</ul>\n\r\nRequête sur <strong>www.aol.fr</strong> :\r\n\r\nMicrosoft-IIS/4.0 on NT4/Windows 98*\r\n\r\n\r\n<ul>\n<li><strong>YAHOO.COM</strong>\r</li>\n</ul>\n\r\nRequête sur <strong>www.yahoo.com</strong> :\r\n\r\nunknown on FreeBSD\r\n\r\n\r\n<ul>\n<li><strong>VOILA.FR*</strong>\r</li>\n</ul>\n\r\nRequêtes sur :\r\n\r\n<strong>www.voila.fr</strong>\r\n\r\nApache/1.3.20 (Unix) on Linux\r\n\r\n<strong>guide.voila.fr</strong>\r\n\r\nApache/1.3.12 (Unix) PHP/3.0.15 mod_perl/1.21 on Linux.   \r\n\r\n<strong>Analyse sur site :</strong>\r\n\r\nPas d''utilisation d''extension habituelle du Php mais des r? ou des voila?\r\n\r\n\r\n<ul>\n<li><strong>LYCOS.FR*</strong>\r</li>\n</ul>\n\r\nRequêtes sur :\r\n\r\n<strong>www.lycos.fr</strong>\r\n\r\nApache/1.3.23 (Unix) mod_gzip/1.3.19.1a on Linux.\r\n\r\n<strong>www.hotbot.lycos.fr</strong>\r\n\r\nApache/1.3.23 (Unix) mod_gzip/1.3.19.1a PHP/4.1.2 on Compaq Tru64\r\n\r\n<strong>www.multimania.lycos.fr</strong>\r\n\r\nApache/1.3.23 (Unix) PHP/4.0.6 on Linux.\r\n\r\n<strong>Analyse sur site :</strong>\r\n\r\nLes parties Pages perso (ex multimania) et moteur de recherche HotBot utilisent des extensions .phtml ou .html?\r\n\r\n<h3>Conclusion</h3>\r\n\r\nSur les 10 sites Web les plus fréquentés en France, 6 sites utilisent le PHP (le module Php est installé ET utilisé).\r\n\r\nCeci ne fait que confirmer la présence de plus en plus forte du PHP dans les sites Web au niveau mondial (3)\r\n\r\n\r\n<h3>PhpStory</h3>\r\n\r\nAprès les poids lourds du Web français, passons au site qui fait parler de lui en cette période de l''année avec pour caractéristique des taux de fréquentation record concentrés sur une courte période ... et oui c''est LOFTSTORY.FR !\r\n\r\nRequête sur <strong>www.loftstory.fr</strong>\r\n\r\nApache/1.3.20 (Unix) PHP/4.0.6 on Linux\r\n\r\n<strong>Analyse du site :</strong>\r\n\r\nVersion finale pas encore visible au moment de l''audit mais on peut déjà télécharger des sonneries et logos du Loft avec une page en ... PHP !\r\n\r\n\r\n\r\n\r\nMarc VINCENT\r\nPour l''afup.org\r\n\r\n\r\n\r\n\r\n\r\n(1) http://fr.jupitermmxi.com/xp/fr/data/thetop.xml\r\n\r\n(2) http://uptime.netcraft.com/up/graph/\r\n\r\n(3) Etude Netcraft de mars 2002 : PHP est utilisé par 8,8 millions de sites Web et 1,1 millions d''adresses IP\r\n\r\nhttp://www.netcraft.com/Survey/\r\n', 0, 1018901297, 1, NULL),
(102, 12, '', 'PHP or not PHP ? Savoir auditer un site web', 'php-or-not-php-savoir-auditer-un-site-web', '', '', 'Le PHP est de plus en plus utilisé par les sites Web, mais comment prouver quantitativement cette percée ?\r\n\r\nRéponse : il suffit d''auditer un certain nombre de sites Web (par catégorie, par classement, etc.), de définir si le PHP est présent ET utilisé et de comptabiliser le tout.\r\n\r\nL''article sur les <a href="https://afup.org/article.php3?id_article=91">"Les 6 sites Web en PHP les plus fréquentés en France : enquête"</a> a été élaboré avec cette méthodologie.\r\n\r\nPour savoir si un site Web utilise le PHP, il y a deux approches : \r\n\r\n<ul>\n<li>Demander au serveur web (via telnet ou un service Web comme Netcraft) ses en-têtes HTTP (ou headers), les lire et voir s''il y a présence d''une version du PHP (inconvénients : certains sites ne souhaitent pas divulguer leur configuration pour des soucis de confidentialité et le fait d''avoir le module Php ne signifie pas qu''on l''utilise) \r</li>\n</ul>\n\r\n<ul>\n<li>Regarder les extensions des pages du site pour retrouver celles habituelles du php : .phtml .php3 et .php pour les plus courantes, mais aussi des .html? avec passage de variables.\r</li>\n</ul>\n\r\nLe rapprochement des deux permet en général de définir si le site audité utilise du Php.\r\n\r\nVoyons maintenant plus précisément le déroulement de cette méthodologie lors d''un audit.\r\n\r\n<h3>Méthodologie</h3>\r\n\r\n<strong>0/ Noter la date de l''audit</strong>\r\n\r\nTrès important : le monde Web évolue vite, il est donc important de bien indiquer la date de l''audit.\r\n\r\n<strong>1/ Interroger les en-têtes du serveur Web</strong>\r\n\r\nPartant du nom de domaine par exemple : www.tiscali.fr\r\n\r\nOn recherche via un service Web (ou un telnet) ces headers :\r\n\r\n<a href="http://uptime.netcraft.com/up/graph/?mode_u=off&mode_w=on&site=www.tiscali.fr">Exemple avec : www.netcraft.com</a>\r\n\r\n[Exemple avec : http://network-tools.com/->\r\nhttp://network-tools.com/default.asp?prog=httphead&Netnic=whois.arin.net&host=www.tiscali.fr]\r\n\r\nDans les 2 cas, on obtient :\r\nMicrosoft-IIS/4.0 on unknown. Donc a priori, pas de présence de PHP dans les en-têtes. Nous passons à la deuxième étape : analyse sur site\r\n\r\n<strong>2/ Analyse sur le site</strong>\r\n\r\nQu''importe le résultat des headers du serveurs Web, il faut surfer sur le site Web pour vérifier la présence ou la non présence du PHP en action.\r\n\r\nNous auditons donc www.tiscali.fr en surfant sur le site. A priori, que des pages avec de l''ASP (extension .asp visible), sauf tout d''un coup ... un sous-domaine avec des .php3 : register.tiscali.fr\r\n\r\nLa confirmation est donnée par une requête sur les headers avec Netcraft : le sous-domaine register.tiscali.fr est en fait sur une machine avec Apache/1.3.12 (Unix) mod_perl/1.24 PHP/3.0.16 on Linux.\r\n\r\nIl y a donc du PHP sur ce site.\r\n\r\n<strong>3/ Faire un compte-rendu daté</strong> \r\n\r\nUne fois l''audit effectué, vous faites des copier-coller de tous les résultats (avec les urls des pages/rubriques concernées) et vous mettez vos commentaires.\r\n\r\n\r\n<h3>Les conseils</h3>\r\n\r\n<strong>Les en-têtes du serveur Web ne mentionnent pas de php : que faire ?</strong>\r\n\r\nAuditer le site en surfant à la recherche d''indice du PHP ! Voici quelques conseils :\r\n\r\n<ul>\n<li><em>Avez-vous essayé le .com ET le .fr ?</em>\r</li>\n</ul>\n\r\nLes résultats sont parfois différents, exemple :\r\n\r\nwww.wanadoo.com (Microsoft-IIS/4.0 on NT4/Windows 98)\r\n\r\nwww.wanadoo.fr (Apache/1.3.14 (Unix) PHP/3.0.17 mod_fastcgi/2.2.9-SNAP-Sep19-13.50 on Solaris.)\r\n\r\n<ul>\n<li><em>Avez-vous vérifié sur le site, les extensions ?</em>\r</li>\n</ul>\n\r\nExemple : <a href="http://uptime.netcraft.com/up/graph/?mode_u=on&mode_w=on&site=www.boursorama.com&submit=Examine">l''interrogation de boursorama.com donne : Apache/1.3.14 on Linux</a>\r\n\r\nAlors que le site présente des .phtml partout et est connu pour son utilisation du PHP.\r\n\r\n\r\n<ul>\n<li><em>Avez-vous vérifié les sous-domaines visibles du site Web, les différentes rubriques, les sites Web associés ?</em>\r</li>\n</ul>\n\r\nPour les <strong>sous-domaines</strong>, nous avons vu l''exemple avec register.tiscali.fr (PHP) et tiscali.fr (pas de PHP)\r\n\r\nAllez voir <strong>les rubriques</strong> susceptibles d''être des pages dynamiques : forum, contact, webmail, "envoyer cette page à un ami", "Votre compte", "Inscrivez-vous", etc.\r\n\r\nParfois le site Web principal n''utilise pas le PHP mais ses <strong>sites Web associés</strong> oui.\r\n\r\nExemple avec www.ratp.fr (pas de PHP visible) et un de ses sites web associés : www.citefutee.com (qui utilise fortement le PHP).\r\n\r\nDans le cas d''une entreprise, il est bon d''aller voir les sites Web des principales filiales.\r\n\r\n\r\n<strong>Les en-têtes du serveur Web indiquent une version du php : c''est bon ?</strong>\r\n\r\n<em>Pas toujours, car la présence du module PHP ne signifie pas obligatoirement une utilisation !</em>\r\n\r\nExemple : www.univ-paris12.fr utilise Apache/1.3.22 (Unix) PHP/4.1.2 on Solaris, mais une analyse sur le site montre qu''il n''y a que des .html visibles.\r\n\r\nIl faut donc toujours auditer en surfant sur le site.\r\n\r\n<h3>Les listes de sites Web</h3>\r\n\r\nVous avez maintenant la méthode, il vous suffit donc de prendre une liste de sites Web, de faire l''audit et d''envoyer à l''AFUP votre rapport pour qu''on le diffuse !\r\n\r\nExemples de listes :\r\n\r\n<ul>\n<li><a href="http://www.boursorama.com/tableaux/cours_az.phtml?MARCHE=CAC40">Les entreprises du CAC40</a>\r</li>\n</ul>\n\r\n<ul>\n<li><a href="http://www.lexpansion.com/pages/default.asp?pid=7800&Action=H">Les 1000 entreprises françaises de l''Expansion avec possibilité de classement sectoriel, taille (CA et personnel)</a>\r</li>\n</ul>\n\r\n<ul>\n<li><a href="http://www.mediametrie.com/web/index.html">Les sites web audités par Mediamétrie</a>\r</li>\n</ul>\n\r\nMarc VINCENT\r\nPour l''AFUP', 0, 1033468996, 1, NULL),
(109, 46, '', 'PHP fonctionne-t-il avec les serveurs web habituels ? ', 'php-fonctionne-t-il-avec-les-serveurs-web-habituels', '', '', 'Oui, PHP fonctionne avec Microsoft IIS, Apache, Netscape Enterprise Server et beaucoup d''autres serveurs web. La quasi totalité, en fait. \r\n', 0, 1020332188, 1, NULL),
(110, 46, '', 'PHP fonctionne-t-il sur les systèmes d''exploitation présents en\nentreprise ? ', 'php-fonctionne-t-il-sur-les-syst-mes-d-exploitation-pr-sents-en-entreprise', '', '', 'Oui, PHP fonctionne sur Microsoft Windows (toutes versions supérieurs à windows 95), toutes versions d''Unix/linux. \r\n\r\nD''autres OS comme Macintosh X sont également des plateformes PHP. \r\n', 0, 1020332248, 1, NULL),
(111, 46, '', 'PHP fonctionne-t-il avec les SGBD du marché ? ', 'php-fonctionne-t-il-avec-les-sgbd-du-march', '', '', 'Oui, PHP s''interface nativement avec Oracle, Sybase, MS SQLServer PostgreSQL, MySQL (ainsi que Ingres, Informix...) et plus généralement toute base accessible en ODBC (donc Access par exemple) si le support natif n''est pas disponible. \r\n', 0, 1020332288, 1, NULL),
(112, 46, '', 'Quelles sont les librairies disponibles ?', 'quelles-sont-les-librairies-disponibles', '', '', 'Gestion de PDF, de graphismes, de sessions applicatives, de cyberpaiment, Flash, XML, messagerie (POP, IMAP, envoi de mail) et bien d''autres (accès aux objets Java et COM...) \r\n', 0, 1020332342, 1, NULL),
(113, 46, '', 'Quels sont les éditeurs et environnements de développement intégrés disponibles ?', 'quels-sont-les-diteurs-et-environnements-de-d-veloppement-int-gr-s-disponibles', '', '', 'On peut citer le <a href="http://www.zend.com">Zend Studio</a> ou <a href="http://www.nusphere.com">Nusphere</a> ainsi que moult <a href="http://faqfclphp.free.fr/editeurs">éditeurs</a> .\r\n\r\nIl est à noter que ces environnements sont simples d''utilisation et ne nécessitent pas de coûteuses formations à l''utilisation de l''outil.\r\n', 0, 1020332382, 1, NULL),
(114, 46, '', 'Quel est le coût de la license PHP ?', 'quel-est-le-co-t-de-la-license-php', '', '', 'La license PHP est gratuite.\r\n\r\n<strong>Même pour un usage commercial ?</strong>\r\n\r\nQuel qu''en soit l''usage.\r\n', 0, 1020332423, 1, NULL),
(115, 46, '', 'Quels sont les coûts cachés ?', 'quels-sont-les-co-ts-cach-s', '', '', 'Aucun de plus que le développement avec une autre technologie. Prenons l''exemple d''un site web : il faudra toujours faire une charte graphique et  l''architecture de la base de données, PHP faisant l''interfaçage.\r\nEnsuite, il faudra que votre hébergeur ou vos services installent la machine, tester le bon fonctionnement, etc...\r\n', 0, 1020332466, 1, NULL),
(116, 46, '', 'PHP est-il long à apprendre ?', 'php-est-il-long-apprendre', '', '', 'PHP est un langage syntaxiquement simple qui ne nécessite aucune gestion mémoire manuelle. En ce sens,  on peut rapidement apprendre suffisement pour développer efficacement. \r\n\r\nTout développeur ayant fait du C, du PERL  ou du shell unix sera immédiatement à l''aise avec PHP.\r\n\r\nRemarquons néanmoins que tout langage de programmation s''apprend aussi avec de l''expérience, PHP n''échappe pas à cette règle.\r\n\r\n', 0, 1020332485, 1, NULL),
(169, 47, '', 'PHP et le format PDF - Olivier PLATHEY', 'php-et-le-format-pdf-olivier-plathey', 'Olivier PLATHEY, auteur de la FPDF, détaille les différentes solutions de génération de PDF à la volée.', '', 'Une référence dans la comparaison des différentes méthodes : forces et faiblessses de toutes les solutions disponibles.\r\n\r\nCette conférence est bien évidemment disponible... en format PDF !\r\n\r\n<a href="http:// forumphp2002/fpdf.pdf">La présentation </a>\r\n', 0, 1042066800, 1, NULL),
(126, 19, '', 'Utilisation de PHP par Wanadoo - Voila', 'utilisation-de-php-par-wanadoo-voila', '<p>Interview de Christophe Ruelle, Responsable du d&eacute;veloppement chez Wanadoo et cr&eacute;ateur du moteur Voila.</p>', '', '<p><strong>Christophe Ruelle, vous &ecirc;tes responsable du d&eacute;veloppement Wanadoo Portail, pouvez-vous nous pr&eacute;senter votre parcours professionnel ?</strong></p>\r\n<p>Formation  d''ing&eacute;nieur  en  informatique  &agrave; l''ESSI puis j''ai &eacute;volu&eacute; vers une formation d''autodidacte.</p>\r\n<p>Fin 96 Echo  SARL  est  cr&eacute;e : Moteur  de recherche, mesure d''audience, services aux internautes en tous genres.  Quelques mois plus tard les pages jaunes nous demandent d''utiliser le moteur, et 6 mois plus tard arrive le portail Voila.</p>\r\n<p>J''occupe  alors  un  double  r&ocirc;le,  d''une  part d''encadrement  technique et d''autre par de d&eacute;veloppement logiciel.</p>\r\n<p>Les ann&eacute;es suivantes consacr&eacute;es  au  d&eacute;veloppement  de  Voila et certaines briques de Wanadoo  avec une &eacute;quipe qui a atteint 80 personnes courant 2000.</p>\r\n<p>Depuis janvier 2002 Echo SA est fusionn&eacute;e &agrave; Wanadoo portails, la filiale  de  Wanadoo  SA qui g&egrave;re les sites portails du groupe, et j''y occupe  le poste de responsable du d&eacute;veloppement avec une &eacute;quipe de 30 personnes.</p>\r\n<p><strong>Vous &ecirc;tes responsable du d&eacute;veloppement de Wanadoo Portails. Quelles sont les activit&eacute;s de cette soci&eacute;t&eacute; ?</strong></p>\r\n<p>Mon r&ocirc;le actuel est &laquo; responsable  du  d&eacute;veloppement &raquo;.  Cela consiste a r&eacute;fl&eacute;chir sur et a mettre en place des infrastructures techniques.</p>\r\n<p>Wanadoo portails comme son nom   l''indique  g&egrave;re  et  d&eacute;veloppe  des  sites  portails  et  couvre l''ensemble  des  m&eacute;tiers  qui  y sont associ&eacute;s : les aspects business, marketing, production, et techniques.</p>\r\n<p><strong>Votre plate-forme technique se trouve &agrave; Sophia Antipolis. Quelles sont ces caract&eacute;ristiques ?</strong></p>\r\n<p>Une  des  principales  plate-formes  techniques se situe &agrave; Sophia.  Cette plate-forme h&eacute;berge pr&egrave;s de 350 serveurs principalement sous  Linux  et quelques autres OS.</p>\r\n<p>Cette  plate-forme technique  est  connect&eacute;e  par  fibre  optique &agrave; plus de 150 Mb/s, des&nbsp;liens gigabit sont en cours d''installation.</p>\r\n<p><strong>La grande majorit&eacute; de votre plate-forme tourne gr&acirc;ce au logiciel Open Source. Est-ce un choix technique ou financier ?</strong></p>\r\n<p>De  nombreux  serveurs  utilisent  des  logiciels open-source,  comme apache ou php ou encore des modules et utilitaires (forums,  ...).</p>\r\n<p>&nbsp;</p>\r\n<p>Ce  choix  n''est pas qu''&eacute;conomique, mais participe au potentiel  de cr&eacute;ativit&eacute; et de compr&eacute;hension des cha&icirc;nes de production du Web.</p>\r\n<p>C''est surtout une vraie s&eacute;curit&eacute; pour ne pas &ecirc;tre pieds &amp;  mains li&eacute;s si un bug venait &agrave; &ecirc;tre d&eacute;couvert, comme ce peut &ecirc;tre le cas  dans  le  cadre  de  logiciels  commerciaux.</p>\r\n<p>Pourtant il ne faut pas croire  que nous passions notre temps &agrave; bidouiller chaque module open-source  que  nous  utilisons.</p>\r\n<p>En  g&eacute;n&eacute;ral  nous attendons que le logiciel  soit  vraiment  mature pour l''utiliser. Cela demande un gros travail  de  test.</p>\r\n<p><strong>Quelles sont les projets significatifs et d''envergure o&ugrave; vous avez mis en oeuvre PHP ?</strong></p>\r\n<p>PHP  est tr&egrave;s r&eacute;pandu dans les portails. Avec Perl c''est le langage le plus  utilis&eacute;  d&egrave;s qu''il s''agit de d&eacute;passer les possibilit&eacute;s du DHTML. Mais  nous  avan&ccedil;ons  toujours  avec une g&eacute;n&eacute;ration de retard et c''est seulement  depuis  la  maturit&eacute; de PHP 4.x que nous avons entrepris de gros  chantiers.</p>\r\n<p>A  ce jour de tr&egrave;s nombreuses sections (les espaces th&eacute;matiques, les petites  annonces , le  carnet d''adresse,&hellip;) sont r&eacute;alis&eacute;s en PHP.</p>\r\n<p>Nous sommes  en train de r&eacute;&eacute;crire de nombreuses autres applications mais il n''est pas encore possible de dire lesquels verront le jour en premier.</p>\r\n<p>Un  tr&egrave;s  gros  projet  de personnalisation du portail est en passe de voir le jour, 100% en PHP.</p>\r\n<p><strong>Qu''apporte de plus une technologie comme PHP dans votre architecture ?</strong></p>\r\n<p>La grande force de php est d''&ecirc;tre compl&egrave;tement int&eacute;gr&eacute; &agrave; l''environnement apache / mysql / html. C''est un ciment  qui  peut  &ecirc;tre  aussi  bien  utilis&eacute; par un junior que par un codeur exp&eacute;riment&eacute;.</p>\r\n<p><strong>PHP  nous  permet  d''envisager de mettre des "choses" en relation : Des utilisateurs  avec  des  services, des services avec des contenus, des services avec d''autres services, etc...</strong></p>\r\n<p>Et ceci en  restant  dans un environnement 100% Web</p>\r\n<p>La  maturit&eacute;  du  langage  nous  permet  aussi  de  capitaliser sur sa constante &eacute;volution. Sa syntaxe proche de C est rapide &agrave; ma&icirc;triser, et surtout,  sa  documentation  (en  ligne) est extr&ecirc;mement bien faite et totalement tourn&eacute;e vers des cas d''utilisation concrets.</p>\r\n<p><strong>Avec quoi utilisez vous PHP ?</strong></p>\r\n<p>Apache,  MySQL,  ftp, imap, GD, XML, ...</p>\r\n<p>PHP est magique, mais il faut garder &agrave; l''esprit les r&egrave;gles de base de la programmation, et ne pas sombrer dans la facilit&eacute;.</p>\r\n<p><strong>Comment justifieriez-vous l''utilisation de cette technologie ?}}</strong></p>\r\n<p>Cela  d&eacute;pend  de  l''objectif.<strong> </strong>Pour des besoins 100% Web et pour   lesquels   on   d&eacute;sire   un   compromis  entre  performance  et possibilit&eacute;s, PHP est de loin le meilleur choix que je connaisse.</p>\r\n<p>Mais dans   d''autres   cas,   notamment programmation   syst&egrave;me,  scripts d''administration  ou  programmes cgi &agrave; haute performance, des langages comme Perl ou C sont plus adapt&eacute;s.</p>\r\n<p><strong>Comment percevez vous l''utilisation de PHP chez Wanadoo Filiale de France T&eacute;l&eacute;com ? Existe t''il des r&eacute;ticences a son utilisation ?</strong></p>\r\n<p>L''appr&eacute;ciation des non-techniques est : on me parle de PHP, je vois  que  le  projet  avance vite, co&ucirc;te pas tr&egrave;s cher et marche bien ensuite.</p>\r\n<p>Entretien : Emmanuel FAIVRE</p>', 0, 1022796000, 1, 0),
(129, 19, '<p>Description de la solution technique utilis&eacute;e par loftstory pour resister &agrave; la charge.</p>', 'Alain Fortune chez M6web: le cas LoftStory', 'alain-fortune-m6web-loftstory', '', '', '<p><strong>Pouvez vous nous d&eacute;crire l''architecture technique du site <a href="http://loftstory.m6.fr/site/index.htm">Loft Story</a> ?</strong></p>\r\n<p>Nous recourons de fa&ccedil;on massive depuis l''ann&eacute;e derni&egrave;re au CDN Akamai tant pour la diffusion vid&eacute;o que pour le caching des sites. Le site &eacute;tant largement statique hormis les pages PHP + acc&egrave;s MySQL, ce sont les&nbsp;frontaux Akamai qui supporte la majorit&eacute; de la charge. La&nbsp;plateforme d''h&eacute;bergement centralis&eacute;e est, elle, relativement "light" : 4 frontaux Apache - PHP + serveurs MySQL redond&eacute;s.</p>\r\n<p>Conjointement au site grand public, nous avons d&eacute;velopp&eacute; une offre FanClub multi-contenus, multi-services rassemblant diff&eacute;rents partenaires (Cryo&nbsp;Networks, NetFrance, Akamai, Easyclick). Cette offre s''appuie sur des d&eacute;veloppements "maison" sur base ATG Dynamo fournissant single sign-on et gestion de sessions.</p>\r\n<p><strong>A quel volum&eacute;trie cette architecture doit-elle faire face ?</strong></p>\r\n<p>Nous sommes partis des donn&eacute;es de l''&eacute;dition 2001 de LoftStory : les piques de charge majeures constat&eacute;s sur les prime-time culminaient &agrave; plus de 300000 adresses IP distinctes en 2 minutes. Les acc&egrave;s &agrave; la base de donn&eacute;es&nbsp;tournent en pointe &agrave; 40 requetes par secondes.</p>\r\n<p><strong>Quels sont les pics d''audience de l''&eacute;dition 2002 ?</strong></p>\r\n<p>Nous nous attendons &agrave; &ecirc;tre un peu en dessous de ces chiffres sur l''&eacute;dition 2002. N&eacute;anmoins, le soir du premier prime-time, nous avons servi une bande&nbsp;passante HTTP totale (sites Loftstory.fr + m6.fr) de plus de 170Mbps et autant en vid&eacute;o. Depuis nous tournons a un r&eacute;gime de croisi&egrave;re de l''ordre de 30/40 Mbps (hors vid&eacute;o).</p>\r\n<p><strong>PHP est-il utilis&eacute; sur tout le site ?</strong></p>\r\n<p>Le FanClub s''appuie sur la plateforme propre M6 : frontaux Linux-Apache + PHP, serveurs d''application ATG Dynamo, Oracle 8i. PHP g&egrave;re la pr&eacute;sentation&nbsp;tandis que les composants m&eacute;tiers sont ex&eacute;cut&eacute;s par un serveur d''application J2EE, ATG Dynamo. L''ensemble fonctionne parfaitement bien ensemble.</p>\r\n<p><strong>Pourquoi recourir &agrave; la plate-forme LAMP (Linux, Apache MySQL, PHP) ?</strong></p>\r\n<p>La r&eacute;ponse tient en quelques mots : robustesse, gratuit&eacute;,&nbsp;universalit&eacute;, ma&icirc;trise. Je ne suis toutefois pas un inconditionnel de MySQL qui me semble devoir encore progresser notamment en terme d''outils d''administration&nbsp;et de tenue de charge transactionnelle.</p>', 0, 1024351200, 1, 0),
(130, 19, '', 'Libération.fr: "PHP s''est imposé de lui même"', 'j-r-me-texier-lib-ration', '<p>Quelques explications sur le choix technologique effectu&eacute; pour le site liberation.fr</p>', '', '<p><br /> <strong>Vous utilisez PHP pour le site <a href="http://www.liberation.fr">Liberation.fr</a>, pouvez-vous nous expliquer les raisons de ce choix ?</strong></p>\r\n<p>Nous ne souhaitions pas utiliser certaines technologies trop propri&eacute;taires et notre &eacute;quipe avait d&eacute;j&agrave; eu une premi&egrave;re exp&eacute;rience concluante autour de PHP. Comme nous ne disposions pas d''un budget pharaonique, PHP s''est impos&eacute; de lui m&ecirc;me.</p>\r\n<p><strong>Comment utilisez-vous PHP sur le site Liberation.fr ?</strong></p>\r\n<p>PHP pr&eacute;sente le contenu (articles, br&egrave;ves, etc.) de nos bases de donn&eacute;es. Nous avons b&acirc;ti une architecture modulaire qui nous permet d''assembler des composants selon nos besoins. Du c&ocirc;t&eacute; de l''architecture mat&eacute;rielle, trois frontaux Apache-PHP servent les pages et la base MySQL est h&eacute;berg&eacute;e sur un quatri&egrave;me serveur.</p>\r\n<p><strong>PHP est-il assez performant pour supporter le trafic d''un des tous premiers sites d''information fran&ccedil;ais ?</strong></p>\r\n<p>Oui. La volum&eacute;trie de notre site peut se r&eacute;sumer &agrave; deux indicateurs cl&eacute;s (pour le mois d''avril 2002) : un pic &agrave; 4 000 visiteurs simultan&eacute;s et 24M pages vues par mois. Les frontaux supportent cette volum&eacute;trie sans probl&egrave;me car nous utilisons une r&eacute;partition de charge mat&eacute;rielle entre les trois frontaux.</p>\r\n<p><strong>Quels sont selon vous les principaux avantages de PHP ?</strong></p>\r\n<p>PHP est facile &agrave; utiliser et sa syntaxe est tr&egrave;s agr&eacute;able. Ce langage permet de construire rapidement de v&eacute;ritables applications. En plus, il est gratuit. <strong>J&eacute;r&ocirc;me Texier - Lib&eacute;ration</strong></p>', 0, 1024351200, 1, 0),
(132, 19, '', 'Questions au créateur de PHP, Rasmus Lerdorf', 'questions-au-cr-ateur-de-php-rasmus', '<p>Questions pos&eacute;es &agrave; Rasmus Lerdorf et Zeev Suraski lors de la conf&eacute;rence PHP 2001 &agrave; la D&eacute;fense</p>', '', '<p><strong>Pourquoi avoir cr&eacute;&eacute; PHP, et quelles etaient ses fonctionnalit&eacute;s au d&eacute;but ?</strong></p>\r\n<p><strong>Rasmus   :</strong> Je voulais simplement r&eacute;soudre un probl&egrave;me : pouvoir ex&eacute;cuter des scripts simples et rapides. Ce qui existait ne me satisfaisant pas, j''ai cr&eacute;&eacute; le PHP. Au niveau fonctionnalit&eacute;s, au d&eacute;but c''etait tres limit&eacute;, puis quelqu''un m''a demand&eacute; de rajouter des conditions, alors je l''ai fait, puis apr&egrave;s on m''a demand&eacute; des boucles etc.. a s''est fait comme &ccedil;a.</p>\r\n<p><strong>Pourquoi avoir choisi l''Open Source ?</strong></p>\r\n<p>Ca s''est pas vraiment fait expr&egrave;s, des amis trouvaient cela int&eacute;ressant, je leur ai donc donne le code, et eux-m&ecirc;me l''ont donne a leur amis Ca s''est fait naturellement !etc</p>\r\n<p><strong>Pour le passage &agrave; PHP3, pourquoi avoir l&eacute;gu&eacute; le leadership &agrave; Zend ? </strong></p>\r\n<p>Pour PHP3, je voulais faire un analyseur qui marche mieux, j''ai donc appris lex et yacc. Mais j''avais quand m&ecirc;me du mal. C''est &agrave; cette m&ecirc;me &eacute;poque que j''ai &eacute;t&eacute; contacte par Zeev et Andy Gutmans, et ils m''ont beaucoup aid&eacute;. PHP est de toute fa&ccedil;ons d&eacute;velopp&eacute; par de nombreuses personnes !</p>\r\n<h3>PHP aujourd''hui</h3>\r\n<p><strong>Quels sont les chiffres de l''utilisation de PHP actuellement ? </strong></p>\r\n<p><strong>Zeev  :</strong> Netcraft donne 25% de sites.</p>\r\n<p><strong>Quels types de sites utilisent PHP ?. </strong></p>\r\n<p><strong>Zeev  :</strong> Tous vraiment. Ca va de la simple page personnelle aux plus importants sites Internet.</p>\r\n<p><strong>Quel est le fonctionnement de la communaut&eacute; PHP ? </strong></p>\r\n<p><strong>Rasmus &amp; Thies  : </strong> Elle n''est pas vraiment organis&eacute;e. Il y a les parties qui s''occupent plus du d&eacute;veloppement du coeur de PHP, celles de la documentation et d''autres des extensions... Il y a des contributions de partout a tous les niveaux ! Cela dit aujourd''hui il y a quand m&ecirc;me moins de travail.</p>\r\n<p><strong>Quel est le rapport de la communaut&eacute; vis a vis des entreprises et &eacute;diteurs ? </strong></p>\r\n<p><strong>Zeev  :</strong> On n''a pas vraiment de rapport avec les grands &eacute;diteurs. Nous n''avons aucune aide de Microsoft par ex., quand nous essayons de d&eacute;velopper des extensions pour qu''elle marche sous Windows. Il y a vraiment plus de r&eacute;activit&eacute; dans la communaut&eacute; Open Source.</p>\r\n<p><strong>Quelle est l''architecture du Zend Engine ? </strong></p>\r\n<p><strong>Zeev  :</strong> Il est compos&eacute; de 3 parties : le "script engine", les extensions et une interface simple d''acc&egrave;s.</p>\r\n<p><strong>Quel comparaison pourrait-on faire avec .NET ? </strong></p>\r\n<p><strong>Zeev  :</strong> PHP existe bien lui pour le moment :) Ses principaux atouts sont ses performances, sa rapidit&eacute; et la facilit&eacute; de sa prise en main et de son d&eacute;veloppement. Il a de plus un aspect de briques applicatives int&eacute;ressantes, c''est &agrave; dire qu''on peut facilement d&eacute;velopper des applications par dessus. Enfin si on rajoute des solutions de cache, on obtient des performances vraiment importantes.</p>\r\n<p><strong>Est ce que PHP va &ecirc;tre inclus dans .NET ? </strong></p>\r\n<p><strong>Rasmus   :</strong> C''est absolument pas pr&eacute;vu et de toutes fa&ccedil;ons je ne vois pas l''int&eacute;r&ecirc;t. PHP n''a pas besoin de ca pour &ecirc;tre utilisable sous Windows.</p>\r\n<h3>PHP demain</h3>\r\n<p><strong>Parlez nous un peu de PEAR. </strong></p>\r\n<p><strong>Rasmus   :</strong> PEAR est un de nos grands projet autour de PHP. Il sert en fait &agrave; ce que chacun puisse d&eacute;poser du code PHP pour le mettre &agrave; la disposition de la communaut&eacute;, car tout ne peut pas &ecirc;tre inclus dans PHP lui-m&ecirc;me ou ses extensions. De plus &agrave; terme nous envisageons de mettre des outils pour permettre son utilisation simple, un peu dans le style d''"apt-get" sous Debian, une sorte de "pear-get" donc.</p>\r\n<p><strong>Qu''est ce qui est pr&eacute;vu dans Zend Engine 2 ? </strong></p>\r\n<p><strong>Zeev  :</strong> Un nouveau mod&egrave;le objet sera int&eacute;gr&eacute; en liaison avec PHP 5. Il y aura &eacute;galement un support am&eacute;lior&eacute; de .NET et Java.</p>\r\n<p><strong>Comment sont choisis les nouveaux d&eacute;veloppement int&eacute;gr&eacute;s dans les nouvelles versions ? </strong></p>\r\n<p><strong>Rasmus   :</strong> Il n''y a pas vraiment de processus d&eacute;fini. Le choix se fait naturellement a travers la communaut&eacute;. On ne d&eacute;cide pas vraiment "Tiens, on va d&eacute;velopper ceci ou cela..." En fait si quelqu''un veut que quelque chose soit int&eacute;gr&eacute;, la meilleure fa&ccedil;on que ca se fasse est qu''il le code lui-m&ecirc;me ! :)</p>\r\n<p><strong>Quid du support futur d''autres plates-formes (J2EE )etc </strong></p>\r\n<p><strong>Zeev  :</strong> Ce n''est pas pr&eacute;vu. En r&egrave;gle g&eacute;n&eacute;rale, s''ils veulent que leur plate-forme soit support&eacute;e, il est pr&eacute;f&eacute;rable qu''ils le fassent eux-m&ecirc;mes ou qu''ils payent quelqu''un pour cela.</p>\r\n<p><strong>Quel est le business model de Zend ? </strong></p>\r\n<p><strong>Zeev  :</strong> D''un c&ocirc;t&eacute; nous am&eacute;liorons PHP et nous y apportons un support, et de l''autre nous vendons des logiciels commerciaux.</p>\r\n<p><strong>Comment comptes-tu d&eacute;velopper la communaut&eacute; PHP ? </strong></p>\r\n<p><strong>Rasmus   :</strong> Il n''y a pas vraiment de probl&egrave;me a ce niveau, je fais pas mal de conf&eacute;rences pour pr&eacute;senter PHP, mais en fait la communaut&eacute; grandit toute seule.</p>\r\n<p><strong>Un PHP Group est il pr&eacute;vu en France ? </strong></p>\r\n<p><strong>Thies  :</strong> C''est vrai qu''il y en a un en Allemagne mais moi je n''y vais jamais :) En fait c''est aux gens de se motiver pour en former un ! Je suis s&ucirc;r qu''il y a plein de gens sur Paris par exemple qui utilisent PHP et qui seraient int&eacute;ress&eacute;s par se int&eacute;ress&eacute;s de temps en temps pour en discuter.</p>\r\n<h3>Questions du public</h3>\r\n<p><strong>Un portage de Zend Cache sous Windows est il pr&eacute;vu ? </strong></p>\r\n<p><strong>Zeev  :</strong> Pas pour le moment, c''est un peu compliqu&eacute;.</p>\r\n<p><strong>Que pensez vous de Zope par rapport a PHP ? </strong></p>\r\n<p><strong>Rasmus   :</strong> Ce n''est pas la m&ecirc;me chose, ce n''est pas vraiment comparable.</p>\r\n<p><strong>Est il envisag&eacute; de cr&eacute;er un compilateur PHP pour cr&eacute;er des applications ? </strong></p>\r\n<p><strong>Zeev  :</strong> Pas pour le moment, et ca n''a pas trop d''int&eacute;r&ecirc;t... Mais &agrave; terme, ce n''est pas impossible.</p>\r\n<p><strong>Access est il support&eacute; ? Et est il possible de r&eacute;f&eacute;rencer des pages PHP. </strong></p>\r\n<p><strong>Rasmus   :</strong> Oui bien s&ucirc;r. Pour le r&eacute;f&eacute;rencement il n''y a aucun probl&egrave;me.</p>\r\n<p><strong>La licence va-t-elle changer ? </strong></p>\r\n<p><strong>Rasmus   :</strong> Non elle restera toujours Open Source.</p>\r\n<p><strong>Pourquoi le passage en licence BSD ? </strong></p>\r\n<p><strong>Zeev  :</strong> Pour simplifier, il y avait trop de licences diff&eacute;rentes entre PHP, Zend etc...</p>\r\n<p><strong>Mais pourquoi le changement de licence de GPL &agrave; BSD avant ? </strong></p>\r\n<p><strong>Rasmus   :</strong> Il n''y avait pas vraiment de raison d''utiliser la GPL pour un langage de script... En fait je l''avais mis en GPL parceque c''&eacute;tait la seule licence libre que je connaissais. Mais apr&egrave;s nous avons pr&eacute;f&egrave;r&eacute; le mettre dans une licence plus proche de celle d''Apache, vu que PHP y est fortement li&eacute;.</p>\r\n<p><strong>Pour quand est pr&eacute;vu PHP5? </strong></p>\r\n<p><strong>Zeev  :</strong> D''ici le prochain mill&eacute;naire :)</p>\r\n<p><strong>Rasmus   :</strong> C''est vraiment quelque chose qu''on ne peut pas dire, c''est vraiment impr&eacute;visible comme pour tous les projets Open Source.</p>\r\n<p><strong>Est il pr&eacute;vu de mettre en place une certification PHP ? </strong></p>\r\n<p><strong>Rasmus   :</strong> Non pas du tout, ce n''est pas quelque chose qui nous int&eacute;resse. Et nous ne supporterions pas quelqu''un qui le ferait. Par contre des formations ou du support PHP, &ccedil;a oui.</p>\r\n<p><strong>Quels seront les nouveaut&eacute;s de la 4.1.0 ? </strong></p>\r\n<p><strong>Zeev  :</strong> Il y aura tr&egrave;s peu de nouveaut&eacute;s, ce sera essentiellement des corrections de bugs, et de probl&egrave;mes de s&eacute;curit&eacute;.</p>', 0, 1006988400, 1, 0),
(150, 47, 'Frederic BORDAGE, Cyril PIERRE de GEYER', 'Présentation de PHP - F.BORDAGE, C.PIERRE de GEYER', 'pr-sentation-de-php-f-bordage-c-pierre-de-geyer', 'Il sera question des différentes possibilités et phases de PHP ainsi que les possibilités de cette plate-forme.', 'Frederic BORDAGE est journaliste informatique.\r\nCyril PIERRE de GEYER est ingénieur informatique et formateur PHP pour Anaska.', '<table width=100%><tr><td><img16></td><td><b>Présentation de PHP</b> </td><td align="right"><img17></td></tr></table>\r\n\r\nFrederic BORDAGE, journaliste informatique et Cyril PIERRE de GEYER, ingénieur informatique et <a href="http://www.anaska.com/formation-php.php">formateur PHP pour Anaska</a>, ont présentés les différentes phases de PHP ainsi que les possibilités du langage.\r\n\r\nLa salle comprend un public très varié et c''est pour cela que Cyril Pierre de GEYER ainsi que Frédéric BORDAGE présentent rapidement Php ainsi que ses compétences, sans entrer dans les détails.\r\nDes questions "simples" comme "A quoi sert Php ? " mais aussi des thèmes comme " Php dans le monde " trouveront réponse dans cette présentation de trente minutes ; Quelques chiffres sont également présentés comme le nombre de fonctions que contient actuellement Php par rapport à ses " principaux " concurrents.\r\n\r\n\r\n\r\n<u>Lien vers la présentation :</u><a href="http://www.phpteam.net/salon_php_2002/intro/index.html" target=_blank>[lien]</a>\r\n\r\n\r\n<u>Lien vers la vidéo de la présentation (21Mo) :</u><a href="http://mma2001.nexen.net/presa1.rm" target=_blank>[lien]</a>\r\n\r\n<u>Lien vers la vidéo de la présentation (streaming) :</u><a href="pnm://217.174.203.144:7070/presa1.rm" target=_blank>[lien]</a>', 0, 1042412400, 1, 0),
(135, 9, '', 'Yahoo choisi PHP, réaction AFUP', 'yahoo-choisi-php-r-action-afup', '', '', 'Lors de la PHP Con 2002 qui s''est déroulée en californie courant Octobre, Michael J. Radwin, ingénieur chez Yahoo, a annoncé que Yahoo avait décidé de migrer progressivement vers PHP.\r\n\r\nYahoo est le premier site web au monde en terme de volumétrie (1,5 milliards de pages vues par jour). Les contraintes de performance, de stabilité et d''évolutivité de ce site sont donc extrêmement fortes. \r\n\r\nRéaction de l''AFUP :\r\n\r\n"Le choix de PHP par le site le plus consulté au monde est un nouveau pas important dans la reconnaissance des qualités de ce langage par le monde professionnel. Il confirme l''intérêt porté à PHP par de grandes entreprises françaises telles que M6, Libération ou France Télécom. L''Association Française des Utilisateurs de PHP (AFUP) ne peut que se réjouir de cette nouvelle. Elle aura à coup sûr un impact important dans les choix technologiques qu''effectueront les entreprises à l''avenir et conforte celles qui ont déjà choisi ce langage comme fondation de leur stratégie internet"\r\n\r\n', 0, 1036072003, 1, NULL),
(151, 47, 'Zeev Suraski', 'Présentation des outils de Zend - Z.SURASKI', 'pr-sentation-des-outils-de-zend-z-suraski', 'Présentation des outils de Zend.', 'Zeev Suraski est un des principaux membres du CORE PHP. Il est également le représentant de ZEND Technologies dont il est un des fondateurs.', 'Les produits présentés semblent posséder des qualités\r\nindéniables, le prix demandé pour utiliser ces outils est relativement élevé (à part le Zend Studio de base  relativement abordable). \r\n\r\nOn notera cependant une remarque d''un spectateur ayant utilisé le Zend Studio qui signalait une certaine lenteur de l''outil. Effectivement le système est développé en JAVA et il faut une machine puissante pour l''utiliser ( compter 512 Mode mémoire vive).\r\n', 0, 1042406954, 1, NULL),
(152, 47, '', 'Les évolutions de MySQL - J.GALLET', 'les-volutions-de-mysql-j-gallet', 'John Gallet nous présente différentes fonctionnalités peu connues de MySQL.', '', 'John Gallet , travailleur indépendant et expert en SGBD, nous exposera donc comment d''ores et déjà gérer les transactions avec MySQL 3.23.xx (utilisation des bases INODB) ainsi que les innovations de MySQL 4 dans ce domaine.\r\n\r\n\r\n<u>Lien vers la présentation : </u><a href="/forumphp2002/mysql_forum2002.pdf" target=_blank>[lien]</a>\r\n', 0, 1042407168, 1, NULL),
(153, 47, '', 'PHP et Oracle - T.ARNTZEN', 'php-et-oracle-t-arntzen', 'Thies Arntzen présente l''interfaçage de PHP à une base de données Oracle, démontrant les capacités de ce SGBDR dans la gestions des transactions.', '', 'Thies Arntzen présente l''interfaçage de PHP à une base de données Oracle, démontrant les capacités de ce SGBDR dans la gestions des transactions.\r\n\r\n\r\nLes différentes manières de se connecter à Oracle y sont largement abordées mais aussi quelques lignes de codes intéressantes mettant en oeuvre les transactions, dans la continuité de l''intervention de John Gallet à ce sujet.\r\n', 0, 1042407362, 1, NULL);
INSERT INTO `afup_site_article` (`id`, `id_site_rubrique`, `surtitre`, `titre`, `raccourci`, `descriptif`, `chapeau`, `contenu`, `position`, `date`, `etat`, `id_personne_physique`) VALUES
(222, 4, '', 'Bureaux de l''AFUP,  fondateurs et conseil d''administration', 'bureaux-de-l-afup-fondateurs-et-conseil-d-administration', '<p>Voici la liste des diff&eacute;rentes personnes ayant compos&eacute; le bureau de l''association et le conseil d''administration depuis sa cr&eacute;ation.  On retrouvera &eacute;galement les personnes ayant particip&eacute;s &agrave; la cr&eacute;ation de l''association.</p>', '', '<p><strong style="font-weight: bold;">Bureau 2010</strong></p>\r\n<ul>\r\n<li>Pr&eacute;sident : Nicolas Silberman</li>\r\n<li>Tr&eacute;sorier : Perrick Penet</li>\r\n<li>S&eacute;cr&eacute;taire : Hugo Hamon</li>\r\n<li>Vice-Pr&eacute;sident : Olivier Hoareau</li>\r\n<li>2nd Vice-Pr&eacute;sident : Cyril Pierre de Geyer</li>\r\n<li>Vice-Tr&eacute;sorier :<em> en cours</em></li>\r\n<li>2nd Vice-Tr&eacute;sorier : Christophe Villeneuve</li>\r\n<li>Vice-secr&eacute;taire : Rapha&euml;l Rougeron</li>\r\n<li>2nd Vice-Secr&eacute;taire : Gauthier Delamarre</li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p><strong>Bureau 2009</strong></p>\r\n<ul>\r\n<li>Pr&eacute;sident : Xavier Gorse </li>\r\n<li>Tr&eacute;sorier : Christophe Villeneuve </li>\r\n<li>S&eacute;cr&eacute;taire : Sarah Haim </li>\r\n<li>Vice-Pr&eacute;sident : Nicolas Silberman </li>\r\n<li>Vice-Tr&eacute;sorier : Julien Pauli </li>\r\n<li>Vice-secr&eacute;taire : Eric Colinet </li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p><strong>Bureau 2008</strong></p>\r\n<ul>\r\n<li>Pr&eacute;sident : Arnaud LIMBOURG </li>\r\n<li>Tr&eacute;sorier : Christophe Villeneuve </li>\r\n<li>Secr&eacute;taire : Mickael MITHOUARD </li>\r\n<li>Vice-Pr&eacute;sident : Xavier Gorse </li>\r\n<li>Vice-Tr&eacute;sorier : - </li>\r\n<li>Vice-secr&eacute;taire : Sarah Haim et Cyril Grandval </li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p><strong>Bureau 2007</strong> Lors de l''AG 2007 a &eacute;t&eacute; d&eacute;cid&eacute; de faire un passage de temoin en douceur entre le pr&eacute;sident (Guillaume PONCON) et le futur pr&eacute;sident (Arnaud LIMBOURG).</p>\r\n<ul>\r\n<li>Pr&eacute;sident : Guillaume PONCON </li>\r\n<li>Tr&eacute;sorier : Olivier LE CORRE </li>\r\n<li>Secr&eacute;taire : Aur&eacute;lia ZAMBON </li>\r\n<li>Vice-Pr&eacute;sident : Arnaud LIMBOURG </li>\r\n<li>Vice-Tr&eacute;sorier : Rodolphe EVEILLEAU </li>\r\n<li>Vice-secr&eacute;taire : Mickael MITHOUARD </li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p><strong>Bureau 2006</strong></p>\r\n<ul>\r\n<li>Pr&eacute;sident : Perrick PENET </li>\r\n<li>Tr&eacute;sorier : Romain BOURDON </li>\r\n<li>S&eacute;cr&eacute;taire : Arnaud LIMBOURG </li>\r\n<li>Vice-Pr&eacute;sident : Guillaume PONCON </li>\r\n<li>Vice-Tr&eacute;sorier : Jean-Marc FONTAINE </li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p><strong>Bureau 2005</strong></p>\r\n<ul>\r\n<li>Pr&eacute;sident : Perrick PENET </li>\r\n<li>Tr&eacute;sorier : Jean-Marc FONTAINE </li>\r\n<li>S&eacute;cr&eacute;taire : Francois BILLARD-MADRIERES </li>\r\n<li>Vice-Pr&eacute;sident : Damien SEGUY </li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p><strong>Bureau 2003-2004</strong></p>\r\n<ul>\r\n<li>Pr&eacute;sident : Cyril PIERRE de GEYER </li>\r\n<li>Tr&eacute;sorier : Olivier LE CORRE </li>\r\n<li>Secr&eacute;taire : S&eacute;bastien HORDEAUX </li>\r\n<li>Vice pr&eacute;sident : Damien SEGUY </li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p><strong>Bureau 2001-2003</strong></p>\r\n<ul>\r\n<li>Pr&eacute;sident : Armel FAUVEAU </li>\r\n<li>Tr&eacute;sorier : John GALLET </li>\r\n<li>Secr&eacute;taire : Nicolas HOIZEY </li>\r\n<li>Membre du CA : Emmanuel FAIVRE </li>\r\n<li>Membre du CA : Damien SEGUY </li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p><strong>Les Membres Fondateurs</strong></p>\r\n<ul>\r\n<li>Olivier COURTIN </li>\r\n<li>Emmanuel FAIVRE </li>\r\n<li>Armel FAUVEAU </li>\r\n<li>John GALLET </li>\r\n<li>Nicolas HOIZEY </li>\r\n<li>David MOREL </li>\r\n<li>Cyril PIERRE de GEYER </li>\r\n<li>Damien SEGUY </li>\r\n<li>Ghislain SEGUY </li>\r\n<li>Hellekin O. WOLF </li>\r\n</ul>', 0, 1264201200, 1, 0),
(160, 47, '', 'Les modèles de données - E.FAIVRE', 'les-mod-les-de-donn-es-e-faivre', 'Emmanuel FAIVRE, le créateur du package EasyPhp, intervient pour nous parler des Templates. ', '', 'Cyril PIERRE de GEYER commentera un retour d''experience de FRANCE télévision.\r\n<ul>\r\n<li>Pourquoi choisir les templates ? </li>\r\n<li>Quels sont les outils à disposition des développeurs dans ce domaine ?</li> \r\n<li>Du côté performance ?</li> </ul>\r\n\r\nTout est détaillé, des Benchmarks affichés. \r\n\r\nOn pourra retenir, pour être concis, que quelque soit la solution de Template utilisée, le coût en ressources n''est pas négligeable bien que Smarty, la solution offerte par le Php Group, semble être la meilleure au niveau des performances, mais pas forcément la plus simple à mettre en place.\r\n\r\nCyril PIERRE de GEYER de <a href="http://www.anaska.com/formation-php.php">Anaska formation</a> nous aura présenté l''utilisation de Templates dans le cadre d''un projet d''intranet vidéo documentaire pour France télévision.', 0, 1042408368, 1, NULL),
(162, 47, '', 'Les mécanismes internes de PHP - D.RETHANS', 'les-m-canismes-internes-de-php-d-rethans', 'Derick Rethans explique à son auditoire dans le détails comment se déroule le processus d''execution d''un script PHP.', '', 'Les mécanismes internes de PHP\r\n\r\nDerick Rethans explique à son auditoire dans le détails comment se déroule le processus d''execution d''un script PHP.\r\n\r\nLes différentes étapes, allant jusqu''à la "tokenisation" du fichier PHP, sont passées en revue.\r\n\r\nIl poursuit ensuite sur des explications techniques concernant la méthode permettant de créer ses propres modules PHP.Intervention assez soutenue, mais très interessante.\r\n\r\n<u>Lien vers la présentation :</u><a href="forumphp2002/ze-ext/index.html" target=_blank>[lien]</a>\r\n', 0, 1042408848, 1, NULL),
(167, 19, '<p>TV5</p>', 'TV5 utilise PHP ', 'tv5-utilise-php', '<p>Vincent FLEURY, d&eacute;veloppeur au sein du service interactivit&eacute; de TV5, nous pr&eacute;sente leur utilisation de PHP.</p>', '<p>La chaine TV5 est le r&eacute;seau mondial de langue fran&ccedil;aise. C''est le premier r&eacute;seau tout public en terme de foyers initialis&eacute;s (devant CNN). Le site est un carrefour de la connaissance et de l''information en fran&ccedil;ais. <a href="http://www.tv5.org">[Tv5 le site]</a></p>', '<p><br /> <strong>Bonjour monsieur Fleury, pouvez faire un petit historique du site tv5 et indiquer les diff&eacute;rentes technologies employ&eacute;es selon les versions?</strong>&nbsp;</p>\r\n<p>La premi&egrave;re version a &eacute;t&eacute; mise en ligne en 1996 et d&eacute;livrait exclusivement la grille de programmes aux 4 coins du monde. La majeure partie des contenus &eacute;tait statique, le module grille des programmes &eacute;tait en CGI. Le serveur h&ocirc;te &eacute;tait un Linux avec une base de donn&eacute;es MiniSQL. Le site s''est progressivement enrichi sur cette m&ecirc;me architecture jusqu''&agrave; la nouvelle version de d&eacute;cembre 2002. Cette derni&egrave;re a &eacute;t&eacute; r&eacute;alis&eacute;e et design&eacute;e par la soci&eacute;t&eacute; Pr&eacute;f&eacute;rences.</p>\r\n<p><strong>Comment g&eacute;rez vous l''ajout d''informations sur le site et combien de personnes travaillent &agrave; sa gestion ?</strong></p>\r\n<p>Le site est administr&eacute; par rubrique via une interface web. Une &eacute;quipe compos&eacute;e de 10 personnes travaille &agrave; sa gestion.</p>\r\n<p><strong>Pouvez vous nous d&eacute;crire l''architecture technique du site ?</strong></p>\r\n<p>Le site est architectur&eacute; autour de 5 serveurs principaux h&eacute;berg&eacute;s chez Easynet :</p>\r\n<p>&nbsp;</p>\r\n<p>\r\n<ul>\r\n<li>un serveur de pr&eacute; production </li>\r\n<li>un serveur de production </li>\r\n<li>un serveur MySQL </li>\r\n<li>un serveur SDX </li>\r\n<li>un serveur de mails </li>\r\n</ul>\r\n</p>\r\n<p>&nbsp;</p>\r\n<p>\r\n<ul>\r\n</ul>\r\n</p>\r\n<p>Nous travaillons sur un mode pr&eacute; production - production pour tester nos contenus.  Le serveur de production tourne sous Linux Red Hat 6.2, PHP 4.2.3, Apache 1.3.27, MySQL 3.23.46.</p>\r\n<p><strong> Quel est le trafic du site ?</strong></p>\r\n<p>Pour vous donner quelques informations techniques : nous avons une bande passante de 1,5 Mbits avec un burst &agrave; 2 Mbits quand n&eacute;cessaire. Le site g&eacute;n&egrave;re environ 320 000 visites et 2 200 000 pages vues par mois.</p>\r\n<p><strong>Pourquoi avez vous privil&eacute;gi&eacute; PHP par rapport &agrave; ses principales alternatives Weblogic, ASP, JSP ?</strong></p>\r\n<p>Son caract&egrave;re open-source, sa simplicit&eacute;, sa popularit&eacute;, le nombre de ressources en ligne disponibles.  De plus, dans le cadre de notre utilisation, il ne souffre pas de la comparaison avec ses concurrents.</p>\r\n<p><strong>Utilisez vous des langages d''interfacage type XML pour faire communiquer vos diff&eacute;rents syst&egrave;mes d''informations ?</strong></p>\r\n<p>Nous recevons des flux XML &eacute;manant de l''AFP et des organismes internationaux. Ils sont mis en forme avec PHP et XSLT.</p>\r\n<p><strong>Les vid&eacute;os propos&eacute;es sont de tr&egrave;s bonne qualit&eacute;, quelle technologie utilisez vous pour g&eacute;rer le streaming sur votre site ?</strong></p>\r\n<p>Notre partenaire View-On-TV  encode, h&eacute;berge et diffuse nos vid&eacute;os au format Real Video.</p>\r\n<p><strong>Comment faites vous le d&eacute;coupage des vid&eacute;os pour un m&ecirc;me sujet ?</strong></p>\r\n<p>Ce d&eacute;coupage est effectu&eacute; par View-On-TV. Ils appliquent une couche SMIL avec des timecodes sur un fichier surestream et les titres sont archiv&eacute;s dans une base de donn&eacute;es MySQL.  <strong>Monsieur Vincent FLEURY, TV5</strong></p>', 0, 1046214000, 1, 0),
(168, 47, 'Shane Caraveo', 'Introduction to SOAP for PHP - S.CARAVEO', 'introduction-to-soap-for-php-s-caraveo', 'Consume, Create, and Host Web services using PHP and PEAR SOAP. This tutorial will cover the basics of\r\n                            using the SOAP classes in PEAR ("PHP Extension and Application Repository") to consume and create\r\n                            Web Services. An introduction to SOAP and SOAP related technologies will be followed by coverage of the\r\n                            PEAR : :SOAP and PHP-SOAP libraries.', 'Shane Caraveo est un membre important de l''équipe PHP. Il a notamment participé au développement de l''architecture SAP qui permet à PHP d''être pluggé automatiquement à de nombreux serveurs Web.', 'Comment créer, simplement, un web service grâce à PHP.\r\n\r\n<a href="http://talks.php.net/show/soap-forumduphp-paris2002">http://talks.php.net/show/soap-forumduphp-paris2002</a>\r\n', 0, 1041721200, 1, NULL),
(177, 19, '', 'France 3 : Streaming et PHP', 'france-3-streaming-et-php', 'Afin de permettre a ses journalistes de toutes régions de visualiser tous ses sujets vidéos France 3 à developpé un outil ( "le browsing") axé sur PHP.', '', '<img25|left> Veuillez trouver ci joint l''article publié dans le magazine "Programmez" n°53 de mai 2003.\r\n\r\n\r\n</a>\r\nCet applicatif permet aux journalistes de la chaine de visualiser sur l''Intranet les vidéos diffusées récemment provenant de n''importe quelle antenne régionale. \r\n\r\nLe coeur de ce système est basé sur Linux Redhat, Apache, PHP, Zend Accelerator et PostGreSQL.\r\n\r\nNotons que le site du magazine programmez vient de migrer d''ASP vers PHP :\r\n<a href="http://www.programmez.com" target=_blank>[Le site]</a>\r\n', 0, 1057096800, 1, NULL),
(178, 9, '', 'PHP utilisé par les média', 'php-utilis-par-les-m-dia', 'De nombreux médias utilisent PHP. Nous faisons ici un point.', '', 'Aujourd''hui nous allons nous intéresser aux sites des médias qui utilisent PHP; et ils sont nombreux.\r\n\r\nIntéressons nous tout d''abord aux médias télévisuels. \r\nLe réseau hertzien est très bien représenté. En effet, on retrouve France2, France3 dont certaines parties de leur site web ont été réalisées à l''aide du système de publication SPIP. On apprenais recemment que France 3 utilisait même PHP dans le cadre d''un projet d''intranet vidéo documentaire tres complexe.\r\n\r\nPour TF1, ce sont les sites de rencontre et de vidéos qui ont été développés avec cette technologie.Toujours dans cette catégorie, on peut citer également Paris-Premiere, Teva ou encore MCM.\r\n\r\nLes grandes stations de radio sont également bien représentées : NRJ, RMCinfo, Skyrock, RadioFrance, BFM ont tous optés pour PHP.\r\n\r\nLa presse n''est pas non plus en reste. Les journaux Libération, La Tribune, le Figaro ou bien encore La Provence proposent tous des sites dynamiques réalisés en php. Dans le même genre, on retrouve les magazines Télé7jours, la Centrale, Le Point ainsi que 01net.\r\n\r\nLe nombre de site de médias, tournant sous PHP est important. Bon nombre de médias, parmi les plus importants, ont choisi la plateforme PHP.\r\n\r\n\r\nNote : Merci à Arnaud Buchoux pour la synthèse.\r\n', 0, 1053003797, 1, NULL),
(184, 19, '', 'Le BHV joue gagnant avec PHP', 'le-bhv-joue-gagnant-avec-php', '<p>La plate-forme PHP a non seulement combl&eacute; nos besoin, mais elle nous a aussi permis d''aller beaucoup plus loin que nous l''aurions fait avec une autre solution vu nos budgets et la taille de notre &eacute;quipe.</p>', '', '<p>Paris, Aout 2003 Question &agrave; Jean-S&eacute;bastien Fest, webmaster du BHV</p>\r\n<p><strong>Bonjour monsieur Fest, vous &ecirc;tes le Webmaster du BHV et vous avez choisi PHP pour vos d&eacute;veloppement. Pourriez vous nous indiquer quels usages vous faites de ce langage ?</strong></p>\r\n<p>Nous l''utilisons pour nos sites bhv.fr et cyberbricoleur.com (front et administration) ainsi que des outils d''analyses de fr&eacute;quentation, mailing, banni&egrave;res, publipostage, etc.  Les projets PHP ne sont plus du tout limit&eacute;s aux sites Web institutionnels.</p>\r\n<p><strong>Pourquoi PHP plut&ocirc;t qu''une autre technologie ?</strong></p>\r\n<p>PHP est un langage souple, rapide, puissant, facile &agrave; apprendre : c''&eacute;tait important pour nous, car nous ne venions pas du monde informatique. La simplicit&eacute; du langage nous &agrave; permis de nous concentrer rapidement sur les services que l''outil avait a rendre plut&ocirc;t que sur l''outil en lui m&ecirc;me. De plus, PHP est une plate-forme en constante &eacute;volution (ce qui est int&eacute;ressant dans un secteur en pleine &eacute;volution comme le Web). Je n''ai pas trouv&eacute; de concurrent qui r&eacute;unissait toutes ces qualit&eacute;s. Au vu des derni&egrave;res &eacute;volutions et de celles &agrave; venir je suis tr&egrave;s heureux du chemin qu''il prend.</p>\r\n<p><strong>Avec quels outils utilisez-vous PHP ?</strong></p>\r\n<p>Nous utilisons Zend Developement Environement 2.5.</p>\r\n<p><strong>Etes vous satisfait des performances et de la stabilit&eacute; de PHP ?</strong></p>\r\n<p>Oui, PHP associ&eacute; &agrave; Linux, Apache et MySQL tient parfaitement la charge.  Notre serveur le plus charg&eacute; encaisse sans probl&egrave;me 20 requ&ecirc;tes SQL et 10  requ&ecirc;tes HTTP par seconde.</p>\r\n<p><strong>Quels sont selon vous les 3 points forts de PHP ?</strong></p>\r\n<p><strong></strong> Simplicit&eacute;, puissance (performance, stabilit&eacute;, etc.), et une communaut&eacute; tr&egrave;s active (support et scripts disponibles).</p>\r\n<p><strong>Et les 3 points faibles ?</strong></p>\r\n<p>Le support des objets (PHP4), une mauvaise r&eacute;putation ill&eacute;gitime.</p>\r\n<p><strong>Bilan ?</strong></p>\r\n<p>Les gains les plus &eacute;vidents sont, l''absence de licence et le support technique apport&eacute; par la communaut&eacute;. Mais aussi que sa facilit&eacute;e de mise en oeuvre et de d&eacute;boguage diminue le nombre de jours de d&eacute;veloppement et le nombre de d&eacute;veloppeurs tout en gardant une excellente qualit&eacute; du produit final.  <strong>BHV - Jean-S&eacute;bastien Fest, webmaster du BHV</strong></p>', 0, 1060639200, 1, 0),
(188, 19, 'SDVP', 'SDVP-Le Parisien migre d''ASP à PHP', 'sdvp-le-parisien-migre-d-asp-php', 'Paris, Septembre 2003 Question à Michael DEHOORNE, Responsable étude et développement de la Société de Vente et de Distribution du Parisien (SDVP).', 'La Société de Distribution et de Vente du Parisien (SDVP) s''occupe de la gestion logistique et des ventes du journal Le Parisien.\r\n', '<p><b>Quelle utilisation faites-vous de PHP ?</b></p>\r\n\r\n<p>Nous utilisons PHP pour un intranet à vocation décisionnelle.</p>\r\n\r\n<p><b>Quelle est la volumétrie de cette application ?</b></p>\r\n\r\n<p>Environ 200 utilisateurs accèdent à cette application dont 40 en simultané.</p>\r\n<p>L''application représente une centaine de tables hébergées dans la base SQL Server de Microsoft. </p>\r\n<p>L''architecture est globalement une table égale une page PHP. Nous avons donc de très bon temps de réponse.</p>\r\n\r\n<p><b>PHP tient-il la charge et est-il stable dans le temps ?</b></p>\r\n\r\n<p>Oui. Nous n''avons eu aucun souci depuis 2 ans.</p>\r\n\r\n<p><b>Quels critères avez-vous pris en compte pour choisir PHP plutôt qu''une autre technologie ?</b></p>\r\n\r\n<p>Nous utilisions ASP (Active Server Pages) de Microsoft et sommes passés à PHP afin de pouvoir profiter des librairies disponibles sur le web : jpgraph, fpdf, etc.</p>\r\n\r\n<p><b>Considérez-vous PHP comme une plate-forme globale au même titre que .NET et J2EE ?</b></p>\r\n\r\n<p>Oui, complètement.</p>\r\n\r\n<p><b>Organisez-vous votre code PHP sous la forme de librairie de classes ?</b></p>\r\n\r\n<p>Oui, mais uniquement pour certaines fonctionnalités récurrentes telles que la génération de classeurs Excel,  de documents PDF, et de tableaux avec des styles propre à notre société.</p>\r\n\r\n<p><b>Quels sont selon vous les 3 principaux avantages de PHP ?</b></p>\r\n\r\n<p>Evolutivité, diversité des librairies disponibles et gratuité de l''ensemble.</p>\r\n\r\n<p><b>Les 3 principaux inconvénients ?</b></p>\r\n\r\n<p>Je n''en vois pas</p>\r\n\r\n<p><b>Au final, votre bilan est-il positif ?</b></p>\r\n\r\n<p>Complètement vu que je n''y trouve pas d''inconvénient.</p>\r\n\r\n<p>', 0, 1062453600, 1, 0),
(233, 46, 'Support professionnel', 'Où trouver du support ?', 'o-trouver-du-support', 'Bilan des multiples resources disponibles pour trouver du support.', 'Dans le cas où vous souhaiteriez un support professionnel vous pouvez vous adresser à l''un des prestataires présent dans l''annuaire:\r\n<a href="https://afup.org/article.php3?id_article=232">Annuaire professionnel</a>', 'Ce qui fait, en partie, la richesse de PHP, c''est le nombre de ressources qui lui sont associées. En effet, à travers le web, on retrouve une multitude de forums, channels IRC, forums de news ou autres mailing lists, dont certains sont capables de constituer un véritable support (gratuit) digne de ce nom. Voici donc une liste (non exhaustive) susceptible d''apporter des réponses à vos questions. A vous de faire votre choix.\r\n\r\n\r\n<strong>Web :</strong>\r\n\r\n<ul>\n<li><a href="http://fr.php.net/manual/fr/index.php">Le manuel officiel</a>\r</li>\n<li><a href="http://fr.php.net/manual/fr/index.functions.php">Index des fonctions</a> \r</li>\n</ul>\n\r\n<ul>\n<li><a href="http://www.phpfrance.com/forums/">Forums phpFrance</a> \r</li>\n<li><a href="http://www.phpdebutant.org/article128.php">Forums phpDebutant</a>\r</li>\n<li><a href="http://www.phpscripts-fr.net/forum/">Forums phpScripts</a>\r</li>\n<li><a href="http://www.nexen.net/forum/list.php?f=5">Forums Nexen</a> \r</li>\n<li><a href="http://www.phpindex.com/agora/index.php3?site=phpindex">Forums phpIndex</a> \r</li>\n<li><a href="http://www.phpteam.net/forum2/phpBB2/index.php">Forums phpTeam</a>\r</li>\n<li><a href="http://php.developpez.com/">Forums Developpez</a>\r</li>\n</ul>\n\r\n<strong>Usenet :</strong>\r\n\r\n<ul>\n<li><html>news://fr.comp.lang.php</html>\r</li>\n<li><html>news://alt.fr.comp.lang.php</html>\r</li>\n<li><html>news://comp.lang.php (Anglais)</html>\r</li>\n</ul>\n\r\n<ul>\n<li>Le serveur de news du site php.net : <a href="http://news://news.php.net">news://news.php.net</a> (Anglais)\r</li>\n</ul>\n\r\n<strong>IRC :</strong>\r\n\r\n<ul>\n<li>Undernet @ #PHPFrance\r</li>\n<li>Undernet @ #PHPExpert\r</li>\n<li>Efnet @ #PHP (Anglais)\r</li>\n<li>Liste des serveurs : <a href="http://www.undernet.org/servers.php">http://www.undernet.org/servers.php</a>\r</li>\n</ul>\n\r\n<strong>Mailing Lists :</strong>\r\n\r\n<ul>\n<li><a href="http://www.php.net/mailing-lists.php">Listes anglophones de php.net</a>\r</li>\n<li><a href="http://www.ilovephp.com/mailinglist/">Liste php-france@linuxfr.org</a></li>\n</ul>', 0, 1079360978, 1, NULL),
(212, 19, '<p>www.lemonde.fr</p>', 'Le journal en ligne Le monde ', 'le-journal-en-ligne-le-monde', '<p>Le JDNet solution publie une interview de Jean Christophe Potocki, directeur informatique de Lemonde.fr.</p>', '<p>Le Monde.fr est un journal en ligne proposant aux internautes &laquo; toute l''information au moment de leur connexion &raquo;.</p>', '<p>Le JDNet solution publie une interview de Jean Christophe Potocki, directeur informatique de Lemonde.fr.  A cette interview vous trouverez un bon complement d''information sur le site d''actualite PHP PHPindex avec l interview d''Olivier Grange-Labat.  <a href="http://solutions.journaldunet.com/itws/040927_it_lemonde.shtml" target="_blank">[Journal du Net]</a> <a href="http://www.phpindex.com/news/news_lire.php3?element=2036" target="_blank">[PHPIndex]</a></p>', 0, 1074207600, 1, 0),
(192, 9, '', 'Oracle Application Server incluera PHP dans sa distribution standard', 'oracle-application-server-incluera-php-dans-sa-distribution-standard', 'Oracle intègrera le langage PHP dans l''Oracle Application Server et propose d''ores et déjà des ressources en ligne pour l''interaction de PHP avec Oracle.', '', 'Une preuve de plus si besoin en était que PHP est désormais un acteur incontournable du web dynamique en milieu professionnel, l''éditeur Oracle distribuera bientôt PHP avec l''Oracle Application Server, comme l''indique une <a href="http://otn.oracle.com/tech/opensource/php/php_ohs_sod.html">note d''orientation</a> disponible sur l''Oracle Technology Network. \r\n\r\nDe nombreuses ressources concernant l''intégration d''Oracle et de PHP sont disponibles sur <a href="http://otn.oracle.com/tech/opensource/index.html">l''Open Source Developers Technology Center</a>\r\n\r\n', 0, 1065536940, 1, NULL),
(198, 19, '', 'Gems-plus:"Toute notre activité repose sur PHP"', 'toute-notre-activit-repose-sur-php', '<p>Gems-plus s''est appuy&eacute;e sur PHP et MySQL pour d&eacute;veloppeer la gestion de stock au coeur de son activit&eacute; de n&eacute;goce. Un choix qu''elle ne regrette pas.</p>', '', '<p>&nbsp;</p>\r\n<p>Paris, octobre 2003, question &agrave; Thierry Pradat, fondateur du site marchand <a href="http://www.gems-plus.com">www.gems-plus.com</a>. <br /> <br /> <strong>Quelle est votre utilisation de PHP ?</strong></p>\r\n<p>Notre site marchand www.gems-plus.com s''appuie sur PHP et MySQL. Le back-office de ce site est notre principal outil de gestion. Il nous permet de g&eacute;rer un stock temps r&eacute;el avec toutes les informations indispensables : prix de revient unitaire, prix total, alertes d&eacute;clench&eacute;es par des seuil, etc.</p>\r\n<p><strong>Est-ce une application strat&eacute;gique ?</strong></p>\r\n<p>Oui. Nous n''avons pas d''autre outil de gestion, cette application est donc au c&oelig;ur de notre activit&eacute; quotidienne. De plus, nous r&eacute;alisons 85% de notre chiffre d''affaires en ligne. Comme je n''ai plus de catalogue papier, ce site est hautement strat&eacute;gique pour d&eacute;velopper mes ventes.</p>\r\n<p><strong>Pourquoi avoir choisi PHP et MySQL ?</strong></p>\r\n<p>Ce sont mes prestataires <a href="http://www.saphirtech.com">SaphirTech</a> et <a href="http://www.resmo.net">Resmo</a> qui ont fait ce choix. Ils sont sp&eacute;cialis&eacute;s dans ces technologies et m''avaient d&eacute;j&agrave; prouv&eacute; leur haut niveau de comp&eacute;tence. Je leur ai donc fait enti&egrave;rement confiance et je ne le regrette pas.</p>\r\n<p><strong>Pourquoi ?</strong></p>\r\n<p>Performance et fiabilit&eacute; sont au rendez-vous et l''interface web de l''application est tr&egrave;s facile &agrave; utiliser. C''est important car je remplis moi m&ecirc;me la base de nouveaux produits quasiment quotidiennement. Ce sont en effet les nouveaut&eacute;s qui attirent les clients.</p>\r\n<p><strong>Conseilleriez-vous PHP et MySQL &agrave; une PME ?</strong></p>\r\n<p>D&eacute;finitivement oui. Je connais moins les autres technologies mais PHP et MySQL me donne enti&egrave;re satisfaction (&agrave; 500% m&ecirc;me). Sans parler du fait qu''il n''y a aucune licence &agrave; payer.</p>\r\n<p><strong>Quels sont selon vous les 3 mots-cl&eacute;s qui r&eacute;sument le mieux PHP ?</strong></p>\r\n<p>Prix, fiabilit&eacute; et simplicit&eacute;</p>\r\n<p>&nbsp;</p>', 0, 1038006000, 1, 0),
(197, 19, '', 'Cermex: "Nous développons nos logiciels métier avec PHP" ', 'nous-d-veloppons-nos-logiciels-m-tier-avec-php', '<p>Filiale du groupe international Sidel, Cermex  s''appuie sur PHP pour d&eacute;velopper l''ensemble de ses logiciels m&eacute;tier. Les explications de son responsable informatique.</p>', '<p>Cermex fait partie du groupe Tetra au travers de la filiale Sidel. Cermex con&ccedil;oit et assemble des machines d''emballage carton et films plastiques. Il s''agit d''un groupe international dont les cinq sites sont en France, aux USA et en Angleterre.  Le CA de 2002 est approximativement de 65 millions d''euros.</p>', '<p>Paris, octobre 2003, question &agrave; Eric Poisse, Responsable informatique de Cermex, filiale de Sidel.</p>\r\n<p><strong>Pouvez-vous nous d&eacute;crire votre utilisation de PHP ?</strong></p>\r\n<p>PHP est utilis&eacute; chez nous pour les applications m&eacute;tiers au sein de notre Intranet applicatif. Cette intranet est accessible sur 4 sites. Nous traitons des applications de gestion de dossier client, de publications techniques, de suivi de projet...</p>\r\n<p><strong>Quelle est la volum&eacute;trie de ces projets ?</strong></p>\r\n<p>500 personnes travaillent sur l''Intranet</p>\r\n<p><strong>PHP tient-il la charge et est-il stable dans le temps ?</strong></p>\r\n<p>Nous n''avons pas de probl&egrave;me de charge avec PHP, car nous travaillons dans une architecture de cluster LVS avec plusieurs serveurs WEB. PHP est tr&egrave;s &eacute;conome en ressources.</p>\r\n<p><strong>Quels crit&egrave;res avez-vous pris en compte pour choisir PHP plut&ocirc;t qu''une autre technologie ?</strong></p>\r\n<p>La rapidit&eacute; de d&eacute;veloppement et de maintenance, l''effacit&eacute; du langage. L''interop&eacute;rabilit&eacute; avec de nombreux syst&egrave;mes (SGBD, SAP, Annuaires LDAP ...).</p>\r\n<p>Le nombre de comp&eacute;tences disponibles sur le march&eacute;.</p>\r\n<p><strong>Consid&eacute;rez-vous PHP comme une plate-forme d''entreprise au m&ecirc;me titre que .NET et J2EE ?</strong></p>\r\n<p>D''un point de vue pratique oui. De plus, elle me semble plus r&eacute;pandue que ces deux technologies.</p>\r\n<p><strong>Exposez-vous votre logique m&eacute;tier au travers d''autres interfaces ?</strong></p>\r\n<p>Oui, nous utilisons CLI pour des applications en ligne de commande ainsi que des services web.</p>\r\n<p><strong>Organisez-vous votre code PHP sous forme de librairie de classes ?</strong></p>\r\n<p>Oui, mais &eacute;galement en fonction en attendant PHP5.</p>\r\n<p><strong>Recourez-vous &agrave; un d&eacute;couplage entre pr&eacute;sentation, traitement et donn&eacute;es ?</strong></p>\r\n<p>Oui, nous avons d&eacute;velopp&eacute; un outil de g&eacute;n&eacute;ration d''application PHP bas&eacute; sur un dictionnaire de donn&eacute;es et une ergonomie d&eacute;finie. Dans cet outil, la pr&eacute;sentation est prise en charge par l''outil.</p>\r\n<p><strong>Utilisez-vous des "progiciels" s''ex&eacute;cutant au dessus du quator Linux Apache MySQL PHP ?</strong></p>\r\n<p>Non. En revanche nous utilisons d''autres outils sous Linux tels qu''Oracle ou Inktomi.</p>\r\n<p><strong>Quels sont selon vous les 3 principaux avantages de PHP ?</strong></p>\r\n<ul>\r\n<p>&nbsp;</p>\r\n<li>Rapide en d&eacute;veloppement, maintenance et ex&eacute;cution, stable et performant.\r\n<p>&nbsp;</p>\r\n</li>\r\n<li>Ouverture.\r\n<p>&nbsp;</p>\r\n</li>\r\n<li>Extr&egrave;mement r&eacute;pandu mais paradoxalement mal connu des DSI.\r\n<p>&nbsp;</p>\r\n</li>\r\n</ul>\r\n<p><strong>Les 3 principaux inconv&eacute;nients ?</strong></p>\r\n<ul>\r\n<p>&nbsp;</p>\r\n<li>Pas encore objet.\r\n<p>&nbsp;</p>\r\n</li>\r\n<li>Il manque quelques outils de d&eacute;bug.\r\n<p>&nbsp;</p>\r\n</li>\r\n<li>R&eacute;putation (injustifi&eacute;e) d''outil de "bricoleur".\r\n<p>&nbsp;</p>\r\n</li>\r\n</ul>\r\n<p><strong>Au final, votre bilan est-il positif ?</strong></p>\r\n<p>Assur&eacute;ment !</p>', 0, 1069542000, 1, 0),
(199, 9, '', 'Sun intégre PHP à Java System Web Server', 'sun-int-gre-php-java-system-web-server', 'Zend Technologies et Sun ont signé un accord pour intégrer PHP à la dernière version du serveur web de Sun', '', 'Zend et Sun viennent de signer un accord pour intégrer un environnement PHP stable et performant à Java System Web Server 6.0, la dernière version du serveur web de Sun.\r\n<br>\r\n<ul>\n<li>Le plugin "PHP Enabler for Sun Java System Web Server" fournit une passerelle FastCGI optimisée entre PHP et le serveur web de Sun.\r</li>\n</ul>\n<br>\r\n<ul>\n<li>et "Zend Performance Suite for Sun Java System Web Server" accélère l''exécution du code PHP, cache le contenu et compresse les données fournies par PHP au serveur web de Sun.\r</li>\n</ul>\n<br>\r\n<br>\r\nPour en savoir plus :\r\n<ul>\n<li><a href="http://www.zend.com/sun/">Zend</a>\r</li>\n<li><a href="http://wwws.sun.com/software/products/web_srvr/home_web_srvr.html">Sun</a>\r</li>\n</ul>\n', 0, 1069542000, 1, NULL),
(215, 56, '', 'Les supports de conférences sont disponibles', 'les-supports-de-conf-rences-sont-disponibles', 'Les supports du forum PHP 2003 sont en ligne.', '=forumphp2003/resume.php', 'Disponibles à l''adresse suivante :\r\n<a href="https://afup.org/forumphp/resume.php">URL</a>\r\n\r\n\r\n\r\n\r\n', 0, 1071442800, 1, NULL),
(220, 22, '', 'Who is AFUP ?', 'who-is-afup', 'The AFUP was created to meet the growing needs of companies : they want a reference to answer their PHP problems.', '', 'The AFUP''s goal is to share information : it highlights PHP keys elements to ensure PHP is suitable to the needs and restricts of your mission.\r\n\r\nThe AFUP offers networking meeting points and technical resources for developpers who want to contribute to the PHP project.\r\nThe AFUP unites users nationwide.\r\n	\r\n\r\n\r\n', 0, 1009407600, 1, NULL),
(223, 4, '', 'Membres de l''AFUP', 'membres-de-l-afup', '<p>Retrouvez ici la liste des personnes soutenant PHP par l''interm&eacute;diaire de l''AFUP.</p>', '', '<p>Vous pouvez trouver ici la liste des membres ayant accept&eacute;s que nous publiions leur nom :  <strong>Membres d''honneurs</strong></p>\r\n<ul>\r\n<li>Rasmus LERDORF, cr&eacute;ateur de PHP. </li>\r\n<li>Zeev SURASKI, co-cr&eacute;ateur de PHP. </li>\r\n<li>Derick RETHANS, membre du PHPGroup. </li>\r\n</ul>\r\n<p><strong>Membres</strong></p>', 0, 1073170800, -1, 0),
(224, 9, '', 'Migrer vers PHP pour réduire les coûts', 'migrer-vers-php-pour-r-duire-les-co-ts', 'Face à la complexité de J2EE et d''ASP.NET, un nombre croissant d''entreprises migre vers le langage open source PHP, plus simple à mettre en oeuvre et plus économique.', '', 'Face à la complexité de J2EE et d''ASP.NET, un nombre croissant d''entreprises migre vers le langage open source PHP, plus simple à mettre en oeuvre et plus économique.\r\n\r\nUn article complet sur 01net :\r\n<a href="http://www.01net.com/article/234237_a.html">http://www.01net.com/article/234237_a.html</a>', 0, 1078500010, 1, NULL),
(228, 53, '', 'Télécharger PHP', 't-l-charger-php', 'Espace de téléchargement de PHP', '=http://fr.php.net/downloads.php', '', 0, 1078182000, 1, NULL),
(229, 53, '', 'Télécharger MySQL', 't-l-charger-mysql', 'Espace de téléchargement MySQL', '=http://www.mysql.com/downloads/index.html', '', 0, 1078095600, 1, NULL),
(230, 53, '', 'Télécharger Apache', 't-l-charger-apache', 'Espace de téléchargement Apache', '=http://httpd.apache.org/download.cgi', '', 0, 1078095600, 1, NULL),
(232, 22, '', 'Annuaire de prestataires', 'annuaire-de-prestataires', '', '=https://afup.org/annuaire/', '', 0, 1047337200, 1, NULL),
(234, 9, '', 'Transformations XML avec XSLT et PHP', 'transformations-xml-avec-xslt-et-php', 'En peu de temps, XML est devenu le langage d''échange entre applications. Grâce à des outils comme XSLT, il est capable de se transformer en d''autres langages comme par exemple le HTML pour être compris et affiché par tout navigateur.', '', 'En peu de temps, XML est devenu le langage d''échange entre applications. Grâce à des outils comme XSLT, il est capable de se transformer en d''autres langages comme par exemple le HTML pour être compris et affiché par tout navigateur. Toutprogrammer nous propose un article sur les différentes approches d''utilisation de XSLT avec PHP.\r\n\r\n<a href="http://toutprogrammer.com/article_18.html">Le site</a>\r\n\r\n', 0, 1079547583, 1, NULL),
(236, 9, '', 'PHP et .NET comparé sur le site technique d''Oracle', 'php-et-net-compar-sur-le-site-technique-d-oracle', 'Sean Hull prend le point de vue d''Oracle pour comparer PHP et ASP.NET. Il aborde le sujet du prix, de l''efficacité, de la sécurité, de la portabilité et de l''Open Source (sic). Selon l''auteur, ASP.NET se montre inférieur à PHP sur 6 critères, alors que les deux technologies sont au même niveau sur les 3 autres critères.', '', 'Sean Hull prend le point de vue d''Oracle pour comparer PHP et ASP.NET. Il aborde le sujet du prix, de l''efficacité, de la sécurité, de la portabilité et de l''Open Source (sic). Selon l''auteur, ASP.NET se montre inférieur à PHP sur 6 critères, alors que les deux technologies sont au même niveau sur les 3 autres critères. Un comparatif PHP 4, PHP 5 et ASP.NET est disponible en fin d''article.\r\n\r\n<a href="http://otn.oracle.com/pub/articles/hull_asp.html">Consultez l''article en ligne</a> ', 0, 1080878417, 1, NULL),
(238, 9, '', 'La moitié des conseils régionaux adoptent PHP', 'la-moiti-des-conseils-r-gionaux-adoptent-php', 'C''est ce qui ressort d''une étude menée au mois d''Avril par Nexen.net, auprès des sites des conseils régionaux de 26 régions française : 13 d''entre elles utilisent PHP sur leur site.', '', 'C''est ce qui ressort d''une étude menée au mois d''Avril par Nexen.net, auprès des sites des conseils régionaux de 26 régions française. 13 d''entre elles utilisent PHP sur leur site. L''essentiel des conseils adoptent une architecture LAMP, avec parfois même des expériences intéressantes avec Apache 2.* (cas de l''Aquitaine). Notons aussi les cas de la Champagne-Ardenne et de la Picardie, qui associent PHP et IIS.\r\n\r\n[\r\nVoir la carte de France d''utilisation->http://www.nexen.net/interview/index.php?id=38]', 0, 1082982747, 1, NULL),
(240, 9, '', 'JournalduNet : PHP parmi les trois principaux langages d''apprentissage', 'journaldunet-php-parmi-les-trois-principaux-langages-d-apprentissage', 'D''après un sondage réalisé par le journal du Net, PHP serait le troisième langage le plus utilisé pour apprendre l''informatique. Il se situe juste derrière C/C++ et Basic, mais desormais devant Pascal/Delphi.', '', '"Le langage de script a dépassé l''habituel Pascal/Delphi, et pourrait bientôt prendre sa seconde place au grand classique Basic."\r\n\r\n<a href="http://developpeur.journaldunet.com/news/040504_sondage.shtml">Lien</a>\r\n\r\nDans la même catégorie on peut également noter le classement des langages de TIOBE :\r\n\r\n<a href="http://www.tiobe.com/tpci.htm">Lien</a>', 0, 1083762718, 1, NULL),
(241, 63, '', 'eGroupWare', 'egroupware', 'eGroupWare est une application web Open Source de collaboration, similaire à Lotus Note. Au mois de mai \r\n2004 eGroupWare a été nommé par SourceForge.net projet du mois.', '', 'Ce logiciel est un framework complet, et inclus un calendrier, un Wiki et un système de gestion de contenu puissant. Avec son framework ouvert et ses API publiques, il peut être étendu en utilisant des modules tiers. \r\n\r\neGroupWare a été un succes endémique sur SourceForge.net (SF.net). Lancé en avril 2003, le projet est listé comme un des 10 plus actifs sur sourceforce, et fait plus de 150,000 downloads en pres de 12 mois. Avec son interface conviviale et une communauté consciencieuse, ce n''est pas une surprise que des institutions, comme le gouvernement du Brésil, ait choisit eGroupWare.\r\n\r\neGroupWare a été nommé par SourceForge.net projet du mois de Mai 2004.\r\n\r\n<ul>\n<li><a href="http://egroupware.org">Le site</a>\r</li>\n<li><a href="http://egroupware.org/egroupware/login.php?domain=demo">Démo</a>\r</li>\n</ul>\n', 0, 1084118449, 1, NULL),
(246, 19, '<p>Syst&egrave;me d''Information du Gouvernement</p>', 'Le gouvernement français oeuvre pour PHP et pour le libre', 'le-gouvernement-fran-ais-oeuvre-pour-php-et-pour-le-libre', '<p>Le gouvernement par l''interm&eacute;diaire du cabinet du premier ministre fait la promotion du libre et aide &agrave; &eacute;conomiser l''argent public en publiant les sources d''un projet de gestion de contenu adapt&eacute; aux organismes publics.</p>', '<p>Le d&eacute;partement multim&eacute;dia est en charge de la communication en ligne au sein du SIG, agence de communication du Gouvernement. A ce titre, il a une &laquo; double casquette &raquo; : un r&ocirc;le op&eacute;rationnel par la cr&eacute;ation et gestion de sites internet (forum.gouv.fr, internet.gouv.fr, par exemple), et une mission de coordination vis &agrave; vis des &eacute;quipes webs des minist&egrave;res (rapprochement &eacute;ditorial, conseil et expertise, mutualisation de prestation et d''outils, etc.)</p>', '<p>Le projet SPIP Agora, d&eacute;velopp&eacute; en PHP, se base sur le syst&egrave;me de gestion de contenu Open Source <a href="http://www.spip.net/">Spip</a>.  Apres avoir utilis&eacute; et test&eacute; de nombreux outils et langages le SIG (Syst&egrave;me Information du Gouvernement) a opt&eacute; pour l''utilisation de PHP.  Le SIG a lanc&eacute; le projet SPIP Agora pour :</p>\r\n<p>&nbsp;</p>\r\n<ul>\r\n<li> Se doter d'' un outil unique de gestion de contenu pour g&eacute;rer l''ensemble de nos sites pr&eacute;sents et &agrave; venir. </li>\r\n<li> Faire le choix d''un environnement technologique unique et &laquo; standard &raquo;. </li>\r\n<li> Faire le choix d''un outil convivial et simple d''utilisation. </li>\r\n<li> Permettre et favoriser l''interop&eacute;rabilit&eacute; des sites via des flux &laquo; XML RSS &raquo;. </li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p>La d&eacute;marche est int&eacute;ressante dans le fait que le gouvernement ach&egrave;te un d&eacute;veloppement avec pour l''objectif de le partager. Le SIG reverse donc dans la communaut&eacute; du libre les sources de SPIP AGORA.   "Nous prenons aujourd''hui la parole sur ces listes pour vous annoncer l''ouverture du site <a href="http://www.agora.gouv.fr">www.agora.gouv.fr</a> qui marque ainsi la diffusion de SPIP-AGORA sous licence GPL, et donc le reversement de ces d&eacute;veloppements par le Service d''Information du Gouvernement qui les a command&eacute;s et pay&eacute;s, dans la communaut&eacute; du libre."</p>', 0, 1088028000, 1, 0),
(247, 9, '', '09/07/2004 : Conférence sur PHP aux rencontres mondiales du logiciel libre', '09-07-2004-conf-rence-sur-php-aux-rencontres-mondiales-du-logiciel-libre', 'L''AFUP participe aux rencontres mondiales du logiciel libre en organisant une session sur PHP et plus particulièrement la version 5.', '', 'Cyril PIERRE de GEYER, notre président et co auteur du <a href="http://www.phpteam.net/livres/details.php?id=15">livre PHP 5 avancé</a>, aura l''occasion de présenter PHP en tant que plateforme de développement puis de s''orienter vers les nouveautés de PHP5.\r\n\r\nLa participation à cette manifestation est gratuite et se déroule à bordeaux.\r\n\r\n<a href="http://rencontresmondiales.org/rubrique1.html">Le site des rencontres mondiales du logiciel libre</a>\r\n\r\n<a href="https://afup.org/docs/PHP5_rmll_juillet2004_finale.sxi">Le support de conférence (formation Open Office)</a>', 0, 1088632800, 1, NULL),
(248, 53, 'Liens PHP 5', 'PHP 5 disponible pour la production', 'php-5-disponible-pour-la-production', 'Apres un long processus qualité la version finale de PHP 5 est enfin disponible. Les changements par rapport à PHP 4 sont très importants, il ne s''agit pas d''une simple mise à jour mais d''une refonte complète du moteur. ', '<ul>\n<li><a href="http://www.php.net/downloads.php">Télécharger PHP 5</a>\r</li>\n<li><a href="http://www.wamp5.com">Installeur Windows Apache MySQL PHP 5</a>\r</li>\n<li><a href="http://www.phpteam.net/php5.php">Articles sur PHP 5</a>\r</li>\n</ul>\n', '<strong>PHP 5 est sorti !</strong>\r\n\r\nApres un long processus qualité la version finale de PHP 5 est enfin disponible. Les changements par rapport à PHP 4 sont très importants, il ne s''agit pas d''une simple mise à jour mais d''une refonte complète du moteur. \r\n\r\nAu menu des principales nouveautés on peut trouver :\r\n<ul>\n<li>Support objet complet; \r</li>\n<li>Gestion des exceptions;\r</li>\n<li>Refonte du support XML basé sur la <a href="http://www.xmlsoft.org/">libxml2</a>;\r</li>\n<li>Simplification de l''utilisation d''XML, notamment avec <a href="http://www.eyrolles.com/Chapitres/9782212113235/chap20_Daspet.pdf">simplexml</a>; \r</li>\n<li>Intégration d''une base de données embarquée : <a href="http://www.sqlite.org">SQLite</a>;\r</li>\n<li>Nouvelle extension MySQLi permettant de gérer les nouvelles possibilités de MySQL 4.1 et +;\r</li>\n<li>Amélioration de la gestion des fluxs;\r</li>\n<li>Refonte et intégration d''une toute nouvelle extension SOAP afin de simplifier l''interfaçage avec les WebServices.\r</li>\n</ul>\n\r\n\r\n<strong>Ce qu''est PHP</strong>\r\n\r\nPHP (PHP : Hypertext Preprocessor) est à la fois un langage de programmation (comme Java ou C#) et une plate-forme globale d''entreprise (comme J2EE ou .NET).\r\n\r\nEn tant que langage, PHP possède deux syntaxes. La première à mi chemin entre C et Perl s''adresse aux développeurs à la recherche d''un langage de script simple à manipuler. Elle est adaptée à la couche présentation. Très proche de Java, la seconde permet de développer dans un paradigme totalement orienté objet. Elle est adaptée au développement de logique métier ou de traitements complexes.\r\n\r\nPHP permet de développer tous type d''application :\r\n<ul>\n<li>des applications web dynamiques (site web, intranet, etc.),\r</li>\n<li>des applications client-serveur (PHP-GTK et PHP4Delphi),\r</li>\n<li>des application locales s''exécutant sur le poste de l''utilisateur,\r</li>\n<li>des services web (SOAP, XML-RPC, REST),\r</li>\n<li>des scripts de commande en ligne (CLI).\r</li>\n</ul>\n\r\n\r\n\r\n<strong>Historique</strong>\r\n\r\nLe langage PHP date de 95 il servait alors uniquement de système de gabarits pour pages Web. La version 3 amène en 98 un vrai moteur de script tout à fait fonctionnel qui gagne vite une forte communauté. \r\n\r\nEn 2000 le moteur voit arriver une nouvelle version, PHP 4. Les performances sont au rendez-vous et la modularité permet l''apparition d''extensions pour gérer tout ce qui peut l''être, de la connexion LDAP jusqu''aux interfaces GTK, en passant par la correction orthographique. \r\n\r\nLa venue de PHP5 amène de grandes nouveautés pour un outil qui se veut à double emploi : facile et utilisable pour des applications simples à destination d''un large public, performant et puissant pour des applications métiers à destination d''un public professionnel. On ne parle plus alors uniquement de langage de programmation mais de plateforme à part entière.', 0, 1089669600, 1, NULL);
INSERT INTO `afup_site_article` (`id`, `id_site_rubrique`, `surtitre`, `titre`, `raccourci`, `descriptif`, `chapeau`, `contenu`, `position`, `date`, `etat`, `id_personne_physique`) VALUES
(249, 19, '', 'Houra.fr de Vignette à PHP', 'houra-fr-de-vignette-php', '<p>Jean Pierre VINCENT responsable technique de l''hypermarch&eacute; en ligne houra.fr r&eacute;pond &agrave; nos questions sur leur syst&egrave;me d''information et sur leur utilisation de PHP.</p>', '<p>houra.fr est un hypermarch&eacute; en ligne.  <a href="http://www.houra.fr">Faites vos courses sur Internet</a></p>', '<p>Paris le 16 Juillet 2004. Interview par Cyril PIERRE de GEYER pour le compte de l''AFUP.</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Commen&ccedil;ons par le site houra.fr pourriez vous nous en dire un peu plus  ?</strong></p>\r\n<p>&nbsp;</p>\r\n<p><a href="http://www.houra.fr/">houra.fr</a> est un hypermarch&eacute; en ligne. on y vend 50 000 r&eacute;f&eacute;rences dont une grosse part d''alimentaire avec en plus ce qu''il faut pour la maison, un peu d''&eacute;lectrom&eacute;nager et de papeterie. Le magasin a &eacute;t&eacute; lanc&eacute; en janvier 2000 par le groupe Cora.</p>\r\n<p>&nbsp;</p>\r\n<p>Lors de la cr&eacute;ation de l''outil et dans l''ann&eacute;e qui a suivi, l''&eacute;quipe comptait pr&egrave;s d''une cinquantaine de personnes (informatique, commercial, compta ...). Maintenant que l''outil est d&eacute;velopp&eacute; un peu moins de trente. La pr&eacute;paration de commandes et la livraison comptent</p>\r\n<p>une centaine de personnes.</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Avec quel outil avez vous d&eacute;velopp&eacute; la premi&egrave;re version du site ?</strong></p>\r\n<p>&nbsp;</p>\r\n<p>Le site a d''abord &eacute;t&eacute; pens&eacute; par nos &eacute;quipes et cr&eacute;&eacute; par une web agency. Pendant plus d''un an nous avons utilis&eacute; Vignette Story Server.</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Vous utilisez maintenant la plateforme de d&eacute;veloppement PHP. Quand et pourquoi avez vous chang&eacute; ?</strong></p>\r\n<p>&nbsp;</p>\r\n<p>Vignette est un environnement lourd, avec de mauvaises performances et tr&egrave;s cher. Il y a maintenant trois ans et demi nous avons &eacute;tudi&eacute; la concurrence. A l''&eacute;poque la technologie qui sortait du rang &eacute;tait PHP.</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Pourquoi ? Quels sont les avantages de PHP ?</strong></p>\r\n<p>&nbsp;</p>\r\n<p>Apprendre &agrave; travailler avec la plateforme PHP est facile. De ce fait les d&eacute;veloppeurs que nous avons form&eacute;s &eacute;taient op&eacute;rationnels et complets rapidement.</p>\r\n<p>De plus il est performant et stable et dispose d''une communaut&eacute; tr&egrave;s active</p>\r\n<p>&nbsp;</p>\r\n<p>Dans notre utilisation quotidienne PHP permet d''utiliser plusieurs base de donn&eacute;es dans le m&ecirc;me script, de dialoguer avec SAP, d''&ecirc;tre utilis&eacute; dans une crontab, de surveiller les prix des sites concurrents ... et avec tout &ccedil;a, on n''a pas encore utilis&eacute; le quart des fonctions PHP.</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Combien de temps vous a t il fallu pour remplacer vignette par PHP ?</strong></p>\r\n<p>&nbsp;</p>\r\n<p>La V2 a &eacute;t&eacute; d&eacute;velopp&eacute;e en 5 mois par une &eacute;quipe de 7 personnes.</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Quels sont les prochains d&eacute;veloppement que vous planifiez ?</strong></p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>En ce moment, nous travaillons sur une application intranet de surveillance des prix. Les projets suivants seront la mise en ligne d''une nouvelle offre commerciale, la refonte de la home, l''optimisation du r&eacute;f&eacute;rencement et &eacute;ventuellement une page de promos personnalis&eacute;es.</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Quelle est la fr&eacute;quentation du site ?</strong></p>\r\n<p>&nbsp;</p>\r\n<p>Dans la grande distribution, on a coutume de garder les chiffres commerciaux secrets :) Parler de hit n''est pas tr&egrave;s pertinent mais nous en avons  entre 200 000 et 1.5M par jour.</p>\r\n<p>Un chiffre plus significatif concerne le nombre de sessions diff&eacute;rentes que nous avons chaque mois : pr&egrave;s de 400 000.</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Sur quelle architecture vous basez vous ? Qui en a fait le choix et comment ?</strong></p>\r\n<p>&nbsp;</p>\r\n<p>C''est du LAPO pour Linux Apache PHP et Oracle.</p>\r\n<p>Oracle avait &eacute;t&eacute; choisi d&egrave;s le d&eacute;part pour sa tenue des mont&eacute;es en charge, son support technique et le fait d''&ecirc;tre support&eacute; par toutes les applis du march&eacute;.</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Quelle est votre architecture mat&eacute;rielle ?</strong></p>\r\n<p>&nbsp;</p>\r\n<p>Pour le frontal, c''est 4 serveurs pour le site, 2 serveurs pour le moteur de recherche, 2 serveurs pour le paiement en ligne, 6 serveurs pour les images et photos, un quadriproc pour la base oracle principale. Plus 3 machines pour les diff&eacute;rents niveaux de d&eacute;veloppement (test / int&eacute;gration / pr&eacute;-prod).</p>\r\n<p>&nbsp;</p>\r\n<p>Pour le back office c''est une foultitude de machines : SAP, LM, Conso, l''envoi de mails ...</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p><strong>D&egrave;s vos d&eacute;buts vous avez fait le choix du PHP, a cette &eacute;poque c''&eacute;tait un choix qui aurait pu para&icirc;tre risqu&eacute; ?</strong></p>\r\n<p>&nbsp;</p>\r\n<p>Pour autant que je me souvienne, PHP &eacute;tait tr&egrave;s prometteur &agrave; l''&eacute;poque et depuis nous n''avons jamais &eacute;t&eacute; d&eacute;&ccedil;u ! Ce choix pouvait peut &ecirc;tre para&icirc;tre risqu&eacute; pour des gens qui ont l''habitude de payer tr&egrave;s cher pour des services et qui se m&eacute;fient du gratuit, mais l''&eacute;quipe qui a d&eacute;cid&eacute; de passer en PHP/Linux/Apache n''avait pas cette superstition.</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Votre syst&egrave;me d''information a &eacute;t&eacute; totalement migr&eacute; vers PHP ?</strong></p>\r\n<p>&nbsp;</p>\r\n<p>Pas pour le back office qui a des softs compl&egrave;ts qui seraient longs &agrave; remplacer (SAP, LM, Conso ...). Mais ces softs sont compl&eacute;t&eacute;es par des applis intranet.</p>\r\n<p>&nbsp;</p>\r\n<p>L''intranet compte une centaine d''applications, qui vont du reporting marketing &agrave; l''aide &agrave; la pr&eacute;paration de commande en passant par la publication des articles sur le site.</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Houra.fr a &eacute;t&eacute; l''un des sites les plus m&eacute;diatiques pendant la bulle internet. Comment l''avez vous v&eacute;cu et comment avez vous v&eacute;cu l''&eacute;clatement de cette bulle ? Est ce que votre mod&egrave;le &eacute;conomique a &eacute;volu&eacute; ?</strong></p>\r\n<p>&nbsp;</p>\r\n<p>Toutes les pr&eacute;tentions de l''&eacute;poque ont &eacute;t&eacute; revues &agrave; la baisse. Nous avons recadr&eacute; l''activit&eacute; en limitant la livraison aux d&eacute;partements rentables et en nous limitant aux produits sur lesquels nous avions une vraie valeur ajout&eacute;e. Nous avons &eacute;galement appliqu&eacute;e une politique de</p>\r\n<p>r&eacute;duction des co&ucirc;ts.</p>\r\n<p>&nbsp;</p>\r\n<p>Le fait d''appartenir &agrave; un gros groupe de "l''ancienne" &eacute;conomie (Cora) nous a permis de garder la t&ecirc;te sur les &eacute;paules, et donc l''&eacute;clatement de la bulle ne nous a pas &eacute;t&eacute; fatale comme aux deux tiers des boites internet de l''&eacute;poque.</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Et l''avenir pour <a href="http://www.houra.fr">houra.fr</a> ?</strong></p>\r\n<p>&nbsp;</p>\r\n<p>Maintenant que le site est stable et m&ucirc;r nous travaillons &agrave; la personnalisation du site. Id&eacute;alement nous visons le m&ecirc;me r&eacute;sultat qu''amazon, mais leur mod&egrave;le n''est pas directement applicable chez nous du simple fait qu''on n''ach&egrave;te pas des dvds comme des petits pois. Ca passera par des services comme aujourd''hui le pense-b&egrave;te qui s''appuie sur l''historique de commandes pour proposer des produits et qui a &eacute;t&eacute; tr&egrave;s bien accueilli par nos clients.</p>\r\n<p>&nbsp;</p>', 0, 1093903200, 1, 0),
(251, 58, 'Livre PHP 5 avancé', '29/09/2004 : Présentation technique de PHP5', '29-09-2004-pr-sentation-technique-de-php5', 'Le 29 Septembre à partir de 20h et à Paris l''AFUP organise une rencontre gratuite sur le thème de PHP5.\r\nLa conférence sera présentée par les auteurs du livre "PHP 5 avancé" édité par Eyrolles.', '<a href="http://www.eyrolles.com/Informatique/Livre/9782212116694/livre-php-5-avance.php">Le livre php 5 avancé</a> édité par Eyrolles est un ouvrage complet sur PHP 5 qui vous livre tous les éléments dont vous aurez besoin pour développer des applications avec PHP. Vous y trouverez des informations détaillées sur l''ensemble des technologies et ressources liées à PHP, de très bons chapitres sur les templates, les expressions régulières, la sécurité... un outil de travail que tout développeur devrait posséder.', 'L''Association Française des utilisateurs de PHP et l''un des auteurs du livre PHP 5 avancé <a href="http://www.eyrolles.com/Informatique/Livre/9782212116694/livre-php-5-avance.php">livre php5</a> vous invitent à découvrir les nouveautés de PHP5 d''un point de vue technique.\r\n\r\n<strong>Sujet :</strong> Présentation technique de PHP5\r\n\r\n<strong>Animateur :</strong> Eric Daspet & Cyril PIERRE de GEYER\r\n\r\n<strong>Date :</strong> Le Mercredi 29 Septembre 2004 à partir de 20 heures\r\n\r\n<strong>Durée :</strong> 2h30 maximum\r\n\r\n<strong>Tarif :</strong> Gratuit, accès prioritaire aux membres AFUP\r\n\r\n<strong>Places disponibles :</strong> 50\r\n\r\n<strong>Lieu :</strong> <a href="http://www.fiap.asso.fr/">Espace FIAP JEAN MONNET</a> \r\nSalle Londres\r\n30 rue Cabanis 75014 Paris\r\n\r\n<center><strong><h3>Inscription à la conférence (complet)</h3></strong></center>\r\n\r\n<center><a href="http://aperophp.cybergroupe.net/apero.php?id=107"><strong><h3>Inscription a l''apéro AFUP précédant  la conférence</h3></strong></a> </center>\r\n\r\nA l''occasion de cette rencontre AFUP vous aurez l''occasion de dialoguer entre praticiens sur les nouvelles possibilités offertes par la version 5 de PHP. \r\n\r\n\r\nIngénieur consultant dans les NTIC chez <a href="http://www.aston.fr">Aston</a>, Eric DASPET a développé une expertise autour de PHP en s''y impliquant depuis 1996.\r\nIl est co auteur du livre <a href="http://formation.anaska.fr/livre-php-5-avance.php">"PHP 5 avancé"</a> publié aux éditions Eyrolles et publie régulièrement des articles sur la conception Web.\r\nIl s''est fait une spécialité des standards du Web.\r\n\r\nCyril PIERRE de GEYER est responsable du pôle <a href="http://formation.anaska.fr/formation-php.php">formation PHP</a> pour Anaska.\r\nIl est co auteur du livre <a href="http://formation.anaska.fr/livre-php-5-avance.php">"PHP 5 avancé"</a> publié aux éditions Eyrolles.', 0, 1088632800, 1, NULL),
(253, 64, 'Forum PHP 2004', 'Forum PHP 2004 - 18 et 19 novembre - 84 rue de Grenelle, Paris 7', 'forum-php-2004-18-et-19-novembre-84-rue-de-grenelle-paris-7', 'L''AFUP organise le 4ème forum PHP les 18 et 19 novembre prochain.\r\nLe plus grand rassemblement annuel de la communauté française PHP aura lieu 84 rue de Grenelle dans le 7ème arrondissement de Paris.', 'L''Association Française des Utilisateurs de PHP a été créée à la suite du premier forum PHP. Depuis elle est devenue la principale source d''informations aux professionnels du Net et de PHP en particulier. \r\nPour pouvoir se donner les moyens de faire progresser la cause de PHP elle a besoin d''une implication et d''une aide de tous les instants de la communauté et des professionnels de PHP. \r\nParticipez en vous inscrivant comme <a href="https://afup.org/article.php3?id_article=70">membre de l''AFUP.</a>', 'L''AFUP organise sa 4ème conférence PHP annuelle autour des axes PHP5 et l''interopérabilité. \r\n<h3><strong>18 et 19 novembre - 84 rue de Grenelle, Paris 7</strong></h3>\r\n\r\n<a href="https://afup.org/forumphp2004/index.php"><center><img src="https://afup.org/forumphp2004/img/logo_forumphp.gif" border="0"></center></a>\r\n\r\nSur deux jours, l''élite PHP tant nationale qu''internationale présentera des sessions qui vous permettront d''avoir les avis des experts du domaine. De <a href="https://afup.org/forumphp2004/conferenciers.php#rl">Rasmus Lerdorf</a> à <a href="https://afup.org/forumphp2004/conferenciers.php#zs">Zeev Suraski</a> en passant par <a href="https://afup.org/forumphp2004/conferenciers.php#dr">Derick Rethans</a> en collaboration avec des experts français, ces conférenciers vous apporteront les dernières informations sur PHP5 et l''interopérabilité de PHP avec les autres technologies du marché. \r\n\r\nEn abordant à la fois des aspects techniques (XML, Oracle, Dot Net, Services web, GTK, Refactoring ...) mais aussi stratégique (Retour sur investissement, gain de compétitivité) vous bénéficierez au cours de ces deux jours de formation de la crème des intervenants qui sont là exclusivement pour vous, vous faire partagez leurs connaissances et répondre à vos questions. \r\n\r\n<a href="https://afup.org/forumphp2004/inscription.php">Inscrivez vous</a> dès maintenant et venez participer au plus grand rassemblement annuel de la communauté française PHP.\r\n\r\n<ul><li><a href="https://afup.org/forumphp2004/index.php">Le forum PHP 2004</a></li><li><a href="https://afup.org/forumphp2004/sessions.php">Les sessions</a></li><li><a href="https://afup.org/forumphp2004/inscription.php">Vous inscrire</a></li></ul>', 0, 1097791200, 1, NULL),
(254, 58, 'Livre PHP 5 avancé', 'Resumé de la présentation technique de PHP5', 'resum-de-la-pr-sentation-technique-de-php5', 'Voici un résumé de la <a href="http://www.eyrolles.com/Informatique/Livre/9782212113235/livre-php-5-avance.php">présentation technique de PHP5->art251] du 29 septembre 2004 proposée par Eric Daspet, co-auteur du livre "[PHP5 Avancé</a>".\r\n', 'Le livre php 5 avancé édité par Eyrolles est un ouvrage complet sur PHP 5 qui vous livre tous les éléments dont vous aurez besoin pour développer des applications avec PHP. Vous y trouverez des informations détaillées sur l''ensemble des technologies et ressources liées à PHP, de très bons chapitres sur les templates, les expressions régulières, la sécurité... un outil de travail que tout développeur devrait posséder.', '<h3>Introduction</h3>\r\n\r\nAvec la sortie de PHP5, les principaux manques reprochés à PHP4 ont été comblés : \r\n\r\n-* <strong>La programmation orientée objet</strong> à été revue complétée.\r\n-* <strong>La gestion des exceptions</strong> et <strong>les contrôles de type</strong> viennent en renfort à la sûreté de programmation.\r\n-* <strong>La gestion des flux XML</strong> est plus homogène, plus simple à mettre en oeuvre.\r\n-* <strong>Un SGBDR embarqué, SQLite</strong>, fait son apparition, ainsi que des interfaces objet pour les autres SGBD.\r\n-* <strong>La collaboration entre Java et PHP</strong> est plus complète, plus stable, grâce à l''apparition d''interfaces fiables.\r\n\r\nCes quelques évolutions montrent le sérieux de la nouvelle version de PHP. Afin de s''en convaincre, examinons ces changements de plus près !\r\n\r\n<h3>La programmation orientée objet</h3>\r\n\r\n<ul>\n<li><strong>Passage par référence</strong>\r</li>\n</ul>\n\r\nContrairement aux valeurs scalaires, dans PHP5 les objets sont toujours passés par <a href="http://fr2.php.net/manual/fr/language.references.php">référence</a>. L''obtention d''une copie doit faire l''objet d''un <a href="http://fr2.php.net/manual/fr/language.oop5.cloning.php">clonage</a>. \r\n\r\nLes objets peuvent maintenant posséder des <a href="http://fr2.php.net/manual/fr/language.oop5.constants.php">constantes de classes</a> et des <a href="http://fr2.php.net/manual/fr/language.oop5.static.php">attributs statiques</a>. \r\n\r\n<ul>\n<li><strong>Sûreté de programmation</strong>\r</li>\n</ul>\n\r\nIl est maintenant possible de déclarer la <a href="http://fr2.php.net/manual/fr/language.oop5.visibility.php">visibilité des attributs et des méthodes</a> de classe  : <em>public</em>, <em>protected</em> ou <em>private</em>. Bien entendu, un contrôle d''accès est associé à chaque déclaration. \r\n\r\nLa sûreté de programmation est encore accrue avec la possibilité d''effectuer des contrôles de type, de définir des <a href="http://fr2.php.net/manual/fr/language.oop5.interfaces.php">interfaces</a>, de déclarer des <a href="http://fr2.php.net/manual/fr/language.oop5.abstract.php">classes et méthodes abstraites</a> et d''<a href="http://fr2.php.net/manual/fr/language.oop5.final.php">interdire la redéfinition des attributs et méthodes</a>. \r\n\r\n<ul>\n<li><strong>Surcharges</strong>\r</li>\n</ul>\n\r\nLes principes des <a href="http://fr2.php.net/manual/fr/language.oop5.overloading.php">surcharges</a> diffèrent de ceux que l''on connaît en Java / C++. En PHP5, une méthode peut disposer d''<a href="http://fr2.php.net/manual/fr/functions.arguments.php#functions.arguments.default">attributs facultatifs</a>. L''appel d''un attribut ou d''un prototype de méthode inexistant peut être <a href="http://fr2.php.net/manual/fr/language.oop5.overloading.php">intercepté</a> afin d''être traité. Les méthodes peuvent également être définies dynamiquement au même titre que les fonctions. \r\n\r\n<ul>\n<li><strong>Itérateurs</strong>\r</li>\n</ul>\n\r\nIl est possible d''itérer sur des objets représentant une collection. <a href="http://fr2.php.net/manual/fr/language.oop5.iterations.php">L''interface Iterator</a> permet de créer de tels objets. \r\n\r\nIl existe également une interface <a href="http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html">ArrayAccess</a> permettant de gérer les accès aux données d''une classe de la même manière qu''avec un tableau. \r\n\r\n<ul>\n<li><strong>Quelques autres évolutions sur les objets</strong>\r</li>\n</ul>\n\r\n-* Le chargement automatique de classes via la méthode <a href="http://www.php.net/~helly/php/ext/spl/autoload_8inc.html#a1">__autoload</a>.\r\n-* La déclaration et le comportement des <a href="http://fr2.php.net/manual/fr/language.oop5.decon.php">constructeurs / destructeurs</a>.\r\n-* Les nouvelles possibilités d''<a href="http://fr2.php.net/manual/fr/language.oop5.reflection.php">introspection</a>.\r\n-* Et plein d''autres évolutions à découvrir : référencement de méthodes, déclarations avant utilisation, vérifications d''appartenances, méthodes prédéfinies, ...\r\n\r\n<h3>Erreurs et Exceptions</h3>\r\n\r\n<ul>\n<li><strong>Les exceptions en PHP5</strong>\r</li>\n</ul>\n\r\nLa <a href="http://www.nexen.net/docs/php/annotee/language.oop5.exceptions.php">gestion des exceptions</a> en PHP5 est similaire à la gestion des exceptions en Java : \r\n\r\n-* Le lancement d''une exception dans un bloc "try" fait appel au bloc "catch" correspondant.\r\n-* Il est possible de créer / personnaliser des exceptions. \r\n\r\n<ul>\n<li><strong>Quelques particularités...</strong>\r</li>\n</ul>\n\r\nEn PHP5 (jusqu''à la version 5.0.2 au moins), toutes les erreurs sont critiques. De plus, seules les nouvelles extensions objets retournent des exceptions, en remplacement des codes d''erreurs habituels. \r\n\r\n<h3>Les traitements XML</h3>\r\n\r\n<ul>\n<li><strong>SimpleXML</strong>\r</li>\n</ul>\n\r\nLe principe de <a href="http://fr2.php.net/manual/fr/ref.simplexml.php">SimpleXML</a> est, comme son nom l''indique, simple : un flux XML est transformé en un objet très facile à manipuler. Ceci est possible, pour l''instant, pour des fichiers XML peu complexes. \r\n\r\n<ul>\n<li><strong>Interface DOM</strong>\r</li>\n</ul>\n\r\nL''extension <a href="http://fr2.php.net/manual/fr/ref.dom.php">DOM</a> déjà présente dans PHP4 à été complètement refaite. Elle est standard, stable et profite des nouvelles possibilités de PHP5. \r\n\r\n<ul>\n<li><strong>Que choisir ?</strong>\r</li>\n</ul>\n\r\nDOM et SimpleXML sont compatibles ! On peut <a href="http://fr2.php.net/manual/fr/function.dom-import-simplexml.php">passer de l''un à l''autre</a> sans aucun coût, car ils utilisent le même backend.\r\n\r\n<ul>\n<li><strong>XSLT</strong>\r</li>\n</ul>\n\r\nLes traitements XSLT sont gérés par DOM dans PHP5, en syntaxe objet, avec de nouvelles possibilités à découvrir. \r\n\r\n<ul>\n<li><strong>Services Web</strong>\r</li>\n</ul>\n\r\nDéclarer et utiliser un client ou un serveur <a href="http://fr2.php.net/manual/fr/ref.soap.php">SOAP</a> en PHP5 est devenu d''une simplicité quasi enfantine. \r\n\r\n<h3>Bases de données</h3>\r\n\r\n<ul>\n<li><strong>MySQL, Oracle, ...</strong>\r</li>\n</ul>\n\r\nLe support <a href="http://fr2.php.net/manual/fr/ref.mysql.php">MySQL</a> n''est plus inclus par défaut. Les extensions des SGBD peuvent être manipulées en objet et procédurale. Il est aussi possible de définir des <a href="http://fr2.php.net/manual/fr/function.mysqli-prepare.php">requêtes paramétrées</a>. \r\n\r\n<ul>\n<li><strong>SQLite</strong>\r</li>\n</ul>\n\r\n<a href="http://fr2.php.net/manual/fr/ref.sqlite.php">SQLite</a> est inclus par défaut dans PHP. Ses avantages sont multiples : utilisation de bases embarquées et multiples fonctionnalités qui en font un SGBD très intéressant. \r\n\r\n<ul>\n<li><strong>Prochainement : l''abstraction</strong>\r</li>\n</ul>\n\r\nDes projets, tel que "<a href="http://pecl.php.net/package/PDO">PDO</a>", proposeront une abstraction rapide et fiable permettant de séparer les requêtes SQL du SGBD utilisé. \r\n\r\n<h3>PHP5 aujourd''hui</h3>\r\n\r\nSi vous pouvez choisir entre PHP4 et PHP5, il est fortement recommandé de choisir PHP5, compte tenu de ses évolutions prometteuses. \r\n\r\nPHP5 est plus performant que PHP4 (grâce notamment au nouveau moteur <a href="http://www.zend.com/php5/zend-engine2.php">Zend Engine 2</a>). La compatibilité entre PHP4 et PHP5 n''est pas tout à fait assurée. Il est possible de paramétrer PHP5 pour une compatibilité totale, mais cela reste déconseillé. \r\n\r\nL''activation d''erreurs de type <a href="http://fr2.php.net/manual/fr/ref.errorfunc.php#e-strict">E_STRICT</a>, agissant sur les nouvelles fonctionnalités PHP5 (notamment les objets), est en revanche recommandée pour assurer des développements plus fiables. ', 0, 1097050104, 1, NULL),
(258, 9, '', 'La plate-forme LAMP brille de mille feux !', 'la-plate-forme-lamp-brille-de-mille-feux', '"Friendster (le leader des services de "social networking", plus de 5 millions d''inscrits...) vient d''abandonner Java, jugé trop lent par les responsables techniques du site, pour passer à PHP.', '', 'Le journal du net nous retransmet une interview d''Alain Lefebvre concernant ce "transfert" spectaculaire et significatif qui permet de confirmer ce que l''on sait déjà : l''environnement LAMP alliant le système Linux (L), le serveur Apache (A), le SGBDR MySQL (M) et le langage PHP (P) est bien la plate-forme standard, la plate-forme de référence de ces prochaines années. Et LAMP ne regroupe que des projets Open Source, comme par hasard..."\r\n\r\n<a href="http://solutions.journaldunet.com/0412/041203_tribune.shtml">L''interview sur le journal du net</a>', 0, 1102062009, 1, NULL),
(260, 9, 'Nexen', 'Pres de 15% des migrations vers PHP5 viennent de .Net', 'pres-de-15-des-migrations-vers-php5-viennent-de-net', 'Selon une étude de Nexen publié en décembre près de 15 % des serveurs ayant migré vers PHP5 sont des plateformes .net. Cette tendance montre que la plateforme PHP5 séduit jusqu''aux utilisateurs de .Net', 'Le portail Nexen est l''un des pionniers dans le genre. En France c''est la principale source d''information régulière sur les actualités techniques.', 'Sur un panel de 23 millions de sites nexen analyse les évolutions du taux de pénétration de PHP.\r\nLes statistiques montrent que PHP4.3 reste la version la plus utilisée.\r\nEn France 41% des serveurs web utilisent PHP ce qui confirme la prédominance de cette plateforme.\r\n\r\nVous pouvez retrouver les statistiques détaillées sur <a href="http://www.nexen.net/interview/index.php?id=44">Nexen</a>\r\n', 0, 1102529503, 1, NULL),
(261, 9, 'PHPtunisie', 'Gérer son serveur vocal avec PHP et VoiceXML', 'g-rer-son-serveur-vocal-avec-php-et-voicexml', 'Le VoiceXML est un langage descriptif (dérivé du XML) conçu pour créer et gérer des dialogues audio. PHP permet d''interagir avec votre serveur vocal et ainsi de piloter toute son activité. \r\nC''est une application intéressante qui met en avant la capacité de PHP à offrir de nombreuses applications différentes des applications web pour lequel il est particulièrement réputé.', 'PHP Tunisie est la communauté des utilisateurs de PHP en Tunisie. Créé courant 2004 ils sont particulièrement actif dans la communauté OpenSource.', 'Le VoiceXML est un langage descriptif conçu pour créer des dialogues audio :\r\n<ul>\n<li>discours synthétisé,\r</li>\n<li>de l''acoustique digitalisée, \r</li>\n<li>l''identification de l''entrée principale parlé \r</li>\n<li>l''enregistrement de l''entrée parlée, \r</li>\n<li>la téléphonie, \r</li>\n<li>les conversations mixed initiative.\r</li>\n</ul>\n\r\nSon but principal est d''apporter les avantages de la livraison de contenu interactif via des applications web-based en utilisant la voix.\r\n\r\nPHPTunise au travers de son magasine nous offre un dossier complet sur le sujet.\r\nVous pouvez le télécharger à l''adresse suivante :\r\n<a href="http://www.phptunisie.net">PHPTunisie</a>\r\n\r\n\r\n', 0, 1103058576, 1, NULL),
(262, 9, '', 'Dossier PHP dans Programmez de Janvier 2005', 'dossier-php-dans-programmez-de-janvier-2005', 'Le numéro de Janvier du magazine programmez fait la part belle a PHP et au forum PHP organisé par l''AFUP.', '', 'Près de cinq pages sur le Forum PHP avec de nombreux chiffres issus du livre blanc "PHP en entreprise".\r\n\r\nDivers intervenants dont Jérôme LAVANCIER de SQLI, Zeev SURASKI , José DIZ et Rasmus LERDORF nous donnent leur avis sur le fer de lance de l''OpenSource qu''est PHP.\r\n\r\nEn première page du dossier il y a une grande photo d''une partie des cents membres de l''AFUP.\r\n\r\nEnfin, dans la partie technique un autre membre de l''association , gerald Croes de la société Aston, anime un TP sur la réalisation d''une FAQ avec PHP5.', 0, 1104942973, 1, NULL),
(263, 9, '', 'PHP, langage de l''année 2004', 'php-langage-de-l-ann-e-2004', '"PHP a reçu le titre de "Langage de programmation de l''année 2004" avec une évolution poisitive de plus de 3% durant l''année. Le lancement de PHP 5 est généralement reconnu comme un signe de maturité. On s''attend à ce que PHP conserve sa place de 4eme pour un long moment. "', '', 'L''index de TIOBE Programming Community est une indication de la popularité des langages de programmation. Les évaluations sont faites une fois par mois, et sont basées sur la disponibilité mondiale de techniciens expérimentés, de formations et d''outils tiers. Les moteurs de recherche Google, MSN, et Yahoo! sont utilisés.\r\n\r\n<a href="http://www.tiobe.com/tpci.htm">TIOBE SoftWare</a>', 0, 1105039493, 1, NULL),
(264, 62, 'AFUP', 'Livre Blanc "PHP en entreprise"', 'livre-blanc-php-en-entreprise', 'L''Association Française des Utilisateurs de PHP (www.afup.org) publie la quatrième édition de son livre blanc « PHP en entreprise ». Rédigé par des experts de PHP, ce document fournit aux entreprises une information synthétique sur PHP 5 et son écosystème. ', 'L''AFUP est une association à but non lucratif qui regroupe les utilisateurs professionnels (entreprises, prestataires, éditeurs, etc.) de PHP en France. \r\n\r\nSon objectif est d''apporter une information objective sur cette plate-forme - basée sur des retours d''expérience concrets d''entreprise - afin d''aider les entreprises à choisir ou non cette technologie. \r\n\r\nL''afup organise également des rencontres régulières (Forum PHP notamment) sur des sujets afférents à PHP', 'Ce livre blanc s''adresse aux développeurs, chefs de projets, décideurs et architectes qui souhaitent répondre aux questions suivantes :\r\n<ul>\n<li>la plate-forme PHP rivalise-t-elle avec .NET et J2EE ?\r</li>\n<li>Quelle est son architecture technique ?\r</li>\n<li>Combien d''entreprises l''utilisent-elle?\r</li>\n<li>Peut-on développer des services web et des applications client serveur avec PHP ?\r</li>\n<li>Est-il possible d''interfacer SAP et Lotus Notes avec PHP ?\r</li>\n<li>Quels sont les projets critiques qui recourent à cette technologie ?\r</li>\n<li>etc.\r</li>\n</ul>\n\r\nChiffres clés (25 études Forrester, Gartner, etc. compilées), schémas techniques (2), captures d''écrans (8), exemples de code (6), témoignages d''entreprises (15) : tous les éléments sont réunis pour faire de ce livre blanc un véritable outil de travail.\r\n\r\nSommaire :\r\n<ul>\n<li>Fiche d''identité de PHP\r</li>\n<li>PHP en chiffres\r</li>\n<li>Les atouts de PHP pour l''entreprise\r</li>\n<li>Architecture technique\r</li>\n<li>Une plate-forme qui s''ouvre aux problématiques d''intégration.\r</li>\n<li>PHP, J2EE et .NET : plus complémentaires que concurrents\r</li>\n<li>L''écosystème PHP\r</li>\n<li>Ce qu''en pensent les entreprises\r</li>\n</ul>\n\r\n\r\n<strong><a href="https://afup.org/docs/livre-blanc-php-en-entreprise-v4.pdf"><img14|center></a></strong>\r\n\r\n\r\n\r\n', 0, 1130364000, 1, NULL),
(265, 9, 'Solutions Linux', 'L''AFUP au salon Linux 2005', 'l-afup-au-salon-linux-2005', 'L''association Française des Utilisateurs de PHP est heureuse de participer pour la première fois à la principale manifestation française sur les technologies OpenSources.\r\n', '« Solutions Linux 2005 », La référence européenne incontournable dédiée aux solutions GNU/Linux, Open Source et Logiciels Libres pour toutes les entreprises (grands comptes et PME/PMI), les services publics et les administrations, ouvrira ses portes début février au CNIT, Paris La Défense.', 'C''est dans le village associatif que nous aurons le plaisir de vous retrouver pour répondre aux questions suivantes :\r\n\r\n<ul>\r\n<li>la plate-forme PHP rivalise-t-elle avec .NET et J2EE ?\r\n</li>\r\n<li>Quelle est son architecture technique ?\r\n</li>\r\n<li>Combien d''entreprises l''utilisent-elle ?\r\n</li>\r\n<li>Peut-on développer des services web et des applications client serveur avec PHP ?\r\n</li>\r\n<li>Est-il possible d''interfacer SAP et Lotus Notes avec PHP ?\r\n</li>\r\n<li>Quels sont les projets critiques qui recourent à cette technologie ?\r\n</li>\r\n<li>etc. \r\n</li>\r\n</ul>\r\n\r\nVenez nombreux !\r\n<a href="http://www.solutionslinux.fr/fr/index.php">Url du salon Linux</a> ', 0, 1106262000, 1, 0),
(266, 4, '', 'Définition du bénévole', 'definition-du-benevole', 'L''activus benevolus est un mammifère bipède qu''on rencontre surtout dans les associations où il peut se réunir avec ses congénères ; ', '', 'les bénévoles se rassemblent à un signal mystérieux appelé «convocation». On les rencontre aussi en petits groupes, dans divers endroits, quelque fois tard le soir, l''oeil hagard, le cheveu en bataille et le teint blafard, discutant ferme sur la meilleure façon d''animer une manifestation ou de faire des recettes supplémentaires pour boucler son budget.\r\n\r\nLe téléphone est un appareil qui est beaucoup utilisé par le bénévole et qui lui prend beaucoup de son temps, mais cet instrument lui permet de régler les petits problèmes qui se posent au jour le jour.\r\n\r\nL''ennemi héréditaire du bénévole est le « Yaqua » (non populaire) dont les origines n''ont pu être à ce jour déterminées. Le « Yaka » est aussi un mammifère bipède, mais il se caractérise par un cerveau très petit, qui ne lui permet de connaître que deux mots, « y''a qu''à », d''où son nom.\r\n\r\nLe « Yaqua », bien abrité dans la cité anonyme, attend. Il attend le moment où le bénévole fera une erreur ou un oubli ; c''est alors qu''il bondit pour lancer son venin. S''il l''atteint, celui-ci peut provoquer chez son adversaire une maladie très grave, le « découragement ».\r\n\r\nLes premiers symptômes de cette implacable maladie sont rapidement visibles : absences de plus en plus fréquentes aux réunions, intérêt croissant pour son jardin, sourire attendri devant une canne à pêche et attrait de plus en plus vif qu''exercent un bon fauteuil et la télévision sur le sujet atteint.\r\n\r\nLes bénévoles, décimés par le découragement, risquent de disparaître. C''est pourquoi ils ont été placés sur la liste des animaux en voie de disparition. Il n''est pas impossible que, dans quelques années, on rencontre cette espèce uniquement dans les zoos où, comme tous ces malheureux animaux enfermés, ils n''arriveront plus à se reproduire.\r\n\r\nLes « Yaquas », avec leurs petits cerveaux et leurs grandes langues, viendront leur lancer des cacahuètes pour tromper l''ennui ; ils se rappelleront avec nostalgie du passé pas si lointain où ils pouvaient traquer le bénévole sans contrainte.', 0, 1011826800, 1, 0),
(268, 4, '', 'Bilan du bureau de l''association pour l''exercice 12/2003 au 02/2005', 'bilan-du-bureau-de-l-association-pour-l-exercice-12-2003-au-02-2005', 'Bilan 2004 du bureau composé par Cyril PIERRE de GEYER, Olivier LECORRE, Sébastien HORDEAUX et Damien SEGUY respectivement président, trésorier, secrétaire et vice-président.\r\n\r\nLe bilan est globalement bon : le nombre d''adhérents a plus que doublé tout en se concentrant sur les pros, le forum 2004 a été un succès et l''implication des membres s''est améliorée.', '', 'Les groupes de travail se sont mis en place et ont produits de bons résultats. La coordination des membres du bureau a été bonne permettant ainsi un bon niveau de production (et ce malgré des emplois du temps chargés).\r\n\r\nLe travail en matière de communication de l''AFUP a produits des résultats intéressants permettant de se rapprocher de notre objectif d''être la voie officielle de PHP en France.\r\n\r\nDes nombreux outils ont étés développés pour simplifier la gestion de tous les jours de l''afup : Gestion des membres, Gestion de l''annuaire, Gestion des rencontres AFUP. \r\n\r\n\r\n\r\n<strong>Organisation du forum 2004</strong> \r\n\r\nLa préparation du Forum PHP a nécessité beaucoup de temps et d''énergie mais la grande réussite de l''événement a récompensé ce travail.\r\nLe paiement en ligne a enfin pu être mis en place.\r\nLe succès du forum a permit au bureau de faire réaliser des goodies pour les visiteurs et les membres du Forum : Chemises PHP/AFUP, Portes cartes PHP, livres blancs.\r\n\r\n\r\n<ul>\r\n<li>Nombre de visiteurs : Plus de 200.\r\n</li>\r\n<li>Bilan financier : Positif.\r\n</li>\r\n<li>Bilan presse : Positif.\r\n</li>\r\n<li>Bilan visiteurs : Positif.\r\n</li>\r\n</ul>\r\n\r\nRemarques : \r\n<ul>\r\n<li>S''y prendre tôt pour la location de la salle (6-8 mois avant).\r\n</li>\r\n<li>Réimprimer des livres blancs (changer d''imprimeur l''année prochaine.)\r\n</li>\r\n</ul>\r\n\r\n<strong>Certifications PHP</strong>\r\n\r\nLe travail sur les certifications a été avancé mais finalement abandonné faute de temps et du fait de la sortie de la certification de la société Zend. \r\n\r\nBilan : Echec du groupe de travail.\r\n\r\nRaisons : \r\n<ul>\r\n<li>Manque de temps,\r\n</li>\r\n<li>Sortie de la certification Zend.\r\n</li>\r\n</ul>\r\n\r\nRemarque :\r\n<ul>\r\n<li>Plutôt que de plancher sur une certification complète nous pourrions envisager de définir des niveaux d''expertise. \r\n</li>\r\n</ul>\r\n\r\n<strong>Remise à plat du site Internet de l''AFUP</strong>\r\n\r\nBilan : \r\n<ul>\r\n<li>Le site a été remis à jour et dispose d''une interface plus claire.\r\n</li>\r\n<li>De nombreux outils ont étés développés.\r\n</li>\r\n</ul>\r\n\r\nRemarque		: \r\n<ul>\r\n<li>Un nouveau travail sur les catégorisation du site serait bien pour améliorer la visibilité. \r\n</li>\r\n<li>Des ajouts de services pourraient être intéressants (espace job, actualités rss,...)\r\n</li>\r\n</ul>\r\n\r\n<strong>Refonte de l''annuaire</strong> \r\n\r\nBilan : \r\n<ul>\r\n<li>L''annuaire est fonctionnel est agréable d''utilisation.\r\n</li>\r\n</ul>\r\n\r\nRemarques :\r\n<ul>\r\n<li>Envisager un affichage différent comprenant notamment le type d''entreprise.\r\n</li>\r\n</ul>\r\n\r\n<strong>Livre Blanc</strong>\r\n\r\nBilan :\r\n<ul>\r\n<li>Le livre blanc n''est pas assez connu par les professionnels.\r\n</li>\r\n<li>Cette initiative est saluée par les utilisateurs PHP à travers le monde.\r\n</li>\r\n</ul>\r\n\r\nRemarque : \r\n<ul>\r\n<li>Optimiser la communication sur ce support, réimprimer une version propre.\r\n</li>\r\n</ul>\r\n\r\n<strong>Poursuite des retours d''expérience</strong>\r\n\r\nBilan : \r\n<ul>\r\n<li>Le rythme des retours d''expériences s''est ralenti faute de bras.\r\n</li>\r\n</ul>\r\n\r\nRemarque : \r\n<ul>\r\n<li>Travailler plus cet aspect au travers des clients des membres afup pro.\r\n</li>\r\n</ul>\r\n\r\n\r\n<strong>Revue de presse autour de PHP</strong>\r\n\r\nBilan : \r\n<ul>\r\n<li>Retours présents sur le site mais pas suffisamment faute encore une fois de bras.\r\n</li>\r\n</ul>\r\n\r\n\r\n<strong>Rencontres AFUP</strong>\r\n\r\nL''organisation de rencontres AFUP et d''apéros PHP ont commencés et donnés de bons résultats. Deux rencontres majeures sur l''"extreme programming" et sur "PHP 5" ont permit de roder l''organisation (développement d''un outil de gestion). Le coût de ces rencontres est faible en passant par des associations telles que la FIAP qui permet de louer une salle pour 50 personnes le soir à un faible coût.\r\n\r\n\r\nBilan :\r\nPositif, les visiteurs étaient contents.\r\n\r\nRemarque : \r\n<ul>\r\n<li>Les inscriptions étant gratuites de nombreuses personnes s''inscrivent et ne viennent pas. Un phoning téléphonique trois jours avant la rencontre permet de limiter fortement le taux d''absence.\r\n</li>\r\n</ul>\r\n\r\n', 0, 1107212400, 1, 0),
(269, 9, '', '[01 informatique] 28/01/05 : Le Crédit Agricole sort PHP du guetto web. ', '01-informatique-28-01-05-le-cr-dit-agricole-sort-php-du-guetto-web', 'Sont listés les outils utilisés par  l''équipe de Batica dans la mise en place de la plateforme de Transfact (filiale d''affacturation du Crédit Agricole) : développement, gestion de code, suivi de bogues, tests, documentation, administration.', '', '\r\nL''équipe de Batica constituée de ... 2 personnes listes les difficultés lors de la mise en place de la plateforme, et préconise les mêmes méthodes que celles des mondes J2EE et .Net. \r\n\r\nCette application est utilisée par plusieurs milliers d''utilisateurs, gère 15 Go de données, 150 connexions simultanées.\r\n\r\nLa plate-forme PHP montre ici sa force même dans le cas d''applications critiques.', 0, 1107193214, 1, NULL),
(270, 9, '', '[01 réseau] 01/2005 : PHP 5 ne se limite plus aux sites web', '01-r-seau-01-2005-php-5-ne-se-limite-plus-aux-sites-web', 'La dernière édition du Forum PHP, qui s''est tenue en novembre à Paris, a été l''occasion pour ses créateurs de définir les nouvelles frontières du langage de script, aujourd''hui disponible en version 5. Désormais orienté objets, PHP a aussi été présenté comme une plate-forme d''intégration.\r\n', '', 'La version 5 de PHP marque une étape importante dans son développement. La plate-forme, qui n''était considérée jusqu''à peu que comme un simple langage de script, vient de connaître une véritable mue. Constituant un simple ajout à PHP 4, le modèle objet est généralisé sous PHP 5,ses supporteurs pouvant annoncer fièrement que leur langage fétiche est à présent un véritable langage orienté objets. \r\n\r\n...\r\n\r\n<a href="http://www.01net.com/article/264921.html">L''article complet</a>', 0, 1107381910, 1, NULL),
(271, 19, '', 'Club Internet : "Notre couche applicative Java et PHP repose sur une base de données Oracle"', 'club-internet-notre-couche-applicative-java-et-php-repose-sur-une-base-de-donn-es-oracle', '<p>Le directeur technique l&egrave;ve le voile sur l''architecture de portail du fournisseur d''acc&egrave;s. Une plate-forme qui fait la part belle aux technologies Open Source.</p>', '<p>Pierre de Rome est Directeur des op&eacute;rations de Club Internet. Il &eacute;tait pr&eacute;c&eacute;demment chez Kertel (filiale du Groupe Iliad, op&eacute;rateur de cartes t&eacute;l&eacute;phoniques pr&eacute;pay&eacute;es pour le grand public).</p>', '<p>Le journal du net nous propose une interview du dir&eacute;cteur des op&eacute;rations de Club Internet.  On y apprend que Club Internet utilise PHP et Java pour son architecture applicative.  <a href="http://solutions.journaldunet.com/itws/050215_it_clubinternet.shtml">L''article</a></p>', 0, 1108422000, 1, 0),
(276, 9, '', 'Cityvox économise grace à PHP', 'cityvox-conomise-grace-php', 'CityVox a migré ses sites de Vignette-Oracle vers une plate-forme PHP-PostgreSQL : Une économie de 50.000 € par an.', '', 'Lors de sa création en 1999, CityVox choisit une plate-forme propriétaire composée de Vignette 5.5 et de la base de données d''Oracle. L''ensemble est hébergé sur des serveurs Sun (sous Solaris).\r\n\r\nMais cette configuration doit être remise en question quelques années plus tard à l''occasion d''une opération de croissance externe. «Suite au rachat des sites WebCity en avril 2003, nous avons commencé à rencontrer de graves problèmes de performances liés à Vignette», explique Bertrand Bigay, P-DG de Cityvox. \r\n\r\n<a href="http://www.zdnet.fr/techupdate/infrastructure/0,39020938,39218831,00.htm">La suite sur ZdNet.</a>', 0, 1114415941, 1, NULL),
(273, 9, '', 'IBM rejoint la communauté PHP', 'ibm-rejoint-la-communaut-php', 'Les annonces se succèdent et après SAP et Intel c''est IBM qui annonce rejoindre la communauté PHP. \r\n\r\nIBM souhaite intégrer PHP dans son offre pour attirer les PME en leur permettant de créer des applications plus facilement qu''avec Java. ', '', 'Les premiers travaux de Big Blue portent sur l''amélioration de la couche services web de PHP 5 et sur l''accès aux données: implémentation SDO (Software Delivery Option) et pilotes pour les bases de données Cloudscape et DB2. Ces améliorations seront réintégrées dans les prochaines versions de PHP 5.\r\n\r\nEn parallèle, la compagnie a travaillé avec Zend Technologies au développement de "Zend Core for IBM". Il s''agit d''une distribution de PHP 5 spécialement optimisée pour les serveurs iSeries (Linux et AIX). Elle se déploie en quelques minutes et intègre les pilotes pour DB2 et Cloudscape. Gratuite et disponible en juin 2005, "Zend Core for IBM" bénéficiera d''un programme de support technique (payant) assuré par Zend.\r\n\r\n<a href="http://www.zdnet.fr/actualites/informatique/0,39040745,39208712,00.htm">L''article complet</a>\r\n\r\n<a href="http://www.01net.com/article/269997.html">Article sur 01 informatique</a>', 0, 1109409882, 1, NULL),
(277, 9, ' SIL-CETRIL', 'Trophées du libre', 'troph-es-du-libre', 'L''Association SIL-CETRIL est à l''origine du premier concours du logiciel libre et réuni pour sa deuxième édition quelques-uns des plus grands acteurs mondiaux, comme <a href="http://www.hp.com">HP</a>, <a href="http://www.mysql.com">MySQL AB</a>, <a href="http://www.mandriva.com">Mandriva</a>, <a href="http://www.objectweb.org">ObjectWeb</a>, <a href="http://www.alcove.com">Alcôve</a>, <a href="http://formation.anaska.fr">Anaska conseil et formation</a>, <a href="http://www.clever-age.com">Clever Age</a>, <a href="http://www.idealx.com">Idealx</a>, ou encore <a href="http://www.nexenservices.com">Nexen Services</a>. ', 'Soissons Informatique Libre - Centre Européen de Transfert et de Ressources en Informatique Libre est une association loi 1901, créée en janvier 2001 et dirigée par François Désarménien, est située au cœur du futur Technoparc de Soissons.\r\n\r\nSoutenue par la Communauté d''Agglomération du Soissonnais, l''Etat, le Conseil Régional de Picardie et le Département, sa vocation est de faire émerger des projets innovants et de contribuer aux progrès techniques et scientifiques des entreprises, administrations, collectivités et des organisations dans les domaines du logiciel libre.', '<p>Des partenaires publics soutiennent aussi l''initiative, parmi lesquels Le Ministère Délégué à la Recherche, l''ADAE, le FEDER, la SGAR, la Communauté d''Agglomération du soissonnais, le Conseil Régional de Picardie, le Conseil Général de l''Aisne et la ville de Soissons.</p>\r\n\r\n\r\n<p>Le <a href="http://www.tropheesdulibre.org">concours international du logiciel libre</a> est en marche avec déjà près de 150 projets inscrits. Le nom des lauréats sera dévoilé le 26 mai 2005 à Soissons, l''occasion d''un événement unique. Pour comprendre les enjeux et la dynamique du phénomène, les meilleurs experts viendront débattre sur des thèmes d''actualité comme le modèle de mutualisation ou les brevets logiciels en Europe. Cette journée permettra d''identifier les meilleures pratiques et apportera des réponses concrètes aux besoins de l''entreprise, avec des témoignages d''utilisateurs, des échanges privilégiés avec les praticiens, des ateliers technologiques et une conférence plénière.</p>\r\n\r\n<p>Le concours international du logiciel libre est en marche avec déjà près de 150 projets inscrits. Le nom des lauréats sera dévoilé le 26 mai 2005 à Soissons, l''occasion d''un événement unique. Pour comprendre les enjeux et la dynamique du phénomène, les meilleurs experts viendront débattre sur des thèmes d''actualité comme le modèle de mutualisation ou les brevets logiciels en Europe. Cette journée permettra d''identifier les meilleures pratiques et apportera des réponses concrètes aux besoins de l''entreprise, avec des témoignages d''utilisateurs, des échanges privilégiés avec les praticiens, des ateliers technologiques et une conférence plénière.</p>\r\n\r\n<p>Avec à la participation de partenaires prestigieux, SIL-CETRIL confirme son engagement aux acteurs du libre et permettra de démontrer comment le mouvement du logiciel libre est en train de dicter l''avenir de l''industrie du logiciel.</p>\r\n\r\n<p>Cet événement multiple viendra aussi souligner la volonté de la communauté d''agglomération du Soissonnais de miser sur le logiciel libre dans le but de redynamiser son territoire en offrant un accueil privilégié aux acteurs du libre et ainsi permettre un nouvel élan à sa région.</p>\r\n\r\n<p>Les candidats peuvent être issus du monde de l''entreprise ou de celui de la communauté des développeurs talentueux. Ils participent au concours des trophées du Libre, parce que c''est avant tout la création de solutions Open Source qui y est à l''honneur et parce qu''ils ont la chance de voir leur projet examiné par des personnalités renommées dans le monde du logiciel libre et de l''entreprise. Les développeurs viennent également pour se mesurer au travail de leurs collègues et apporter des solutions pragmatiques aux besoins exprimés des 6 catégories représentées :</p>\r\n\r\n<ul>\r\n<li>Sécurité </li>\r\n<li>Applications pour les structures publiques </li>\r\n<li>collectivités</li>\r\n<li>Gestion d''entreprises </li>\r\n<li>Educatif / Multimedia </li>\r\n<li>Mobilité </li>\r\n<li>Système embarqué</li>\r\n<li>Prix Spécial PHP</li>\r\n</ul>\r\n\r\n<a href="http://www.tropheesdulibre.org">Le site des trophés</a>\r\n\r\n\r\n\r\n\r\n', 0, 1115036635, 1, NULL),
(278, 9, '', 'SAP se lance dans la promotion de PHP', 'sap-se-lance-dans-la-promotion-de-php', '', '', 'Après l''accord signé entre Zend et SAP Ventures (cf. <a href="http://342">Intel et SAP Ventures, nouveaux partenaires financiers de Zendarticle</a>) les travaux avancent au niveau de l''intégration entre SAP et PHP.\r\n\r\nOn pourra ainsi très bientôt trouver une section orientée à propose de PHP sur le SAP Developer''s Network. Et dès à présent le blog existe : <a href="http://https://www.sdn.sap.com/sdn/weblogs.sdn?blog=/pub/u/43220"> SAP Developer Network PHP Weblog</a>. Il est maintenu par John Coggeshall, un des conférenciers les plus actifs autour des technologies PHP.', 0, 1115221475, 1, NULL);
INSERT INTO `afup_site_article` (`id`, `id_site_rubrique`, `surtitre`, `titre`, `raccourci`, `descriptif`, `chapeau`, `contenu`, `position`, `date`, `etat`, `id_personne_physique`) VALUES
(282, 19, '<p>Itool Systems www.itool.com</p>', 'Modèle économique et technique d''une solution de comptabilité en PHP chez Itool', 'mod-le-conomique-et-technique-d-une-solution-de-comptabilit-en-php', '<p>Un &eacute;diteur de progiciel nous explique pourquoi et comment il utilise PHP dans le cadre du d&eacute;veloppement de ses solutions.</p>', '<p>Itool Systems con&ccedil;oit et commercialise des applications de gestion &agrave; destination des entreprises et Expert comptables. Itool Systems est un &eacute;diteur ASP (Application Service Provider ou Fournisseur d''Applications H&eacute;berg&eacute;es). Ses applications sont lou&eacute;es entre 15 et 50 euros par mois et accessibles sur Internet avec un simple navigateur, ind&eacute;pendemment du syst&egrave;me d''exploitation (Windows, Linux ou MacOS).</p>', '<h3>Soci&eacute;t&eacute; et mod&egrave;le &eacute;conomique</h3>\r\n<p><strong>Pouvez-vous nous faire une pr&eacute;sentation de votre soci&eacute;t&eacute; ?</strong></p>\r\n<p><em></em> Itool Systems con&ccedil;oit et commercialise des applications de gestion &agrave; destination des entreprises et Expert comptables.  Nos applications se basent sur un navigateur web. Elles sont lou&eacute;es entre 15 et 50 euros par mois.</p>\r\n<p><strong>Pouvez-vous nous faire une pr&eacute;sentation de votre produit Itool Compta ?</strong></p>\r\n<p>Itool Compta est une application de comptabilit&eacute; g&eacute;n&eacute;rale qui est utilis&eacute;e par l''entreprise et par son Expert comptable. Autour de la comptabilit&eacute; nous disposons aussi d''applications de gestion commerciale, de notes de frais et de GED.</p>\r\n<p><strong>Quelle est la volum&eacute;trie de votre projet ?</strong></p>\r\n<p>Nous g&eacute;rons la comptabilit&eacute; de  1 500 entreprises en collaboration avec 270 cabinets. Au niveau comptable cela repr&eacute;sente 7 millions de lignes. Ces applications sont utilis&eacute;es par plus de 2 500 utilisateurs avec des pics de 70 sessions simultan&eacute;es sur nos serveurs applicatifs. Une r&eacute;cente &eacute;tude de ip-label a montr&eacute; un taux de disponibilit&eacute; de 99.9 %.  Par ailleurs, le laboratoire SPC d''IBM nous a certifi&eacute; la tenue en charge de notre plate forme &agrave; 300 sessions simultan&eacute;es.  <strong>Olivier Ferlin, Pr&eacute;sident cofondateur</strong></p>\r\n<h3>Architecture technique / logiciel</h3>\r\n<p><strong>Pourquoi PHP plut&ocirc;t qu''une autre technologie?</strong></p>\r\n<p>Les deux crit&egrave;res pris en compte ont &eacute;t&eacute; les performances et le prix.</p>\r\n<p><strong>Pouvez-vous nous d&eacute;crire votre utilisation de PHP ?</strong></p>\r\n<p>Nous utilisons PHP pour toutes nos applications web, mais aussi pour la g&eacute;n&eacute;ration des documents Excel, PDF, XML... Par ailleurs, nous avons d&eacute;velopp&eacute; un module C++ pour PHP, permettant la conversion de document XML en PDF. (XMLPDF est sous licence GPL : http://sourceforge.net/projects/xmlpdf). Aussi, PHP facilite l''acc&egrave;s &agrave; nos bases de donn&eacute;es dans nos scripts shell.</p>\r\n<p><em><strong>Quels autres logiciels et briques logicielles utilisez-vous ?</strong></em></p>\r\n<p><em><strong></strong></em> Nous utilisons MySQL pour la base de donn&eacute;es, Apache pour les serveurs web, XMLPDF et libpdf pour la g&eacute;n&eacute;ration de PDF, Imagick pour la cr&eacute;ation d''image, libxml pour la cr&eacute;ation/lecture de donn&eacute;es XML, yats pour le templating.</p>\r\n<p><strong> Bilan:&nbsp;</strong><strong>Quels sont selon vous les 3 principaux avantages de PHP ?</strong></p>\r\n<p>La simplicit&eacute;, la performance et le principe de l''Open Source.</p>\r\n<p><strong>Les 3 principaux inconv&eacute;nients ?</strong></p>\r\n<p>Nous n''en connaissons que 2 : le langage interpr&eacute;t&eacute; et la non-persistance...</p>\r\n<p><strong>Quel bilan faites-vous aujourd''hui ?</strong></p>\r\n<p><strong></strong> La facilit&eacute; d''apprentissage et la rapidit&eacute; du d&eacute;veloppement font de PHP et des produits connexes une plateforme de d&eacute;veloppement &agrave; part enti&egrave;re, en bonne voie pour concurrencer des produits tels que .NET ou J2EE.   Aujourd''hui, le projet Itool Compta nous semblerait inenvisageable sur une autre plateforme dans les m&ecirc;mes conditions &eacute;conomiques.  <strong>Mathieu Virbel, Responsable d''exploitation</strong></p>', 0, 1121810400, 1, 0),
(283, 65, '', 'Appel à conférenciers', 'appel-conf-renciers', 'L''AFUP, Association Française des Utilisateurs de PHP, a le plaisir d''annoncer le Forum PHP 2005, qui aura lieu les 9 et 10 novembre 2005, à Paris. Pour cet événement unique en France, nous recherchons les experts francophones qui souhaitent partager leurs experiences et leurs savoirs-faire.', '', '(english version at bottom)\r\n\r\nLe Forum PHP 2005 se déroulera sur deux jours avec des thèmes distincts :\r\n\r\n<ul>\n<li>Journée technique, couvrant les techniques avancées PHP\r</li>\n<li>Journée fonctionnelle, destinée à partager les expériences en PHP\r</li>\n</ul>\n\r\n<strong>Date et situation :</strong>\r\n\r\nLe Forum PHP 2005 se tiendra à Paris, à la SNH (Société Nationale d''Horthiculture), les mardi et mercredi 9 et 10 Novembre 2005.\r\n\r\n<strong>Candidature :</strong>\r\n\r\nNous attendons les propositions de session par courriel, à l''adresse suivante : bureau@afup.org, en français. Indiquez clairement votre nom et votre société, si pertinent ; une courte biographie, de 4 à 6 phrases sur votre expérience en PHP, vos coordonnées complètes. Les sessions durent 45 mins, suivi de questions du public.\r\n\r\nVous pouvez soumettre plusieurs propositions de sessions. Pour chacune, indiquez le titre de la session, l''audience visée, et une courte description de la session (10 phrases). Vous pouvez aussi indiquer la journée à laquelle vous pensez programmer cette session (technique ou fonctionnelle).\r\n\r\n<strong>Date limite de dépot des candidatures :</strong>\r\n\r\n30 Juin 2005, 23h59, heure de Paris.\r\n\r\n<strong>Comité de sélection :</strong>\r\n\r\nLe comité de sélection du Forum PHP 2005 est composé des membres du bureau 2005 de l''AFUP :\r\n - Perrick Penet, Président\r\n - Damien Séguy, Vice-Président\r\n - Jean-Marc Fontaine, Trésorier\r\n - Francois Billard-Madrières, Secrétaire\r\n\r\n<strong>Trousse du conférencier :</strong>\r\n\r\nLe Forum PHP 2005 couvrira les dépenses de voyage et deux nuits d''hôtels sur Paris aux conférenciers retenus. Les conférenciers auront aussi accès complet aux deux jours du forum. Les conférenciers sont conviés à un souper avec les membres de l''AFUP et les commanditaires, le jeudi soir.\r\n\r\n<strong>Processus de sélection des candidatures :</strong>\r\n\r\nLe comité de sélection recevra toutes les candidatures. Après clôture de la période des soumissions, il étudiera toutes les propositions, en demandant éventuellement un complément d''information. Le choix des sessions sera basé sur la présentation de la session, son intérêt pour une audience professionnelle et la complémentarité des sujets abordés durant le forum. Les candidats recevront individuellement la décision concernant leurs suggestions. La décision du comité de sélection est sans appel. La priorité est donnée aux sessions en français.\r\n\r\n<strong>Call to speaker for Paris Forum 2005</strong>\r\n\r\nThe AFUP, Association Française des Utilisateurs de PHP, is proud to announce the upcoming conference "Forum PHP 2005". For this unique event in France, we are looking for the best French speaking experts, who want to share their know-how and enthusiasm. The forum PHP features 2 days, with distinct themas :\r\n\r\n<ul>\n<li>Technical day, with the most advanced PHP technics\r</li>\n<li>Business day, with user cases and sucessuful projects\r</li>\n</ul>\n\r\n<strong>Date and location :</strong>\r\n\r\nThe Forum PHP 2005 will take place in Paris, at the SNH (Société Nationale d''Horthiculture), on Wednesday 9th and Thursday 10th, November 2005. ', 0, 1117611755, 1, NULL),
(286, 9, '', 'Ravalement de façade dopé aux logiciels libres pour Companeo.com', 'ravalement-de-fa-ade-dop-aux-logiciels-libres-pour-companeo-com', 'Le guide d''achat de services et d''équipements pour les entreprises Companeo.com a dévoilé le 27 juin 2005 la nouvelle maquette de son site web, articulé autour des technologies libres, des changements rendus nécessaires par l''évolution radicale du profil de ses visiteurs.', '', '\r\n\r\nMise en ligne le 27 juin 2005, la nouvelle version du site Companeo a été développée en 6 mois par une équipe de 7 personnes. Ce ravalement de façade, a choisi d''exploiter les technologies des logiciels libres (PhP, PostgreSql) et Linux.\r\n\r\nUne l''étude réalisée sur l''ancienne version de Companeo.com constatait que les décideurs d''entreprise souhaitaient plus de clarté, de simplicité et de contenu pour les aider à choisir. Toujours selon cette étude, les dirigeants attendaient un graphisme qui tranche des sites BtoC, tout en restant en phase avec leur univers professionnel.\r\n\r\nC''est sur le marché belge que Companeo a testé dès avril 2005 les nouvelles fonctionnalités du site Internet companeo.be.\r\n\r\n<a href="http://www.toolinux.com/news/services/ravalement_de_facade_dope_aux_logiciels_libres_pour_companeo.com_ar6379.html">L''information sur TooLinux</a>', 0, 1120140008, 1, NULL),
(289, 19, '', 'LeMonde.fr ajoute une couche XUL sur son back-office PHP', 'lemonde-fr-ajoute-une-couche-xul-sur-son-back-office-php', 'Dans un article du Journal du Net, Jean-Christophe Potocki, directeur informatique du monde.fr, présente la <a href="http://solutions.journaldunet.com/0509/050916_cas_lemonde_xul.shtml">migration complète</a> de leur architecture vers l''Open Source. ', '', 'Initiée par un framework en PHP, cette migration se poursuit avec une interface utilisateur en XUL.\r\n\r\nUne présentation de ce projet fédérateur (baptisé SEPT - Système Editorial de Production et de Trafic) sera effecutée par Olivier Grange-Labat -- responsable système chez LeMonde.fr -- au prochain forum PHP les 9 et 10 novembre 2005.\r\n\r\nCette article suit notre article de 2004 :\r\n<a href="https://afup.org/pages/site/?route=retours-d-experience/212/le-journal-en-ligne-le-monde">Le journal en ligne Le monde </a>\r\n\r\nUne présentation plus complète de l''application XUL utilisée par le monde.fr\r\n<a href="https://afup.org/pages/site/?route=rendez-vous-de-l-afup/298/r-sum-de-la-conf-rence-clients-riches-avec-xul">Résumé de la conférence "Clients riches avec XUL"</a>', 0, 1126821600, 1, 0),
(292, 65, '', 'Résumés et présentations disponibles', 'r-sum-s-et-pr-sentations-disponibles', 'Les supports des conférences du Forum PHP 2005 sont <a href="https://afup.org/pages/forumphp/resumes.php">disponibles au téléchargement</a>. Ils sont complétés par un résumé des 2 jours de sessions effectué par Henry Cesbron Lavau, rédacteur expert.', '', 'Vous avez raté le Forum PHP 2005 ? Vous souhaitez utiliser les documents présentés au Forum pour promouvoir PHP au sein de votre entreprise ? L''ensemble des supports des conférences sont en ligne sur le site du Forum organisé cette année par l''AFUP.\r\n\r\n', 0, 1132056254, 1, NULL),
(293, 9, '', 'PHP 1 - J2EE 0 pour l''ERP du groupe Girard', 'php-1-j2ee-0-pour-l-erp-du-groupe-girard', 'Le groupe Girard est leader européen du transport de meubles choisit PHP pour son ERP et détaille les avantages qu''ils ont trouvés.', '', 'Le groupe Girard est leader européen du transport de meubles choisit PHP pour son ERP et détail les avantages qu''ils ont trouvés.\r\n\r\nLeur retour d''expérience et des explications du choix par le directeur technique sont disponibles sur le site d''Indexel : <a href="http://www.indexel.net/1_6_4264__3_/15/89/1/Le_groupe_Girard_prefere_PHP_a_J2EE_pour_developper_son_ERP.htm">Le groupe Girard préfère PHP à J2EE pour développer son ERP</a>', 0, 1132912741, 1, NULL),
(294, 9, '', 'L''AFUP sera présent au Salon Solutions Linux 2006', 'l-afup-sera-pr-sent-au-salon-solutions-linux-2006', 'L''AFUP sera présent au <a href="http://www.solutionslinux.fr/fr/index.php">Salon Solutions Linux</a> du 31 janvier au 2 février 2006. Cette manifestation rassemble l''ensemble des acteurs professionnels et associatifs du monde Open Source.', '', 'Après une première participation en 2005, l''AFUP aura l''honneur de présenter la 3ème édition du Livre Blanc ainsi que la vidéo du dernier Forum PHP à Paris. Ce sera aussi l''occasion d''échanger autour de la plateforme web la plus utilisé dans le monde.', 0, 1134031477, 1, NULL),
(295, 58, '', '02/03/2006 : clients riches avec XUL', '02-03-2006-clients-riches-avec-xul', 'Le 2 mars 2006 à partir de 20h et à Paris l''AFUP organise une rencontre sur le thème des clients riches avec XUL.\r\nLa conférence sera présentée par Laurent Jouanneau (initiateur de xulfr.org et ingénieur chez Disruptive Innovations), Edouard Andrieu et Olivier Grange-Labat (respectivement chef de projet et responsable technique au sein de LeMonde.fr).', 'XUL, pour XML-based User interface Language, est un langage de description d''interfaces graphiques basé sur XML créé dans le cadre du projet Mozilla. XUL se prononce zoul en anglais (pour rimer avec cool, mais aussi en hommage au demi-dieu Zoul dans le film SOS Fantômes).', 'L''Association Française des utilisateurs de PHP vous invite à découvrir comment développer des clients riches avec XUL,  un format XML aux composants XPCOM réutilisables et multi plate-forme.\r\n\r\n<strong>Sujet :</strong> Clients riches avec XUL\r\n\r\n<strong>Intervenants :</strong> Laurent Jouanneau (xulfr.org / Disruptive Innovations), Edouard Andrieu et Olivier Grange-Labat (LeMonde.fr)\r\n\r\n<strong>Date :</strong> Le jeudi 2 mars 2006 à partir de 20 heures\r\n\r\n<strong>Durée :</strong> 2h30 maximum\r\n\r\n<strong>Tarif :</strong> Gratuit, accès prioritaire aux membres AFUP\r\n\r\n<strong>Places disponibles :</strong> 50\r\n\r\n<strong>Lieu :</strong> <a href="http://www.fiap.asso.fr/">Espace FIAP JEAN MONNET</a> \r\nSalle Madrid\r\n30 rue Cabanis 75014 Paris\r\n\r\n<center><strong><h3><a href="https://afup.org/pages/rendezvous/">Inscription à la conférence</a></h3></strong></center>\r\n\r\n\r\nDeux interventions viendront ponctuer ce rendez-vous. Tout d''abord Laurent Jouanneau  présentera XUL,  langage basé sur XML pour décrire une interface graphique. Ensuite deux membres de l''équipe de développement de LeMonde.fr, Edouard Andrieu et Olivier Grange-Labat feront une démonstration de leur application "Le Sept", extension Firefox utilisée par les journalistes pour mettre à jour le site web du Monde.\r\n', 0, 1139439600, 1, 0),
(297, 19, '', 'Flickr, le service de partage de photo de Yahoo ! utilise PHP', 'flickr-le-service-de-partage-de-photo-de-yahoo-utilise-php', '<p>60.000 lignes de code PHP, 25.000 transactions par seconde en base de donn&eacute;es, 1.000 pages affich&eacute;es par seconde.  C''est bien avec PHP que Flickr a &eacute;t&eacute; d&eacute;velopp&eacute;.</p>', '<p>Flickr est un site d''&eacute;change de photos, appartenant &agrave; la sph&egrave;re Yahoo!, permettant la diffusion, le partage et le chargement des photos en ligne.</p>', '<p>Flickr est &agrave; la fois un site et un syst&egrave;me d''&eacute;change de photos. Il permet, gr&acirc;ce &agrave; des services Web, d''utiliser tout ou partie des API.  L''int&eacute;r&ecirc;t de cette architecture r&eacute;side dans son appartenance &agrave; la sph&egrave;re Yahoo! Par d&eacute;faut ce type d''application est pris d''assaut et n&eacute;cessite donc des garanties de services. L''utilisation de PHP permet de servir pr&egrave;s de mille pages par secondes, ce qui repr&eacute;sente sur une journ&eacute;e un total sup&eacute;rieur &agrave; 80 millions de pages !  Le premier goulot d''&eacute;tranglement de l''application a &eacute;t&eacute; atteint avec MySQL (avec plus de 25.000 transactions par seconde). La solution a consist&eacute; en l''utilisation des fonctions de r&eacute;plication. D''un cot&eacute; un serveur ma&icirc;tre qui re&ccedil;oit les requ&ecirc;tes d''&eacute;critures (Insert / Update / Delete) et en dessous des fermes de serveurs esclave pour les requ&ecirc;tes de lecture (Select).   Ce document permet d''en savoir plus sur l''architecture de Flickr :</p>', 0, 1142204400, 1, 0),
(298, 58, '', 'Résumé de la conférence "Clients riches avec XUL"', 'r-sum-de-la-conf-rence-clients-riches-avec-xul', 'Le 2 mars 2006, Laurent Jouanneau (initiateur de xulfr.org et ingénieur chez Disruptive Innovations), Edouard Andrieu et Olivier Grange-Labat (respectivement chef de projet et responsable technique au sein de LeMonde.fr) ont présenté le développement d''applications en client riche avec XUL (un format XML aux composants XPCOM réutilisables et multi plate-forme).', '<a href="http://www.eyrolles.com/Informatique/Livre/9782212116755/livre-xul.php">Le livre XUL</a>  aux éditions Eyrolles dans la collection des cahiers du programmeur vous fera découvrir, à travers la création d''un forum écrit en XUL, une plate-forme de développement novatrice : le framework Mozilla.\r\n\r\n>>> <a href="http://php.openstates.org/conf-afup-xul.mp3">Télécharger la conférence de Laurent Jouanneau en mp3</a>\r\n\r\nMerci à Michel Lefranc pour son intervention.', 'C''est dans l''espace très international du FIAP Jean Monet à Paris que s''est déroulé le jeudi 2 mars 2006 le rendez-vous de l''AFUP sur les clients riches avec XUL.\r\n\r\nSuite logique de la présentation d''Olivier Grange-Labat lors du Forum PHP de novembre 2005, la soirée a commencé par une introduction plus détaillée de XUL faite par Laurent Jouanneau. Initiateur de xulfr.org et ingénieur de Disruptive Innovations, c''est lui qui a formé et accompagné l''équipe technique du site LeMonde.fr.\r\n\r\n<h3>Introduction à XUL</h3>\r\n\r\nAprès un rapide historique de l''évolution du poste client web, de Netscape à Mozilla, nous sommes entrés dans le vif du sujet : XUL (prononcez zul ou zoul selon affinité) est un langage de description en XML de l''interface utilisateur (XML based User Interface Language).\r\nCouplé avec un client capable de l''interpréter, tel que FireFox, il permet le déploiement d''applications web.\r\nChaque page est décrite à l''aide de balises (boutons, menu, zones de saisie, table etc.).\r\nLe modèle d''emboîtement des contrôles est de type Motif X11, donc différent du CSS, mais offre les mêmes possibilités. XUL est ouvert sur les autres technologies de développement Web : javascript (en CDATA), DOM, Webservices, Xpath, XPCOM, E4X (simple_xml d''EcmaScript).\r\nXUL a son propre système de template. D''autres standards XML interviennent :\r\nRDF (Resource Description Framework) permet de stocker sous forme XML des données relationnelles et remplace avantageusement Ajax dans le cas où l''on travaille sur un ensemble de données.\r\nAprès saisie en local, les données sont envoyées au serveur et l''arbre des données RDF est mis à jour sans avoir à réafficher la page entière.\r\nXBL (XML Binding Language) (prononcez zibeul) permet de réaliser son propre balisage.\r\nOn peut personnalisez les widgets par héritage.\r\n\r\nIl suffit donc  à l''aide d''un éditeur de décrire sa page en langage XUL pour que celle-ci soit affichée sur le poste client par une interprétation directe faite par le navigateur FireFox. La contrainte de FireFox n''est pas un problème dans le cas d''un Intranet. D''autant que ce navigateur offre de nombreuses fonctionnalités complémentaires, telle la gestion de l''installation de l''application en tant qu''extension et sa mise à jour automatique lors de la connexion. XULRunner permet même de faire tourner une application purement en local.\r\n\r\nLa présentation a été suivie de questions / réponses qui ont permis de préciser les points suivants :\r\n\r\nS''il n''y a pas d''environnement de développement (IDE), en revanche, l''organisation des fichiers constitutifs de l''application est suffisamment structurée pour permettre un développement efficace.\r\n\r\nGecko 1.9 (moteur de FireFox 3) début 2007 intègrera SQLite ce qui permettra d''utiliser d''autres sources de données que RDF.\r\n\r\n<h3>Cas d''utilisation de XUL : LeMonde.fr</h3>\r\n\r\nAprès la théorie, la pratique : \r\n\r\nOlivier Grange-Labat, accompagné de Edouard Andrieu, nous ont présenté le site LeMonde.fr. Plus de 80 000 abonnés soit 80 millions de pages vues / mois et plus de  200 nouveaux articles / jour. Il s''agit du premier site généraliste français.\r\n\r\n<img5|center>\r\nCopyright Le Monde interactif\r\n\r\nOlivier nous a rappelé l''historique : une solution à l''origine, propriétaire, coûteuse, une réflexion, une validation des use cases avec Daniel Glazman (Dirigeant - Fondateur de Disruptive Innovations), et la mise en place progressive accompagnée par Laurent ont permis d''avoir un superbe outil : le SEPT (Système Editorial de Production et de Trafic). \r\n\r\nEcrit en XUL, il s''appuie sur FireFox pour son exécution sur le poste client, mais se présente pour l''utilisateur (le rédacteur) comme une application autonome.\r\n\r\nLa zone d''édition remplit la partie principale de l''écran : elle permet de rédiger les articles, de faire du copier-coller et du glisser-déposer, de travailler le format des photos.\r\nEn dessous, la ZEN (Zone d''Enrichissement et de Navigation) permet de se déplacer dans les ressources en suivant de riches arborescences et de compléter facilement l''article.\r\nEnfin divers champs entourent l''article : Titre, sur-titre, sous-titr, etc...\r\n\r\nLe développement avec XUL a permis d''utiliser de nombreux contrôles qu''il suffisait d''assembler : par exemple, la présentation des ressources en tables avec tri sur toutes les colonnes n''a pas nécessité d''écriture.\r\n\r\nL''ensemble est très convivial.\r\n\r\nLe rafraîchissement du serveur est fait de manière transparente toutes les minutes. Ce délai sera réduit à la seconde dans la prochaine version.\r\n\r\nLes utilisateurs sont satisfaits, aussi bien sous PC que sous MAC.\r\n\r\n\r\nLes points forts de la technologie vus par l''équipe de développement sont la puissance, le structuration, la documentation, l''accès aux sources, la richesse des extensions, et le fun.\r\nLes points faibles sont l''absence de Best practises et un débuggage  parfois laborieux.\r\n\r\nSi la courbe d''apprentissage est longue, elle reste très progressive : on peut démarrer un projet sans avoir tout vu.\r\n\r\nCette démonstration en live a convaincu un auditoire visiblement très impressionné.\r\n\r\nAlors  : XUL nouveau standard du client riche ? L''avenir, en tout cas, semble prometteur.\r\n\r\nLa présentation de la technologie suivie d''une magistrale application professionnelle ont fait de cette soirée un fort moment du calendrier de l''AFUP.', 0, 1142782855, 1, NULL),
(299, 9, 'MySQL en France', '29/03/2006 : Le Stack LAMP dans les Entreprises Modernes', '29-03-2006-le-stack-lamp-dans-les-entreprises-modernes', 'Si vous êtes sous pression de « faire plus avec moins », ou si vous cherchez à réduire vos coûts tout en maintenant la qualité et la performance de votre infrastructure, cette présentation peur vous aider à développer une stratégie open source et à comprendre les implications économiques clés liées au déploiement du stack LAMP.\r\nUn séminaire Web présenté par Mårten Mickos, Directeur Général, MySQL AB', 'MySQL AB développe et vend toute une gamme de serveurs de bases de données et d''outils performants et abordables. Notre mission est de rendre la gestion de données accessible à tous. Dans le monde entier, nous contribuons à la construction de systèmes destinés à des missions critiques et supportant de gros volumes.\r\nMySQL propose du <a href="http://www-fr.mysql.com/network/">support</a>, du <a href="http://www-fr.mysql.com/consulting/">conseil</a> et des <a href="http://formation.anaska.fr/formation-mysql.php">formations MySQL</a> en français !', 'Les logiciels libres ne sont pas seulement prêts pour l''entreprise, ils ont d''ores et déjà fait leurs preuves. Les références internationales telles que Google, Lycos Europe, Lafarge, EADS, Alcatel, Suzuki ou encore la NASA, réduisent de manière significative leurs coûts en utilisant les logiciels libres pour leurs sites Web, leurs applications critiques d''entreprise, ou en intégrant MySQL à leurs logiciels.\r\n\r\nMais ce ne sont pas seulement les grands comptes qui mettent en place des solutions basées sur les logiciels libres. De nombreuses sociétés évaluent un « open source stack » comme une alternative ou un complément aux solutions propriétaires proposées par des entreprises telles que Microsoft, IBM ou Oracle. \r\n\r\nDans cette présentation (en Anglais), Mårten Mickos, Directeur Général de MySQL AB, abordera les points suivants :\r\n<ul>\n<li>L''évolution de l''open source\r</li>\n<li>L''adoption croissante du stack LAMP\r</li>\n<li>Les implications économiques de la mise en place de ce stack\r</li>\n<li>Exemples de déploiements d''entreprise du stack LAMP\r</li>\n</ul>\n\r\nPour vous inscrire :\r\n<a href="http://www.mysql.com/news-and-events/web-seminars/lamp-stack.php">http://www.mysql.com/news-and-events/web-seminars/lamp-stack.php</a>\r\n\r\nQUI: Mårten Mickos, Directeur Général, MySQL AB\r\n\r\nQUAND: Le 29 Mars 2006 à 20h00 (heure d''été de Paris). La présentation durera environ 45 min, suivie par 15 min de questions/réponses\r\n\r\nOÙ: Dans votre bureau ou chez vous, via votre navigateur\r\n', 0, 1142851182, 1, NULL),
(303, 9, 'Quelques liens et références', 'Evaluation de la certification PHP de Zend par l''AFUP', 'evaluation-de-la-certification-php-de-zend-par-l-afup', 'La société Zend a mis en place une certification sur PHP qui permet aux développeurs de valider leur niveau.', '<a href="http://www.anaska.com/certification-php.php">En savoir plus sur la certification PHP</a>\r\n\r\n<a href="http://blog.agoraproduction.com/index.php?/archives/23-Zend-PHP5-Certification-self-test.html">Test sur la certification</a>\r\n\r\n<a href="http://www.anaska.com/formations/formation-php-expert-certifie.php">La formation officielle de préparation à la certification PHP</a>\r\n', 'Il existe plusieurs certifications sur PHP. L''une d''elle, mise en place par la société Zend, a été testée par nos équipes.\r\nCinq de nos experts ont tenté de la passer, avec et sans préparation afin de d''évaluer la pertinence et le niveau de cette certification.\r\n\r\nDans ce dossier vous trouverez donc le détail de cette expérience ainsi que des informations sur le rôle de l''AFUP et l''impact que cette certification aura selon nous.\r\n\r\n<a href="https://afup.org/docs/Communiques_de_presse_AFUP_Certification_php.pdf">Télécharger le dossier au format PDF (420 Ko)</a>', 0, 1147274688, 1, NULL),
(304, 66, '', 'Appel à conférenciers', 'appel-conf-renciers', 'L''AFUP, Association Française des Utilisateurs de PHP, a le plaisir d''annoncer le Forum PHP 2006, qui aura lieu les 9 et 10 novembre 2006, à Paris. Pour cet événement unique en France, nous recherchons les experts francophones qui souhaitent partager leurs experiences et leurs savoirs-faire.', '', 'Le Forum PHP 2006 se déroulera sur deux jours avec des thèmes distincts :\r\n\r\n<ul>\n<li>Journée technique, couvrant les techniques avancées de PHP.\r</li>\n<li>Journée fonctionnelle, destinée à partager les expériences en PHP.\r</li>\n</ul>\n\r\n<strong>Date et situation</strong>\r\n\r\nLe Forum PHP 2006 se tiendra à Paris, à la SNHF (Société Nationale d''Horthiculture de France), les jeudi et vendredi 9 et 10 Novembre 2006.\r\n\r\n<strong>Candidature</strong>\r\n\r\nNous attendons les propositions de session par courriel, à l''adresse suivante : bureau@afup.org, en français. Indiquez clairement :\r\n\r\n-* votre nom et votre société, si pertinent,\r\n-* une courte biographie, de 4 à 6 phrases sur votre expérience en PHP,\r\n-* vos coordonnées complètes. \r\n\r\nLes sessions durent 45 minutes, suivi de questions du public pendant environ 10 minutes.\r\n\r\nVous pouvez soumettre plusieurs propositions de sessions. Pour chacune, indiquez : \r\n\r\n-* le titre de la session,\r\n-* l''audience visée\r\n-* et une courte description de la session (10 phrases).\r\n\r\nVous pouvez aussi indiquer la journée à laquelle vous pensez programmer cette session (technique ou fonctionnelle).\r\n\r\n<strong>Date limite de dépot des candidatures</strong>\r\n\r\n30 Juin 2006, 23h59, heure de Paris.\r\n\r\n<strong>Comité de sélection</strong>\r\n\r\nLe comité de sélection du Forum PHP 2006 est composé des membres du conseil d''administration 2006 de l''AFUP :\r\n\r\n-* Perrick Penet, Président\r\n-* Guillaume Ponçon, Vice-Président\r\n-* Romain Bourdon, Trésorier\r\n-* Arnaud Limbourg, Secrétaire\r\n-* Jean-Marc Fontaine, membre du CA\r\n-* Olivier Lecorre, membre du CA\r\n\r\n<strong>Trousse du conférencier</strong>\r\n\r\nLe Forum PHP 2006 couvrira les dépenses de voyage et deux nuits d''hôtels sur Paris aux conférenciers retenus. Les conférenciers auront aussi accès complet aux deux jours du forum. Les conférenciers sont conviés à un souper avec les membres de l''AFUP et les commanditaires, le jeudi soir.\r\n\r\n<strong>Processus de sélection des candidatures</strong>\r\n\r\nLe comité de sélection recevra toutes les candidatures. Après clôture de la période des soumissions, il étudiera toutes les propositions, en demandant éventuellement un complément d''information. Le choix des sessions sera basé sur la présentation de la session, son intérêt pour une audience professionnelle et la complémentarité des sujets abordés durant le forum. Les candidats recevront individuellement la décision concernant leurs suggestions. La décision du comité de sélection est sans appel. La priorité est donnée aux sessions en français.', 0, 1147424798, 1, NULL),
(307, 58, '', '20/06/2006 : Utilisation optimale et professionnelle de PHP', '20-06-2006-utilisation-optimale-et-professionnelle-de-php', 'A l''occasion du prochain rendez-vous AFUP, nous vous proposons une conférence qui intéressera tous ceux qui se posent la question d''une utilisation optimale et professionnelle de PHP : architecture d''une application, frameworks, outils de travail, documentation, débogage, travail en équipe, outils d''optimisation.', 'Les intervenants :\r\n\r\n-* <strong>Jean-Marc Fontaine</strong> est consultant/expert indépendant en PHP.\r\n-* <strong>Guillaume Ponçon</strong> est architecte/formateur PHP chez <a href="http://www.anaska.fr/formation-php.php">Anaska</a> et auteur de l''ouvrage <strong><a href="http://www.amazon.fr/exec/obidos/ASIN/2212116764/402-5829530-3009702">Best practices PHP 5</a></strong> aux éditions eyrolles. ', '-* Date et heure : <strong>le mardi 20 juin 2006 à 20h00</strong>\r\n-* Lieu : <strong><a href="http://maps.google.fr/?hl=fr&near=30%20rue%20cabanis%20-%20paris">Espace FIAP - 30 rue Cabanis - 75014 Paris</a></strong>\r\n\r\nCette conférence se veut pratique et riche d''informations utiles. Son but est de permettre aux développeurs comme aux décideurs d''avoir les yeux ouverts sur les possibilités offertes par une utilisation efficace de la plate-forme PHP. \r\n\r\nLes conférenciers remercient toutes les personnes présentes lors de la conférence. Ils ont également été très heureux de prolonger le débat en fin de conférence et de recevoir vos encouragements par e-mail. \r\n\r\nSuite à plusieurs demandes, nous mettons à disposition les slides de la conférence : \r\n\r\n<doc7|center>', 0, 1149858248, 1, NULL),
(308, 58, '', 'Mise en ligne des supports du rendez-vous AFUP sur l''utilisation optimale et professionnelle de PHP', 'mise-en-ligne-des-supports-du-rendez-vous-afup-sur-l-utilisation-optimale-et-professionnelle-de-php', 'Le 20 juin 2006, Guillaume Ponçon (Architecte / Formateur PHP chez Anaska et auteur de l''ouvrage français Best Practices PHP 5) et Jean-Marc Fontaine (Directeur technique de la société Kanopée) ont présenté une conférence ayant pour thème l''utilisation optimale et professionnelle de PHP.', '', 'Les supports du rendez-vous AFUP sur l''<a href="https://afup.org/article.php3?id_article=307">utilisation optimale et professionnelle de PHP</a> qui a eu lieu le 20 juin sont à présent disponibles en ligne.\r\n\r\n<a href="https://afup.org/docs/conf_optimisation.pdf">Télécharger les supports au format PDF.</a>', 0, 1151049663, 1, NULL),
(310, 9, '', 'Statistiques d''utilisation de PHP / Aout 2006', 'statistiques-d-utilisation-de-php-aout-2006', 'Les statistiques de diffusion de PHP pour le mois de aout 2006 sont disponibles. ', '', 'En résumé :\r\n\r\n<ul>\n<li>PHP 5 dépasse les 10%, avec un mois record d''adoption\r</li>\n<li>PHP 4.4 est sur le point de détroner PHP 4.3\r</li>\n<li>La France est en tête pour l''adoption de PHP (45% des sites Web)\r</li>\n</ul>\n\r\n<strong>Evolutions</strong>\r\n\r\n<a href="http://www.nexen.net/chiffres_cles/phpversion/evolution_de_php_sur_internet_aout_2006.php">http://www.nexen.net/chiffres_cles/phpversion/evolution_de_php_sur_internet_aout_2006.php</a>\r\n\r\n<strong>Détails</strong>\r\n\r\n<a href="http://www.nexen.net/chiffres_cles/phpversion/statistiques_de_deploiement_de_php_en_aout_2006.php">http://www.nexen.net/chiffres_cles/phpversion/statistiques_de_deploiement_de_php_en_aout_2006.php</a>\r\n', 0, 1157453704, 1, NULL),
(311, 66, '', 'PHP fait salon à Paris les 9 et 10 novembre 2006', 'php-fait-salon-paris-les-9-et-10-novembre-2006', 'Le forum PHP 2006 se tiendra les 9 et 10 novembre à Paris. Des poids lours de l''industrie Internet seront présents, notamment Yahoo! -- avec le créateur de PHP, Rasmus Lerdorf -- et Google.', '', 'Ce forum s''adresse à tous ceux qui développent des applications Web. La première journée est axée sur le domaine fonctionnel, la deuxième est orientée technique.\r\n\r\nDe nombreux domaines seront abordés : les bonnes pratiques en PHP, l''intégration PHP / Java, les motifs de conception, le futur de PHP.\r\n\r\nLe forum PHP organisé par l''Association Française des Utilisateurs de PHP (AFUP) est une occasion unique de rencontrer la communauté et les professionnels de PHP.\r\n\r\nPlus d''informations sur <a href="https://afup.org/forumphp/"></a>', 0, 1159518400, 1, NULL),
(312, 27, '', 'Appel à témoignages : Recherche retour d''expérience PHP 5', 'appel-t-moignages-recherche-retour-d-exp-rience-php-5', 'Nous recherchons pour la presse des témoignages d''entreprises utilisatrices de PHP 5. ', '', 'Les paramètres :\r\n<ul>\n<li>PME / PMI\r</li>\n<li>entreprise ne travaillant pas dans l''informatique (ssii, éditeur, etc.),\r</li>\n<li>PHP 5 (si possible utilisant programmation OO)\r</li>\n</ul>\n\r\nMerci de me contacter directement : cyril@anaska.fr', 0, 1158271200, 1, NULL),
(313, 19, '', 'SugarCRM : un logiciel CRM performant, commercial et Open Source', 'sugarcrm-un-logiciel-crm-performant-commercial-et-open-source', '<p>Un &eacute;diteur de progiciel d''envergure mondiale nous explique pourquoi et comment il utilise PHP dans le cadre du d&eacute;veloppement de ses solutions.</p>', '<p>SugarCRM est le leader mondial des logiciels commerciaux Open Source dans le domaine de la gestion client (CRM). Il s''adapte tr&egrave;s facilement &agrave; tous types d''entreprise. C''est une solution souple et abordable gr&acirc;ce &agrave; un mod&egrave;le &eacute;conomique originale : &agrave; la fois commerciale et libre. En France il est possible de suivre une <a href="http://www.anaska.com/formations/formation-sugarcrm-crm-open-source.php">formation &agrave; SugarCRM</a>.</p>', '<p><strong>Bonjour, est-ce-que vous pourriez dans un premier temps nous pr&eacute;senter votre profil ainsi que celui de votre soci&eacute;t&eacute; ?</strong></p>\r\n<p>Je m''appelle Jacob Taylor. Je suis le cofondateur et le directeur technique de SugarCRM Inc. SugarCRM c''est pr&egrave;s de 90 personnes et 900 clients &agrave; travers 40 pays.</p>\r\n<p><strong>Quelles est plus en d&eacute;tail l''activit&eacute; de votre entreprise ?</strong></p>\r\n<p>Nous sommes un vendeur de logiciels commerciaux Open Source, sp&eacute;cialis&eacute; dans le domaine de la Gestion de Relation Client (CRM). Un CRM couvre trois grands axes :</p>\r\n<p>&nbsp;</p>\r\n<ul>\r\n<li>les ventes (comptes, opportunit&eacute;s et contacts) </li>\r\n<li>les services (gestion de documents) </li>\r\n<li>l''aspect marketing (suivi des prospects ou des campagnes de mailing</li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p><strong>Quelles sont les caract&eacute;ristiques de votre plateforme technique ?</strong></p>\r\n<p>D''un point de vue basique, notre logiciel peut &ecirc;tre d&eacute;ploy&eacute; sur n''importe quel OS compatible avec PHP : Linux / Windows &amp; Mac OS X. Au niveau des bases de donn&eacute;es, nous g&eacute;rons les bases MySQL, Oracle et SQL Server.  Pour nos serveurs de productions, nous avons opt&eacute; pour MySQL et pour la Zend Platform. La Zend Platform am&eacute;liore la gestion, les performances et la surveillance de nos serveurs.</p>\r\n<p><strong>Apparemment la grande majorit&eacute; de votre plate-forme tourne gr&acirc;ce aux logiciels Open Source. Pourquoi ? ( choix technique ou financier ?</strong></p>\r\n<p>Nous &eacute;tions &agrave; la recherche d''une solution transversale, c''est &agrave; dire fonctionnant sur plusieurs OS : PHP s''est impos&eacute; comme le choix &eacute;vident.  De plus, c''est un langage vraiment rapide &agrave; coder et &agrave; prendre en main. Il s''adapte parfaitement &agrave; notre &eacute;tat d''esprit. "Mettre en place l''approche la plus simple qui fonctionne et que l''on complete quand on en a besoin".  Par exemple, nous avons ajout&eacute; un m&eacute;canisme de cache externe avec la version 4.5 de SugarCRM. Auparavant, cela n''&eacute;tait pas n&eacute;cessaire : quand le besoin s''en est fait ressentir, ce fut facile de l''impl&eacute;menter.</p>\r\n<p><strong>Qu''attendez-vous des futures versions de PHP?</strong></p>\r\n<p>PHP5 est d&eacute;j&agrave; un bon produit : de meilleures performances et un meilleur support de la Programmation Orient&eacute;e Objet sont des fonctionnalit&eacute;s que nous attendions. Et MySQL 5 apporte &eacute;galement son lot d''am&eacute;liorations en terme de performance et de fonctionnalit&eacute;s.  Nous attendons le support natif de l''Unicode pr&eacute;vu pour PHP6 : avec l''UTF8 en natif avec PHP, cela facilitera grandement notre travail.}</p>\r\n<p><strong>Utilisez-vous d''autres langages de programmation pour SugarCRM ?</strong></p>\r\n<p>Nous sommes particuli&egrave;rement attach&eacute;s au langage PHP et &agrave; ses outils web associ&eacute;s (HTML, CSS, Javascript). Nous utilisons le langage .Net pour un plug-in de synchronisation Outlook qui est quasiment notre seul code qui n''est pas  d&eacute;velopp&eacute; en PHP.</p>\r\n<p><strong>Quelles sont les principales briques logicielles que vous utilisez ?</strong></p>\r\n<p>La liste compl&egrave;te est disponible sur la page "&Agrave; propos/About" de notre logiciel : XTemplate, Log4php, NuSOAP, JS Calendar, PHP PDF, DOMIT!, HTTP_WebDAV_Server, JavaScript O Lait, PclZip, Smarty, Overlibmws, WICK, FCKeditor, Yahoo! User Interface Library, PHPMailer, etc.</p>\r\n<p><strong>Quelles types de relations avez-vous avec la communaut&eacute; open-source?</strong></p>\r\n<p>Nous avons &eacute;norm&eacute;ment d''utilisateurs open-source en comparaison avec nos utilisateurs payants. Ces utilisateurs nous aident &agrave; am&eacute;liorer notre produit, &agrave; le traduire dans de nombreuses langues et &agrave; am&eacute;liorer la qualit&eacute; globale de notre logiciel. C''est une part int&eacute;grante de notre mod&egrave;le de fonctionnement.  C''est un bon &eacute;cosyst&egrave;me : de nombreux projets sont apparus sur SugarExchange et sur SugarForge. Certains de ces projets sont m&ecirc;me des concurrents directs : le plug-in JRabbit pour Outlook en est un bon exemple.  De m&ecirc;me, les traductions sont principalement g&eacute;r&eacute;es par la communaut&eacute;. Notre premier pack de langue a &eacute;t&eacute; le pack fran&ccedil;ais : 24 heures apr&egrave;s le lancement de SugarCRM il &eacute;tait disponible, nous n''avions pas pr&eacute;vu qu''il soit pr&ecirc;t si rapidement!  &Agrave; l''heure actuelle, SugarCRM est disponible dans plus de 40 langues (y compris les langues se lisant de droite &agrave; gauche) : c''est assez incroyable de voir comment l''Open Source permet aux gens n''importe o&ugrave; dans le monde de collaborer et d''innover.</p>', 0, 1162854000, 1, 0),
(314, 66, '', 'Après le succès de 2006, le Forum PHP donne rendez-vous pour 2007', 'apr-s-le-succ-s-de-2006-le-forum-php-donne-rendez-vous-pour-2007', '', 'L&#8217;AFUP (Association Française des Utilisateurs de PHP) vient de tenir son forum annuel du PHP en France. Retours d''expérience et présentations techniques auront jalonnés deux jours de conférences.', 'Avec presque 200 personnes pour chaque jour de conférences, le Forum PHP 2006 a remporté un franc succès. Des nombres entreprises (BNP Paribas, Publicis, France Telecom...) et institutions (CNRS, Ministère de l''Intérieur, DGME...) étaient présentes : preuve de la place incontournable acquise par PHP au fil des ans.\r\n\r\nLes plus grands pointures du monde PHP étaient présentes : Rasmus Lerdorf (ingénieur chez Yahoo! et créateur de PHP), Andreï Zmievski (leader sur PHP6), Derick Rethans (architecte chez eZ et membre du PHP Core) ou Zeev Suraski (CTO de Zend et auteur du moteur interne de PHP). Le monde francophone n''était pas en reste. Plus acteurs importants ont présentés leurs projets actuels : Cyril Pierre de Geyer (auteur du livre "PHP5 avancé"), Guillaume Ponçon (auteur de "Best Practices PHP5"), Gérald Croès (leader du framework Copix) ou Sébastien Hordeaux (créateur de PHPEdit).\r\n\r\nParmi les annonces importantes du Forum, celle de Zend : un partenariat a été noué avec Microsoft pour stabiliser et optimiser le moteur PHP sur leur serveur IIS. De son côté eZ Systems a présenté son modèle économique basé sur un logiciel Open Source, eZ Publish. L''autre annonce concernait le calendrier de PHP6 avec sa gestion simplifiée d''Unicode : une version ''Unicode Preview Release'' est prévu pour la fin de l''année 2006, avant une mise en production fin 2007. Si le coeur de PHP est désormais opérationnel, il reste encore toutes les extensions (elles sont très nombreuses) à migrer, la documentation à mettre à jour et les performances à améliorer. \r\n<img8|center>', 0, 1163406723, 1, NULL),
(317, 19, '', 'L''Etat français se modernise avec PHP', 'l-etat-fran-ais-se-modernise-avec-php', '<p>PHP est utilis&eacute; pour moderniser l''Etat fran&ccedil;ais :  c''est ce qui ressort d''un entretien avec Alexis Monville,&nbsp;Responsable du sch&eacute;ma directeur administration &eacute;lectronique au sein de la DGME.</p>', '', '<p><strong>Est ce que vous pourriez dans un premier temps nous pr&eacute;senter votre profil ainsi que celui de votre organisation?</strong></p>\r\n<p><em>&nbsp;</em>&nbsp;Je m''appelle Alexis Monville et je suis &laquo;&nbsp;Responsable du sch&eacute;ma directeur administration &eacute;lectronique&nbsp;&raquo;. Le Sch&eacute;ma Directeur Administration &Eacute;lectronique est une mise en coh&eacute;rence suivant trois grands principes :</p>\r\n<p>&nbsp;</p>\r\n<ul>\r\n<li>Simplifier la relation de l''usager &agrave; l''administration, </li>\r\n<li>Am&eacute;liorer l''efficience du service public, </li>\r\n<li>Valoriser l''agent dans sa mission. </li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p>Une structuration suivant de grandes initiatives de trois natures :</p>\r\n<p>&nbsp;</p>\r\n<ul>\r\n<li>Initiatives Sectorielles : &Eacute;ducation, Sant&eacute;, Justice, Diplomatie, S&eacute;curit&eacute;, Emploi... </li>\r\n<li>Initiatives Nouveaux Services : par cible (le citoyen, l''entreprises...), par &eacute;v&eacute;nement de vie (d&eacute;m&eacute;nagement, naissance...) </li>\r\n<li>Initiatives Socle Commun : infrastructures (r&eacute;seaux, production...), services de confiance (identit&eacute; num&eacute;riques, certificats...), techniques (d&eacute;veloppement informatique, gestion des processus, SIG...) et fonctionnelles (archivage, ressources humaines, finances...). </li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p>Alimentant et utilisant des r&eacute;f&eacute;rentiels g&eacute;n&eacute;raux ayant force de loi sur l''interop&eacute;rabilit&eacute;, la s&eacute;curit&eacute; et l''accessibilit&eacute;, Cr&eacute;er dans le cadre d''une concertation, orientant vers la coop&eacute;ration des organisations du service public, s''appuyant sur un dispositif de mutualisation dont la porte d''entr&eacute;e public est Synergies &ndash; le r&eacute;seau des ressources ADELE (http://synergies.modernisation.gouv.fr).</p>\r\n<p><strong>Quelles sont les services propos&eacute;s dans le cadre de ce dispositif ?</strong></p>\r\n<p>Pour l''instant nous avons surtout mis en place des outils pour la coordination : il s''agit principalement du site Internet <a href="http://synergies.modernisation.gouv.fr/">Synergies</a> r&eacute;alis&eacute; avec SPIP-Agora.  Les trois autres outils sont :</p>\r\n<p>&nbsp;</p>\r\n<ul>\r\n<li><a href="http://admisource.gouv.fr/">une forge</a>. Tous les services peuvent d&eacute;poser les projets Open Source qui les int&eacute;ressent. On y retrouve aussi bien des frameworks de d&eacute;veloppement que des applications m&eacute;tier. Par exemple EDI2MIF : il s''agit d''un convertisseur simple permettant la traduction des fichiers du cadastre num&eacute;rique au format EDIG&eacute;O PCI vers le format d''&eacute;change g&eacute;n&eacute;raliste SIG MIF/MID. </li>\r\n</ul>\r\n<ul>\r\n<li><a href="http://https//www.ateliers.modernisation.gouv.fr/">une plateforme d''animation de communaut&eacute;</a>. Elle propose un&nbsp;environnement d&eacute;mat&eacute;rialis&eacute; et permet le travail collaboratif multi-sites.</li>\r\n</ul>\r\n<ul>\r\n<li><a href="http://projets.admisource.gouv.fr/cybeo/">une plate-forme de e-formation</a>. Il s''agit d''une initiative originale : initialement propos&eacute;e par la soci&eacute;t&eacute; CybEOsphere, l''Etat en a acquis la propri&eacute;t&eacute; apr&egrave;s une liquidation : le code source du projet est d&eacute;sormais Open Source. </li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p><strong>Apparement la grande majorit&eacute; de votre plate-forme tourne grace au logiciel Open Source. Pourquoi ? Choix technique ou financier ?</strong></p>\r\n<p>Nous sommes avant tout des pragmatiques, notre motivation est de mettre en oeuvre une solution correspondant &agrave; notre besoin : c''est toujours la convergence de plusieurs raisons qui nous am&egrave;nent sur un logiciel, qu''ils soient Open Source ou non. Il y a d''abord la question des ressources internes, du budget et du temps disponible : on va essayer de trouver un logiciel existant et d''&eacute;viter de r&eacute;-inventer la roue. Avec l''Open Source, les comp&eacute;tences existent sur le march&eacute; local : au niveau du du co&ucirc;t et de la maintenance c''est toujours int&eacute;ressant.</p>\r\n<p><strong>Et par rapport &agrave; PHP, quelle est votre position ?</strong></p>\r\n<p><em>&nbsp;</em> Les cycles de d&eacute;veloppement sont tr&egrave;s longs : la plupart des administration ont fait le choix de Java il y a plusieurs ann&eacute;es. Les &eacute;quipes sont form&eacute;es, les cadres de d&eacute;veloppement existent... Changer de technologie ou en ajouter une demande des efforts et du temps.   Pour en revenir &agrave; PHP, cette technologie a largement &eacute;volu&eacute; depuis les pages personnelles. C''est devenu une vraie option strat&eacute;gique : il y a plusieurs &eacute;tudes en cours avec des industriels - en particulier suite &agrave; un s&eacute;minaire que nous avons mis en place au mois de juin 2006. En ce moment le projet "Presto" est assez repr&eacute;sentatif de ce mouvement : des alternatives existent en dehors de Java et de Dot Net.  Le travail de l''Adullact est aussi tr&egrave;s int&eacute;ressant : plus de 80% des projets qu''ils h&eacute;bergent sont en PHP. Le choix de cette technologie se fait indirectement : ce sont avant tout les fonctionnalit&eacute;s du logiciel ou du produit qui vont faire pencher la balance.</p>\r\n<p><strong>Pouvez-vous lister rapidement les diff&eacute;rents projets / applications dans lesquels vous utilisez PHP aujourd''hui ?</strong></p>\r\n<p><em>&nbsp;</em> Nous utilisons bien s&ucirc;r SPIP Agora. GForge et WordPress font aussi parti de notre bo&icirc;te &agrave; outils. Pour les CMS une &eacute;tude est en cours : la liste est tr&egrave;s longue et PHP y est tr&egrave;s pr&eacute;sent.</p>', 0, 1164063600, 1, 0);
INSERT INTO `afup_site_article` (`id`, `id_site_rubrique`, `surtitre`, `titre`, `raccourci`, `descriptif`, `chapeau`, `contenu`, `position`, `date`, `etat`, `id_personne_physique`) VALUES
(322, 58, '', '29/01/2007 - Conférence sur la sécurité', '29-01-2007-conf-rence-sur-la-s-curit', 'Avec son statut de langage dominant sur le Web, PHP est une cible de choix pour les pirates. Lors de cette conférence, Damien Séguy nous propose de parcourir les techniques d''attaque et de défense, en approfondissant les techniques de type XSS et CSRF.', 'Damien Séguy est membre du PHPGroup, co-fondateur de nexen.net et rédacteur en chef de Direction|PHP. Depuis de nombreuses années il consacre son temps et ses efforts à PHP, dans le travail et les loisirs. ', '-* Date et heure : <strong>le lundi 29 janvier 2007 à 20h15</strong>\r\n-* Lieu : <strong><a href="http://maps.google.fr/maps?f=q&hl=fr&q=177+rue+de+Charonne%2C+75011+Paris">AGECA - 177 rue de Charonne - 75011 Paris</a></strong>, salle Paris\r\n\r\n<h3><a href="https://afup.org/rdv_afup/">&gt;&gt;&gt; S''inscrire à la conférence</a></h3>\r\n\r\nDepuis 2005, la sécurité est un point crucial pour les applications Web en général et PHP en particulier. Avec son statut de langage dominant sur le Web, PHP est une cible de choix pour les pirates. \r\n\r\nLors de cette conférence, vous aurez un bilan des problèmes de sécurité qui se présentent aux applications Web écrites en PHP et MySQL, les techniques d''attaques et les défenses à mettre en place, ainsi que les concepts de protections des applications. Avec le regard exercé d''un hébergeur reconnu.\r\n\r\nDamien Séguy nous propose en particulier de parcourir les techniques d''attaque et de défense, en approfondissant les techniques de type <a href="http://fr.wikipedia.org/wiki/XSS">XSS</a> et <a href="http://fr.wikipedia.org/wiki/CSRF">CSRF</a>.\r\n\r\n<doc13|center>', 0, 1168941042, 1, NULL),
(319, 9, '', '[01 net] Le Zend Framework, prêt à fédérer la communauté PHP ?', '01-net-le-zend-framework-pr-t-f-d-rer-la-communaut-php', 'Article 01Net du 13/12/2006.\r\nStandardiser, simplifier et industrialiser les développements : Zend a de grandes ambitions pour son framework, qui propose génération de documents PDF, connecteurs vers des services en ligne et support de MVC. Téléchargé plus de 200 000 fois, le projet reste toutefois à l''état de bêta.', '', 'Actuellement, la tendance des outils de développement est aux frameworks web; en effet, ceux-ci fournissent un cadre de travail standard pour le développement d''applications web. Ils utilisent différentes classes qui facilitent la programmation et augmentent la fonctionnalité du langage.\r\n\r\n<a href="http://www.01net.com/">01net</a> propose un article intéressant regroupant plusieurs témoignages sur les débuts du Zend Framework.\r\n\r\n<a href="http://www.01net.com/editorial/336094/article/le-zend-framework-pret-a-federer-la-communaute-php">Lire l''article chez 01net</a>', 0, 1167865200, 1, 0),
(321, 9, '', 'PHP 5 passe à la vitesse supérieur pour finir 2006', 'php-5-passe-la-vitesse-sup-rieur-pour-finir-2006', 'Les statistiques de diffusion PHP dans le monde pour décembre 2006 sont arrivées.\r\n\r\n<ul>\r\n<li>PHP 5 atteint maintenant 13% du parc installé</li>\r\n<li>PHP 5 représente 40% des nouvelles installations PHP</li>\r\n<li>PHP 5.1 commence son déclin, face à PHP 5.2</li>\r\n<li>PHP 4.4.4 : toujours en forme, toujours le plus populaire</li>\r\n</ul>', '', '<p>Ce mois ci, de nouvelles informations sont disponibles :</p>\r\n\r\n<ul>\r\n<li>Les versions qui ont le plus gagné ou perdu de part de marché\r\n</li>\r\n<li>La distribution de PHP par IP (au lieu de domaines)\r\n</li>\r\n</ul>\r\n\r\n<p><a href="http://www.nexen.net/chiffres_cles/phpversion/statistiques_de_deploiement_de_php_de_decembre_2006.php">Statistiques de déploiement de PHP de décembre 2006</a></p>\r\n\r\n<p><a href="http://www.nexen.net/chiffres_cles/phpversion/evolution_de_php_sur_internet_decembre_2006.php">Evolution de PHP sur Internet (décembre 2006)</a></p>', 0, 1168470000, 1, 0),
(324, 9, '', 'Solutions Linux 2007 - Compte rendu télévisé', 'solutions-linux-2007-compte-rendu-t-l-vis', '', 'Lors du salon <a href="http://www.solutionslinux.fr/fr/">Solutions Linux 2007</a> qui s''est tenu au CNIT de la Défense du 30 janvier au 1 février, l''AFUP a interrogé plusieurs entreprises utilisant PHP.', '<p>Dans l''ensemble, le langage PHP se distingue grâce à la rapidité de développement qu''il permet et son évolution rapide soutenue par une communauté forte et volontaire. </p>\r\n\r\n<h3>Que pensent-ils de PHP au sein de leurs activités ?</h3>\r\n\r\n<p>Une question commune posée sur plusieurs stands, à vous de juger / comparer les réponses données par les uns et les autres. </p>\r\n\r\n-* <strong><a href="https://afup.org/pages/video/?0">Qualité normale</a></strong>\r\n-* <strong><a href="https://afup.org/pages/video/?1">Qualité supérieure</a></strong>\r\n\r\n<h3>Quels conseils vous donnent-ils à propos de PHP ?</h3>\r\n\r\n<p>Une question personnalisée a été posée à chaque intervenant. Ils vous répondent sur des sujets aussi variés que la formation, l''utilisation de PHP dans un environnement hétérogène, le choix d''un CMS ou les sujets importants qui intéressent les utilisateurs de PHP.</p>\r\n\r\n-* <strong><a href="https://afup.org/pages/video/?3">Qualité normale</a></strong>\r\n', 0, 1170370800, 1, 0),
(325, 58, '', 'Résumé de la conférence  Zend Framework', 'r-sum-de-la-conf-rence-zend-framework', '', 'Le Zend Framework est un projet open-source écrit en PHP. Sa communauté grandissante et son implémentation fiable à l''image de PHP en fait sans aucun doute un projet d''avenir.', 'C''est une salle comble de la FIAP qui accueillit mercredi 13 décembre 2006 nos trois conférenciers de la soirée AFUP de présentation du Zend Framework, avec, par ordre d''entrée en scène : \r\n·	Guillaume Ponçon, Architecte, Fondateur et Auteur du "Best Practices PHP"\r\n·	Arnaud Limbourg, secrétaire de l''AFUP, contributeur de PEAR et du Zend Framework\r\net, last but not least,\r\n·	Zeev Suravski, cofondateur de Zend, acteur majeur de l''Open Source.\r\n\r\nGuillaume nous a d''abord présenté les quatre pôles qui organisent la synergie d''un framework :\r\n\r\n1 - l''architecture, dont le squelette décrit en UML le plus souvent bâtie sur le MVC (Model - View -Control) articule les rôles, y compris ceux de la maintenance et de la performance.\r\n\r\n2 - les rôles qui régissent l''écriture du code, la syntaxe à respecter et le renommage pour faciliter le travail en équipe.\r\n\r\n3 - les briques qui permettent l''échange grâce à la généricité d''une organisation orientée objet de construire les composants\r\n\r\n4 - les outils qui servent les méthodes de développements, de déploiement et de maintenance tel l''éditeur Zend Studio, PHPUnit2  et Phing.  \r\n   \r\n \r\nArnaud, qui participe activement au Zend Framework(ZF), a exposé  sur l''ouverture du Framework. tout d''abord, au plan juridique : la licence a surtout pour but d''assurer la pérennité de la diffusion et du droit à l''emploi du source. Au plan technique, il s''agit de proposer sans forcer l''utilisation : le ZF charge les classes automatiquement en fonction des besoins.\r\n\r\nLes contributeurs sont organisés en équipes. Les tests unitaires (PHP unit) sont de rigueur.\r\nLa documentation est maintenue dans le code avec Notebook. \r\nEt l''étape de la revue de code est obligatoire avant toute intégration nouvelle dans le Framework.\r\nArnaud a ensuite passé en revue les principaux composants :\r\nZend_controler : le MVC\r\nZend_view : moteur de templates\r\nZend_Db : manipulation de la base de données\r\nZend_feed:flux : RSS et Atom\r\nZend_filter\r\nZend_HTTP\r\nZend_Mail\r\nZend_Mime\r\nZend_PDF\r\nZend_convert\r\nZend_Service\r\nZend_Xmlprc\r\nZend_Conflig\r\nZend_Cache\r\nZend_JSon\r\nOn en trouvera le manuel à http://framework.zend.com/manual\r\nToutes les classes ont une classe dérivée utilisable (pas d''abstraction pure)\r\n\r\nGuillaume est ensuite revenu sur le modèle MVC et en particulier sur le rôle majeur du Controler dont l''implémentation la plus fréquente  est le fichier index php par lequel vont passer toutes les requêtes grâce notamment à l''url-rewriting.\r\n\r\nUne arborescence standard permet de retrouver facilement les différents composants:\r\n-app\r\n-contrôleurs\r\n-models\r\n-views\r\n-event \r\n-indep\r\n-layont\r\n-lib\r\n-www\r\n-css\r\n-images\r\n-indep.php.\r\n-js\r\nPuis Arnaud nous a indiqué que le nouveau router de la version 0.6 a été bâti pour faciliter les tests unitaires : on peut ainsi créer une requête et lancer le test sans passer par le site. \r\n \r\nZend_View est d''autant plus simple que php est lui-même un langage de template à la base.\r\n\r\nEnfin, Zeev nous a fait découvrir une démonstration du ZF en insistant sur les lignes technologiques qui justifient la création de ce nouveau framework, alors qu''il en existe déjà beaucoup. La première ligne est la simplicité extrême : il ne s''agit pas de viser l''universalité mais l''extensibilité. Celle-ci sera d''autant plus assurée que, et c''est la troisième ligne, le code  sera de qualité.\r\nTout cela justifie d''avoir rebâti un nouveau framework depuis la base. \r\n\r\nA la suite de cette conférence très applaudie, de nombreuses questions furent posées :\r\ny aura-t-il un jour ce fameux Active Record dont il avait été question dans la conférence de lancement du Framework en 2005 ?\r\nRéponse : ce n''est plus prévu aujourd''hui, et on s''interroge même sur l''intérêt des Active Records.\r\n\r\nLe Zend Framework est développé par 25 contributeurs dont environ 15 très actifs.\r\n\r\nNous avons eu ensuite quelques informations sur les mailing listes (très actives).\r\n\r\nJ.Data va faire parti du ZF et permettra d''accéder à des services tels le calendrier de Google.\r\n\r\nRigth Design pour développer  un IDE à la Delphi ou VB avec Drag & Drop.\r\n\r\nEnfin ce conseil : pour commencer à travailler avec le Zend Framework, le mieux est de suivre les exemples de la page de téléchargement.\r\n\r\n<em>La soirée s''est terminée par la distribution de livres aux heureux élus d''un tirage au sort.</em>', 0, 1173691718, 1, NULL),
(326, 19, '', 'PHP et IBM, quelles interactions possibles ?', 'php-et-ibm-quelles-interactions-possibles', '<p>IBM est un acteur majeur de l''informatique. Historiquement partisan de Java il s''ouvre cependant &agrave; PHP et propose des interactions &agrave; ses outils. R&eacute;sum&eacute; d''une rencontre entre deux mondes.</p>', '<p><a href="http://www.anaska.com">Anaska</a> est le sp&eacute;cialiste des formations sur les technologies OpenSource en France. En partenariat avec MySQL AB, Talend, Zend et d''autres acteurs de la communaut&eacute;, Anaska propose un catalogue de plus de <a href="http://www.anaska.com/plan.php">50 formations d&eacute;di&eacute;s aux technologies du libre</a> ainsi que des formations de pr&eacute;paration aux certifications Linux, <a href="http://www.anaska.com/certification-mysql.php">MySQL</a>, <a href="http://www.anaska.com/certification-php.php">PHP</a> et bient&ocirc;t PostgreSQL.</p>\r\n<p>Anaska propose aussi aux entreprises une gamme compl&egrave;te de services professionnels,  Anaska Services, qui les accompagnent dans le projet de transformation de leur Syst&egrave;me d''Information. Cette gamme de services s''&eacute;tend du conseil &agrave; l''assistance technique en passant par l''ing&eacute;nierie qui en constitue le coeur.</p>', '<p>Le 19 Janvier 2007 suite &agrave; quelques &eacute;changes de mails et gr&acirc;ce &agrave; la participation de l''association <a href="http://www.gsefr.org/">guide Share</a> j''ai (<a href="https://afup.org/auteur.php3?id_auteur=9">Cyril PIERRE de GEYER</a>) rendez vous avec Christian Griere (IBM) pour faire le point sur les interactions possibles entre PHP et IBM.</p>\r\n<p><strong>R&eacute;sum&eacute; rapide</strong></p>\r\n<p>Il est possible d''acc&eacute;der &agrave; la base de donn&eacute;es DB2 sans probl&egrave;mes, soit via PDO soit via les connecteurs ibm_db2. La grande nouveaut&eacute; c''est qu''il est &eacute;galement possible de faire appel &agrave; des programmes RPG, Cobol, CL, Java via l''i5 toolkit qu''a fait d&eacute;velopper IBM (<a href="http://www-03.ibm.com/systems/i/software/php/">+ d''infos</a>).</p>\r\n<p><strong>Qu''est ce que le system i ?</strong></p>\r\n<p>&laquo; AS/400, iSeries, System i &raquo; sont des d&eacute;nominations commerciales pour d&eacute;signer les diff&eacute;rentes &eacute;volutions du System/38. Jusqu''en 1995 le syst&egrave;me et les applications utilisaient une architecture CISC 48 bits. En 1995 le syst&egrave;me et les applications  sont pass&eacute;s sur une architecture RISC 64 bits.</p>\r\n<p>Cette machine a &eacute;t&eacute; con&ccedil;ue en 1975 avec pour objectif de profiter de l''exp&eacute;rience des mainframes:</p>\r\n<ul>\r\n<li>Syst&egrave;me et applicatif ind&eacute;pendants du mat&eacute;riel. </li>\r\n<li>Notion d''espace adressable unique. </li>\r\n<li>Ne plus avoir besoin de g&eacute;rer les probl&egrave;mes de taille et d''unit&eacute; physique.</li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>A la base pr&eacute;vue pour les grandes entreprises la machine ne leur a pas &eacute;t&eacute; propos&eacute;e car les co&ucirc;ts de migration &eacute;taient trop importants. IBM a alors affect&eacute; cette machine au march&eacute; des PME.</p>\r\n<p><strong>Fonctionnement</strong></p>\r\n<p>IBM peut &agrave; tout moment changer de type de processeur  car les applications utilisent un code interm&eacute;diaire (sorte de bytecode) ind&eacute;pendant du mat&eacute;riel.</p>\r\n<p>On peut travailler soit en ligne de commande. +/- 2-3000 fonctions, soit en client /serveur soit en mode Web.</p>\r\n<p>La base de donn&eacute;es (DB2 for System i) est int&eacute;gr&eacute;e &agrave; l''OS. Elle respecte le core level de SQL 2003.</p>\r\n<p>Interface 5250 : Interface texte (la plus utilis&eacute;e historiquement). Interface graphique : Operation Navigator</p>\r\n<p>Sur un System i on peut mettre plusieurs types d''OS (i5/OS, AIX, LINUX) sur des partitions logiques diff&eacute;rentes. Pour chaque partition on associe des ressources processeur, m&eacute;moire et cartes d''entr&eacute;e/sortie. Ces ressources peuvent &ecirc;tre affect&eacute;es dynamiquement entre les partitions. De plus l''hyperviseur  est capable d''affecter en temps r&eacute;el &agrave; une partition les cycles cpu non utilis&eacute;s d''une autre partition.</p>\r\n<p>Le nombre de processeur du serveur physique va de 1 &agrave; 64 et sa puissance de traitement applicatif peut &eacute;voluer entre 1 et 300.</p>\r\n<p><strong>Comment communiquer entre PHP et un &laquo; System i &raquo; ?</strong></p>\r\n<p>Il existe trois possibilit&eacute;s pour communiquer entre PHP et un &laquo; system i &raquo; :</p>\r\n<ul>\r\n<li>PHP --&gt; ibm_db2 --&gt; Base de donn&eacute;es DB2 for System  i </li>\r\n<li>PHP --&gt; ODBC   --&gt; Base de donn&eacute;es DB2 for System i </li>\r\n<li>PHP --&gt;  i5 PHP Toolkit --&gt; Appel de programme RPG, COBOL, CL, Java </li>\r\n</ul>\r\n<p>Les deux premi&egrave;res m&eacute;thodes sont  classiques il s''agit d''une connexion directe &agrave; la base de donn&eacute;es DB2 via ibm_db2 ou ODBC.</p>\r\n<p>La troisi&egrave;me m&eacute;thode permet un interfa&ccedil;age plus pouss&eacute; entre PHP et le &laquo; system i &raquo; via un connecteur d&eacute;velopp&eacute; par une soci&eacute;t&eacute; fran&ccedil;aise (Aura Equipement) pour IBM via Zend.</p>\r\n<p>Les informations sur les deux  m&eacute;thodes &eacute;tant classiques et facilement trouvables nous allons d&eacute;tailler un peu plus la troisi&egrave;me.</p>\r\n<p><strong>Installation du i5 PHP Toolkit</strong></p>\r\n<p>Il faut installer sur le &laquo; System i &raquo; un programme qui s''appelle ZendCore for  i5/Os. ZendCore for i5/OS fonctionne avec l''i5/OS V5R3 et V5R4. Ce produit doit &ecirc;tre command&eacute; &agrave; IBM. Il est gratuit ainsi qu''un support Web pendant 3 ans.</p>\r\n<p><strong>Utilisation</strong></p>\r\n<p>L''extension i5 permet de faire appel &agrave; plein de nouvelles fonctions. L''exemple suivant nous montre comment faire appel &agrave; un programme RPG.</p>\r\n<pre><code>\r\n<!--?php\r\n\r\n$system = "localhost";\r\n\r\n$user = "PHPDEMO";\r\n\r\n$password = "PHPDEMO";\r\n\r\n$pgm_name = "PHPDEMO/DET_FILM";\r\n\r\n$parm_in=array("CODE"=-->$_GET[''codefilm'']);\r\n\r\n$name_parm_out = array("CODE"=&gt;"CODE_FILM", "TITRE"=&gt;"TITRE_FILM");\r\n\r\n$conn = i5_connect($system, $user, $password);\r\n\r\n$prepare = i5_program_prepare($pgm_name, $pgm_desc);\r\n\r\n$call = i5_program_call($prepare, $parm_in, $name_parm_out);\r\n\r\ni5_program_close($prepare);\r\n\r\ni5_close($conn);\r\n\r\n?&gt;\r\n</code></pre>\r\n<p><strong>Quelques tests</strong></p>\r\n<p>Il est &eacute;galement possible de faire appel &agrave; des commandes sur le &laquo; system i &raquo; directement.</p>\r\n<pre><code>\r\n<!--?php\r\n\r\n$system = "localhost";\r\n\r\n$user = "PHPDEMO";\r\n\r\n$password = "PHPDEMO";\r\n\r\n$conn = i5_connect($system, $user, $password);\r\n\r\nif (!$conn) die("Erreur lors du i5_connect : ".i5_errormsg());\r\n\r\ni5_command("rtvjoba", array(), array ("user" =--> "usertest","date"=&gt;"datetest"),$conn);\r\n\r\nprint_r($usertest);\r\n\r\nprint_r($datetest);\r\n\r\n?&gt;\r\n</code></pre>\r\n<p><strong>Test de lecture des valeurs syst&egrave;mes</strong></p>\r\n<p>Il est possible de modifier les valeurs syst&egrave;mes. Pour acc&eacute;der &agrave; ces valeurs on utilise i5_get_system_value()</p>\r\n<pre><code>\r\n<!--?php\r\n\r\n$system = "localhost";\r\n\r\n$user = "PHPDEMO";\r\n\r\n$password = "PHPDEMO";\r\n\r\n$conn = i5_connect($system, $user, $password);\r\n\r\nif (!$conn) die("Erreur lors du i5_connect : ".i5_errormsg());\r\n\r\necho i5_get_system_value("QPWDMINLEN");\r\n\r\n?-->\r\n</code></pre>\r\n<p><strong>Test par rapport aux DATA AREA</strong></p>\r\n<p>Il est possible de cr&eacute;er un object de type DATA AREA.. Cela consiste en une zone de stockage persistante de type caract&egrave;re ou d&eacute;cimal. Souvent la DATE AREA est utilis&eacute;e pour stocker des num&eacute;ros de facture. Cela permet d''y acc&eacute;der plus facilement.</p>\r\n<p><strong>Test par rapport aux DATA QUEUE</strong></p>\r\n<p>C''est une structure qui permet d''empiler des donn&eacute;es. Cela permet de g&eacute;rer des processus asynchrones. Ces informations sont g&eacute;n&eacute;ralement utilis&eacute;es par les diff&eacute;rentes applications pour communiquer. Pas pour les utilisateurs.</p>\r\n<pre><code>\r\necho i5_data_area_read("DEGEYERBIB/TAB");\r\n</code></pre>\r\n<p>Test cr&eacute;ation/&eacute;criture par API et relecture d''un USER SPACE. Il est possible de faire appel &agrave; des API syst&egrave;me qui mettent les r&eacute;sultats dans un USER SPACE.</p>\r\n<p>Pour plus d''information :  <a href="http://www.anaska.com">Cyril PIERRE de GEYER / Soci&eacute;t&eacute; Anaska</a> <a>(contact@anaska.com)</a></p>', 0, 1170630000, 1, 0),
(327, 58, '', '06/03/2007 - Framework Symfony', '06-03-2007-framework-symfony', 'Le framework <a href="http://www.symfony-project.com/">Symfony</a> est un important projet de framework. Il se distingue de ses concurrents par ses nombreux outils de développement haut niveau qui permettent de réaliser des applications rapidement et simplement. ', 'Fabien Potencier est l''un des principaux développeurs du framework Symfony. Il sera présent pour répondre à vos questions aussi bien techniques que stratégiques.', '-* Date et heure : <strong>le mardi 06 mars 2006 à 20h00</strong>\r\n-* Lieu : <strong><a href="http://maps.google.fr/?hl=fr&near=30%20rue%20cabanis%20-%20paris">Espace FIAP - 30 rue Cabanis - 75014 Paris</a></strong>\r\n\r\nSymfony est composé de nombreux modules permettant l''interopérabilité, la réutilisabilité et l''adoption de technologies en vogue telles que AJAX. Fabien Potencier nous propose lors de cette conférence une présentation / démonstration de ce framework qui est aujourd''hui largement utilisé dans le monde professionnel et associatif.\r\n\r\n<h3>>>> <a href="https://afup.org/rdv_afup/">S''inscrire à la conférence</a></h3>\r\n\r\n', 0, 1171987538, 1, NULL),
(328, 58, '', 'Mise en ligne des supports de la conférence Framework Symfony', 'mise-en-ligne-des-supports-de-la-conf-rence-framework-symfony', 'Le 6 mars 2007, Fabien Potencier (Leader technique du projet Symfony et Directeur Général de la société Sensio Labs) a présenté une conférence sur le Framework Symfony.', '', 'Les supports du rendez-vous AFUP sur le <a href="http://327">Framework Symfony</a> qui a eu lieu le 6 mars 2007 sont à présent disponibles en ligne.\r\n\r\n<a href="https://afup.org/docs/conf_symfony.pdf">Télécharger les supports au format PDF.</a>', 0, 1173692128, 1, NULL),
(332, 9, '', 'Statistiques PHP / Janvier 2008 : PHP 5 à 30 % !', 'statistiques-php-janvier-2008-php-5-30', 'Les statistiques de diffusion PHP dans le monde pour Janvier 2008 sont arrivées. PHP 5 continue sa percée.', ' ', '<ul>\n<li>* PHP 5 en grande forme, presque à 30 %\r</li>\n<li>PHP 5.2 va dépasser PHP 4.3 avant avril\r</li>\n<li>PHP 5.2.5 en tête du marché PHP \r</li>\n<li>PHP 4.4.8 passe inaperçu\r</li>\n</ul>\n\r\n<a href="http://www.nexen.net/chiffres_cles/phpversion/18088-evolution_de_php_sur_internet_janvier_2008.php">Evolution de PHP sur Internet (Janvier 2008)</a>', 0, 1203030000, 1, NULL),
(335, 19, '', 'Philips : la meilleure arme est généralement PHP', 'philips-la-meilleure-arme-est-g-n-ralement-php', '<p>Un entretien avec Pascal Vogels. D&eacute;veloppeur logiciel pour le d&eacute;partement Software Engineering Services (SES) de Philips Research Eindhoven aux Pays-Bas, il expose les usages de PHP au sein de la c&eacute;l&egrave;bre soci&eacute;t&eacute; d''&eacute;lectronique grand public et de leur contribution au mouvement Open Source.</p>', '', '<p><strong>Bonjour, commen&ccedil;ons par une petite introduction sur vous et ce sur quoi vous travaillez ?</strong></p>\r\n<p>Je m''appelle Pascal Vogels, d&eacute;veloppeur logiciel pour le d&eacute;partement Software Engineering Services (SES) de Philips Research Eindhoven aux Pays-Bas. Comme vous pouvez le penser, Philips Research est la division de recherche de Philips.</p>\r\n<p><strong>Pouvez-vous d&eacute;tailler ce que fait votre soci&eacute;t&eacute; ? Et en quoi vos projets contribuent &agrave; ce but ?</strong></p>\r\n<p>Philips produit de nombreuses choses innovantes, allant de produits &eacute;lectroniques grand public &agrave; des outils m&eacute;dicaux professionels. Les bases de ces innovations viennent de notre division de recherche.</p>\r\n<p>Le d&eacute;partement SES supporte le d&eacute;partement de recherche en d&eacute;veloppant des logiciels, principalement pour des prototypes et des d&eacute;monstrateurs. Nous faisons aussi un peu de logiciels d''infrastructure ici et l&agrave;.</p>\r\n<p><strong>Quel genre d''applications faites-vous avec PHP ? Avez-vous un exemple marquant ?</strong></p>\r\n<p>Si la demande concerne un logiciel web, la meilleure arme est g&eacute;n&eacute;ralement PHP. L''exemple le plus marquant est probablement "Project Planning &amp; Tracking System" (ou PPTS pour faire court), qui est un envirronement open-source web supportant les pratiques XP@Scrum[[les techniques XP pour la gestion de l''&eacute;quipe et du code, les techniques Scrum pour le pilotage de projet]].</p>\r\n<p>PPTS offre par exemple des fonctionnalit&eacute;s comme l''allocation de ressources, le backlog (ou pile des fonctionnalit&eacute;s &agrave; mettre en place), le d&eacute;coupage des t&acirc;ches, le calcul de v&eacute;locit&eacute;, le burndown chart et autres graphs de progression, un support multilingue, des interfaces avec Bugzilla et Mantis, plusieurs m&eacute;triques requises par CMM, etc...</p>\r\n<p>Nous l''avons d&eacute;velopp&eacute; il y a deux ans pour un besoin interne, puis d&eacute;cid&eacute; de le rendre open-source ensuite. Il peut &ecirc;tre trouv&eacute; sur SourceForce : <a href="http://sourceforge.net/projects/ses-ppts/">http://sourceforge.net/projects/ses-ppts/</a></p>\r\n<p><strong>Quelles sont les caract&eacute;ristiques de votre infrastructure technique ?</strong></p>\r\n<p>Avec PHP, nous g&eacute;rons souvent des documents XML, cr&eacute;ons des connexions FTP, nous interfa&ccedil;ons avec des serveurs LDAP, utilisons les bases de donn&eacute;es Mysql, PostgreSLQ ou MSSQL, g&eacute;n&eacute;rons des fichiers PDF et des images. Et nous avons r&eacute;cemment plong&eacute; dans Ajax.</p>', 0, 1180303200, 1, 0),
(337, 9, 'Tester la compatibilité de vos applications sous PHP 5', 'La fin du support de PHP 4 est annoncé', 'la-fin-du-support-de-php-4-est-annonc', 'Le PHPGroup annonce la fin programmée du support de PHP 4 au profit de PHP 5 et du futur PHP 6.', 'Vous pouvez facilement tester la compatibilité PHP 5 de vos applications (sous Windows) en utilisant l''<a href="http://www.wampserver.com">auto installeur PHP MySQL WampServer</a> qui, installé avec son <a href="http://www.wampserver.com/add-ons.php">module PHP 4</a>, permet de switcher de PHP 4 vers PHP 5 et vice versa.', 'Le 13 Juillet 2007 cela fera exactement trois ans que PHP 5 est en version de production. Durant ces trois années de nombreuses améliorations ont été faites pour PHP 4.\r\n\r\nPHP 5 est rapide, stable et apte à la production. De plus PHP 6 est en cours de réalisation.\r\n\r\nPour toutes ces raisons le PHPGroup annonce que le support de PHP 4 ne va continuer que jusqu''à la fin de l''année. Après le 31 décembre 2007 il n''y aura plus de nouvelles versions de PHP 4.4. Bien entendu les éventuelles failles de sécurités seront traitées mais au cas par cas jusqu''au 8 août 2008.\r\n\r\nProfitez de ce délai pour valider que vos applications sont compatibles PHP 5. \r\n\r\nDe la documentation sur la migration de PHP 4 à PHP 5 est disponible sous la forme d''un <a href="http://www.php.net/manual/fr/migration5.php">guide de migration</a>. \r\n\r\nLa migration des applications d''entreprises peut être accompagnée par l''un des nombreux prestataires PHP. Une liste non exhaustive est disponible sur <a href="https://afup.org/pages/annuaire/">l''annuaire PHP</a> de l''AFUP.\r\n', 0, 1186228214, 1, NULL),
(338, 58, 'Mike Potter, l''expert mondial PHP/Flex, sera présent !', '[11/10/2007] Conférence gratuite : Clients Web riche avec PHP et Flex', '11-10-2007-conf-rence-gratuite-clients-web-riche-avec-php-et-flex', 'L''Association Française des Utilisateurs de PHP s''associe à Adobe et <a href="http://www.anaska.com">Anaska</a> pour vous inviter à un apéro technique gratuit présentant comment créer des applications riches avec PHP et la technologie openSource Adobe Flex. Le tout avec l''expert mondial PHP Flex : Mike POTTER.', 'Mike Potter, l''expert mondial PHP/Flex, sera présent pour présenter des exemples de réalisations où l''expérience utilisateur est dominante. Il en profitera pour présenter les techniques de base pour commencer à connecter une interface Flex à vos applications PHP, ainsi que les nouvelles fonctionnalités de Flex 3 (en beta actuellement) dédiées aux développeurs PHP.', 'Web 2, Web 3 : les applications Internet évoluent et la place des clients riches RIA (Rich Internet Applications) risque de peser lourd dans la balance ces prochaines années (Gartner le considère comme le marché leader de l''applicatif d''ici 4 ans.)\r\n\r\nA ce jour les différentes voies sont XHTML/Ajax, XUL et Flex. PHP, la plateforme Web la plus utilisée, à un rôle majeur à jouer dans l''avancée et l''évolution des applications Internet.\r\n\r\n\r\nL''Association Française des Utilisateurs de PHP s''associe à Adobe pour vous inviter à un apéro technique gratuit présentant comment créer des applications riches avec PHP et la technologie openSource Adobe Flex.\r\n\r\n-* <strong>Clients Web riche avec PHP et Flex (Adobe)</strong>\r\n-* Date et heure : <strong>le jeudi 11 Octobre 2007 à 20h15</strong>\r\n-* Lieu : <strong><a href="http://maps.google.fr/?hl=fr&near=30%20rue%20cabanis%20-%20paris">Espace FIAP - 30 rue Cabanis - 75014 Paris</a></strong>\r\n\r\n<h3><a href="https://afup.org/rdv_afup/">&gt;&gt;&gt; S''inscrire à la conférence</a></h3>\r\n', 0, 1189586825, 1, NULL),
(339, 9, 'L''observatoire des logiciels libre', 'LAMP à l''honneur dans l''observatoire du logiciel libre', 'lamp-l-honneur-dans-l-observatoire-du-logiciel-libre', '<a href="http://www.ob2l.com">L''Observatoire des logiciels libres</a> mesure l''usage réel des logiciels libres en entreprise. Les derniers résultats comparant 2006 et 2007 sont disponibles en ligne. \r\n\r\nLa plateforme LAMP est à l''honneur.\r\n', 'Anaska et IB Groupe Cegos ont créé en 2006 l''Observatoire des logiciels libres. Son objectif : mesurer l''usage réel de ces logiciels en entreprise. Les derniers résultats comparant 2006 et 2007 sont disponibles en ligne. Nous vous résumons les principaux enseignements.\r\n\r\nL''étude révèle des données précises par domaines et volumes de l''activité formation. Voici les principaux enseignements de cette deuxième édition du baromètre (évolution de 2006 à 2007).', 'Issu de l''<a href="http://www.ob2l.com/">observatoire du logiciel libre</a> :\r\n\r\n\r\nPHP continue à s''imposer un peu plus comme la technologie de référence pour construire des applications web en entreprise. \r\n\r\n« Entre le premier semestre 2006 et le premier semestre 2007 : un nombre croissant de DSI font le choix de parler de PHP en tant que solution possible pour leurs\r\napplications critiques » explique Cyril Pierre de Geyer, co-fondateur d''<a href="http://www.anaska.com">Anaska</a>. \r\n\r\nPreuve de ce succès, IB-Groupe Cegos forme désormais plus de personnes sur PHP que sur ASP.NET. Le nombre de personnes formées augmente à la fois chez IB-Groupe Cegos (+55%) et Anaska (+27%).\r\n\r\nLes cursus ont peu évolué depuis 2006, si ce n''est l''abandon des formations PHP 4 (en fin de vie) au profit d''un catalogue centré sur PHP 5. Les cursus restent centrés sur des formations avancées :  <a href="http://www.anaska.com/formations/formation-architecte-php-bonnes-pratiques-php.php">bonnes pratiques PHP</a> et <a href="http://www.anaska.com/formations/formation-optimisation-php.php">optimisation PHP</a>. Chez un généraliste comme IB-groupe Cegos, le nombre de stagiaires croît plus vite (+50%) que chez un spécialiste comme Anaska. \r\n\r\nCette technologie arrive donc en phase de maturité dans toutes les entreprises, pas seulement les pionniers dans l''adoption des logiciels libres. Autre preuve de cette arrivée à maturité, le nombre de certifications progresse. «C''est un plus pour mon entreprise. Cela permet de valider nos connaissances internes acquises au fur et à mesure des années. J''ai souhaité m''y préparer avec Anaska pour profiter de leur expertise reconnue et pour rencontrer d''autres professionnels du domaine» explique Rui Albuquerque, ingénieur concepteur, X-Prime, agence de communication et de marketing spécialisée dans les nouveaux médias.\r\n\r\n<ul>\n<li>Progression sur un an : +40%\r</li>\n<li>Niveau de maturité des entreprises : 4/5\r</li>\n<li>Formation avancées : oui\r</li>\n</ul>\n\r\n\r\nL''avis du formateur : « Il y a de plus en plus d''applications métiers développées avec PHP. De nombreux stagiaires viennent se perfectionner car leurs applications se sont enrichies avec le temps et sont devenues critiques. »\r\nJulien PAULI, <a href="http://www.anaska.com/formation-php.php">Formateur PHP</a> chez Anaska et administrateur du site sur le Zend Framework <a href="http://www.z-f.fr">www.z-f.fr</a>.\r\n\r\n\r\n', 0, 1190708713, 1, NULL),
(340, 9, '', 'Les podcasts des conférences du Forum PHP 2007', 'les-podcasts-des-conf-rences-du-forum-php-2007', 'L''AFUP vous propose les sessions du Forum PHP 2007 en Podcast !', '', 'L''AFUP est heureuse de vous proposer les sessions plénières du Forum PHP 2007 en podcast !\r\n\r\n\r\nVous pouvez souscrire au flux rss à l''adresse suivante: <a href="http://feeds.feedburner.com/forumphp2007">http://feeds.feedburner.com/forumphp2007</a>\r\n\r\nVous pouvez également écouter les sessions individuelles sur la page des résumés: <a href="https://afup.org/pages/forumphp2007/resumes.php">https://afup.org/pages/forumphp2007/resumes.php</a>\r\n\r\nBonne écoute à tous !', 0, 1196168493, 1, NULL),
(343, 9, '', 'Éclosion de Mantis 1.1.0', 'closion-de-mantis-1-1-0', 'Mantis est un logiciel libre (GPL) collaboratif de suivi de bugs (BT pour « Bug Tracker ») écrit en PHP. Victor Boctor, l''un des développeurs principaux, vient d''annoncer la version 1.1.0 du mantoptère, à l''issue d''une période de gestation, de développement et de stabilisation de 15 mois depuis septembre 2006 passant par quatre versions alpha et trois versions candidates (release candidate).', '', 'Bien que le numéro de version ne progresse que d''un .1 depuis février 2006, Mantis 1.1 apporte un grand nombre d''évolutions :\r\n\r\n<ul>\n<li>Inclusion de MantisConnect (une API SOAP) ;\r</li>\n<li>Intégration Wiki (dokuwiki, mediawiki, xwiki) ;\r</li>\n<li>Email queuing ;\r</li>\n<li>Intégration des Gravatars ;\r</li>\n<li>Prise en charge de DB2 ;\r</li>\n<li>Tagging ;\r</li>\n<li>Filtrage des permaliens ;\r</li>\n<li>Suivi temporel ;\r</li>\n<li>Intégration Twitter ;\r</li>\n<li>Prise en charge du codage de caractères UTF8 ;\r</li>\n<li>Page de configuration générique ;\r</li>\n<li>Visualisation des derniers bugs visités ;\r</li>\n<li>Compatibilité XHTML ;\r</li>\n<li>RSS authentifié.\r</li>\n</ul>\n\r\n<a href="http://www.mantisbt.org/">Le site de Mantis</a>\r\n\r\nLa liste des fonctionnalités est devenue très complète, avec entre autre : 68 localisations, changelog et roadmap, recherche en texte, rapports, champs personnalisés, notifications par email, flux RSS, cycle de vie éditable, sponsoring (bounties et paiements), captcha, pièces jointes avec prévisualisation, données publiques et privées, intégration LDAP et AD, prise de charge de multiples SGBDR, etc. Ce qui fait de Mantis un bug tracker qui devrait satisfaire de très nombreuses équipes de différentes tailles à moins de besoins spécifiques.\r\n', 0, 1198320039, 1, NULL),
(345, 9, 'Objectif de l''observatoire du libre - indicateur formation Anaska / ib - groupe Cegos', 'PHP, la technologie de référence pour le Web', 'php-la-technologie-de-r-f-rence-pour-le-web', '<a href="http://www.ob2l.com">L''observatoire du logiciel libre</a> vient de sortir sa troisième édition. Celle-ci analyse le marché et dresse un bilan de 2007. La technologie PHP y est cité comme la technologie de référence pour le Web et elle est assimilée à une technologie mature et fiable.', 'Faire un point tous les 6 mois sur le marché du logiciel libre en se basant sur les mouvements du marché de la formation. La complémentarité d''<a href="http://www.anaska.Com">Anaska (spécialiste de l''open source)</a> et d''ib - groupe Cegos (généraliste de la formation informatique) permet de conforter les tendances mesurées. Elles arrivent d''abord chez Anaska puis se confirment chez ib - groupe Cegos.', '<a href="http://www.ob2l.com"><img17|center></a>\r\n\r\n\r\nQuelques extraits :\r\n\r\n\r\n"La plate-forme PHP continue à s''imposer comme la technologie de référence pour construire des sites et applications web en entreprise. Mais « les développeurs maîtrisent désormais tous PHP en sortant de l''école. Les formations ont donc commencé à se déporter sur des briques techniques plus nouvelles et de plus haut niveau comme les frameworks (<a href="http://www.anaska.com/formations/formation-zend-framework.php">Zend Framework</a>, <a href="http://www.anaska.com/formations/formation-symfony.php">Symphony</a>, etc.) et surtout les <a href="http://www.anaska.com/formations/formation-ajax-javascript-web-2-maitrise.php">frameworks AJAX</a> associés » explique Cyril\r\nPierre de Geyer, co-fondateur d''<a href="http://www.anaska.com">Anaska</a>.\r\n\r\n\r\nLes cursus restent centrés sur des formations avancées : bonnes pratiques et optimisation. Chez un généraliste comme IB-groupe Cegos, le nombre de stagiaires croît plus vite (+50%) que chez un spécialiste comme <a href="http://www.anaska.com">Anaska</a>. Cette technologie arrive donc en phase de maturité dans toutes les entreprises, pas seulement les pionniers dans l''adoption des logiciels libres."\r\n', 0, 1201215600, 1, 0),
(346, 58, '', '25/03/2008 - Outiller la qualité PHP', '25-03-2008-outiller-la-qualit-php', 'Stratégie, réflexes et bonnes pratiques pour un développement web durable.', 'Miguel Lopez est le créateur de la société Algorismi, spécialisée sur la qualité logiciel (C, Java, PHP et autres). Il a aussi été professeur / chercheur sur cette thématique à l''Université de Namur - Belgique.', 'Un site qui fonctionne, c''est bien. un site qui dure, c''est mieux. Une présentation d''une heure suivi d''un retour d''expérience, sur les réflexes et les bonnes pratiques d''un développement web durable.\r\n\r\nNous verrons quels sont les stratégies possibles pour garantir la Capacité fonctionnelle, la Fiabilité, la Facilité d''utilisation, le Rendement, la Maintenabilité et la Portabilité.\r\n\r\nInfos pratique :\r\n\r\n-* Date et heure : <strong>le mardi 25 mars 2008 à 20h00</strong>\r\n-* Lieu : <strong><a href="http://maps.google.com/maps?f=q&hl=en&geocode=&q=151+rue+Montmartre,+Paris&sll=37.09024,-95.712891&sspn=35.494074,58.798828&ie=UTF8&ll=48.870685,2.342706&spn=0.007198,0.014355&z=16&iwloc=addr">La Cantine - 151 rue Montmartre, 12 passage Montmartre - Galerie des Panoramas, 75002 Paris</a></strong>\r\n\r\n<h3>>>> <a href="https://afup.org/pages/rendezvous/">S''inscrire à la conférence</a></h3>\r\n', 0, 1204475047, 1, NULL),
(412, 9, '', 'Experts PHP : participez au Forum PHP 2010 !', 'experts-php-participez-au-forum-php-2010', '<p>Prenez activement part au 15&egrave;me anniversaire de PHP &agrave; l''occasion du Forum PHP les 9 et 10 Novembre prochain &agrave; la Cit&eacute; des Sciences de La Villette.</p>', '<p>Le forum 2010 sera plac&eacute; sous le signe des <strong>15 ans de PHP</strong> et des <strong>10 ans de l''Afup</strong>. A l''occasion de cet anniversaire, l''Association Fran&ccedil;aise des Utilisateurs de PHP organise  un Forum plus ambitieux que jamais, pr&eacute;voyant de nombreuses conf&eacute;rences et d&eacute;bats, ainsi qu''un espace d''exposition pour les &eacute;quipes de projets libres souhaitant venir &agrave; la rencontre d''un public de professionnels (d&eacute;veloppeurs, d&eacute;cideurs, presse...).</p>', '<p>Vous &ecirc;tes expert sur un domaine, vous avez install&eacute; une ou plusieurs applications PHP (CMS, e-commerce, CRM, GED) dans un contexte sp&eacute;cifique (forte charge, client reconnu, projet innovant) ou bien vous participez &agrave; un projet Open Source li&eacute; &agrave; PHP,  venez partager votre exp&eacute;rience !<br /><br />Pour l''&eacute;dition 2010, les th&egrave;mes particuli&egrave;rement mis en lumi&egrave;re seront les suivants :</p>\r\n<ul>\r\n<li><strong>PHP de A &agrave; Z :</strong> d&eacute;buter en PHP, r&eacute;ussir un projet avec PHP, choisir son h&eacute;bergement...</li>\r\n<li><strong>Outils bas&eacute;s sur PHP :</strong> CMS et CMF, outils de e-commerce et de business, paiement en ligne, CRM et ERP...</li>\r\n<li><strong>Industrialisation de PHP :</strong> performances, tests, authentification centralis&eacute;e, frameworks</li>\r\n<li><strong>Technologies autour de PHP :</strong> Javascript, HTML 5, microformats...</li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p>Pour soumettre votre sujet de conf&eacute;rence, rendez-vous sur <a href="../forumphp2010/appel-a-conferenciers.php">https://afup.org/pages/forumphp2010/appel-a-conferenciers.php</a> et compl&eacute;tez une demande en ligne <strong>avant le 30 Juin 2010</strong>.</p>\r\n<p>Vous souhaitez traiter un autre th&egrave;me ? Vous n''avez pas d''exp&eacute;rience en tant que conf&eacute;rencier ? Vous souhaitez des renseignements sur la logistique que n&eacute;cessite votre participation au Forum ?</p>\r\n<p>Contactez Sarah sur organisation@afup.org</p>', 0, 1277157600, 1, 173),
(411, 9, '', '2ème Barcamp PrestaShop', '2ème Barcamp PrestaShop', '<p>PrestaShop, la solution eCommerce Open Source PHP,&nbsp; organise le deuxi&egrave;me Barcamp &agrave; Paris</p>', '', '<!-- 		@page { margin: 2cm } 		P { margin-bottom: 0.21cm } -->\r\n<p style="margin-bottom: 0cm;">&nbsp;</p>\r\n<p>La date retenue est le 22 juin 2010 &agrave; la Galerie de Nesle de Paris 6 et l''ensemble des utilisateurs PHP sont invit&eacute;s pour ce rendez-vous</p>\r\n<p>Durant cette journ&eacute;e, des ateliers, des conf&eacute;rences, des tables rondes, ainsi que des espaces de discussion libre sont pr&eacute;vus. Vous pourrez rencontrer les d&eacute;veloppeurs, les utilisateurs et partenaires qui utilisent PrestaShop</p>\r\n<p>Le programme de la journ&eacute;e est disponible et les inscriptions sont ouvertes !</p>\r\n<p>&nbsp;</p>\r\n<p><a href="http://barcamp.prestashop.com/programme_du_barcamp">Acc&eacute;der au programme du Barcamp du 22 juin 2010</a></p>', 0, 1274565600, 1, 151),
(350, 58, '', '3 Avril 2008 : Architectures PHP et Premiers contacts avec PHP 6', '3-avril-2008-architectures-php-et-premiers-contacts-avec-php-6', 'Venez participer au rendez vous afup lyonnais du 3 Avril sur les sujets PHP 6 et architectures PHP.', ' La nouvelle mouture de la plateforme star du Web est en cours de préparation : PHP 6 est en vue ! Pourquoi, comment, quelles seront les nouveautés ? Découvrez avec nous ce que le PHPGroup, mené par Rasmus LERDORF et Andrei ZMIEVSKI nous préparent !\r\n<a href="http://www.phpfrance.com/tutoriaux/index.php/2008/02/26/48-les-nouveautes-de-php-6">Lire l''article de PHPFrance</a>', 'Un double Rendez-vous que propose AFUP autour de PHP dans la ville de Lyon.\r\n\r\nCe rendez-vous se décomposera sous la forme de 2 mini conférences d''1 heure chacune avec comme thème :  \r\n\r\nArchitectures PHP, par Jérome Renard : outils et techniques pour organiser son application PHP\r\n\r\nPremiers contacts avec PHP 6, par Damien Seguy : comment se préparer à la future version de PHP\r\n\r\nInfos pratique :\r\n\r\n-* Date et heure : <strong>le jeudi 3 avril 2008 de 19h00 à 21h00</strong>\r\n-* Lieu : prochainement\r\n\r\n<h3>>>> <a href="https://afup.org/pages/rendezvous/">S''inscrire à la conférence</a></h3>\r\n', 0, 1206524435, 1, NULL),
(351, 9, '', 'PHP en vidéo', 'php-en-vid-o', '', '', 'Lors du précédent Salon "solution Linux 2008", 2 reportages ont été réalisés par l''intermédiaire de Guillaume Ponçon de OpenStates pour la communauté PHP concernant les thèmes suivants :\r\n\r\n<ul>\n<li>PHP expliqué par les utilisateurs\r</li>\n</ul>\n\r\n<ul>\n<li>Témoignage des utilisateurs de PHP\r</li>\n</ul>\n\r\nDeux très bonnes réalisations pouvant vous servir de références dans vos futurs discutions\r\n\r\n<a href="http://www.openstates.com/blog/index.php?2008/03/30/90-php-en-video-sur-solution-linux-2008">http://www.openstates.com/blog/index.php?2008/03/30/90-php-en-video-sur-solution-linux-2008</a>', 0, 1206991333, 1, NULL),
(352, 9, 'Ingres - base de données Open Source', '[8 et 16 Avril 2008] Webinar sur Ingres et PHP', '8-et-16-avril-2008-webinar-sur-ingres-et-php', 'PHP permet de communiquer avec toutes les bases de données du marché. Si vous souhaitez en savoir plus sur les interactions possibles entre PHP et la base de données Ingres, venez suivre ce webinar.', 'Ingres est un SGBD relationnel, tout comme DB2, Oracle ou MySQL pour citer les plus connus. Ingres signifie : INtelligent Graphic RElational System.\r\n<a href="http://www.anaska.com">Anaska</a>, partenaire formation d''ingres, propose des <a href="http://www.anaska.com/formation-ingres.php">formations pour la base de données Ingres</a>.', '<strong>8 Avril : Les bases de la communication entre PHP et la base de données Ingres</strong>\r\n\r\nDu téléchargement de l''extension Ingres (via PECL) à l''extraction de données en passant par des manipulations plus complexes découvrez via ce web seminar comment manipuler Ingres avec PHP.\r\n<em>Webseminar en anglais.</em>\r\n\r\n*Time: 6am* Pacific Standard Time\r\n<a href="http://cl.exct.net/?ju=fe6617707561007b7214&ls=fdff15777160007c76107277&m=fef51271766c0c&l=fecd16727464027a&s=fe2116747161037d721c79&jb=ffcf14&t=">S''enregistrer</a>\r\n\r\n*Time: 5pm* Pacific Standard Time\r\n<a href="http://cl.exct.net/?ju=fe6517707561007b7215&ls=fdff15777160007c76107277&m=fef51271766c0c&l=fecd16727464027a&s=fe2116747161037d721c79&jb=ffcf14&">S''enregistrer</a>\r\n\r\n\r\n<strong>16 Avril : Contribuez à PHP et Ingres au travers du driver PECL</strong>\r\n\r\nLe premier pas pour ceux qui souhaitent aider n''importe quel projet\r\nOpenSource peut être assez difficile. Du rapport de bug au soumissionement de nouvelle fonctionnalités en passant par des corrections de bugs cette présentation s''adresse à ceux qui souhaitent participer au développement de l''extension PHP Ingres.\r\n<em>Webseminar en anglais.</em>\r\n\r\n*Time: 6am* Pacific Standard Time\r\n<a href="http://cl.exct.net/?ju=fe6417707561007b7216&ls=fdff15777160007c76107277&m=fef51271766c0c&l=fecd16727464027a&s=fe2116747161037d721c79&jb=ffcf14&t=">S''enregistrer</a>\r\n\r\n*Time: 5pm* Pacific Standard Time[\r\nS''enregistrer->http://cl.exct.net/?ju=fe6317707561007b7217&ls=fdff15777160007c76107277&m=fef51271766c0c&l=fecd16727464027a&s=fe2116747161037d721c79&jb=ffcf14&t= ]', 0, 1207126411, 1, NULL),
(353, 58, '', '29 avril 2008 : Industrialiser les développements PHP, le cas e-TF1', '29-avril-2008-industrialiser-les-d-veloppements-php-le-cas-e-tf1', 'e-TF1, filiale de la chaîne <a href="http://www.tf1.fr">TF1</a> en charge des nouveaux médias, vous propose propose de découvrir sa méthodologie et ses outils permettant une réelle industrialisation des projets PHP.', 'Thierry Longis et Christophe Moine sont architectes logiciel. Depuis plusieurs années ils étudient et développent des outils et des méthodes pour l''efficacité du travail en équipe, la durabilité des développements et leur résistance à la charge au sein du groupe <a href="http://www.tf1.fr/">TF1</a>. Les outils qu''ils utilisent pour arriver à leur fin proviennent pour la majeure partie du monde de l''open-source. ', 'e-TF1 dispose d''une équipe de 25 développeurs qui travaille dans un environnement complexe hébergeant plusieurs milliers de fichiers PHP pour une trentaine de projets actifs. Comment faire dans ces conditions pour garder le contrôle de la plateforme, la maitrise de la qualité et des connaissances tout en assurant une productivité optimale ? \r\n\r\nLors de cette conférence, e-TF1 vous propose de découvrir sa méthodologie et ses outils permettant une réelle industrialisation des projets PHP.\r\n\r\nInfos pratique :\r\n\r\n-* Date et heure : <strong>le mardi 29 avril 2008 de 20h00 à 21h30</strong>\r\n-* Lieu : la <a href="http://maps.google.fr/maps?f=q&hl=fr&geocode=&q=fiap,+jean+monet&sll=47.15984,2.988281&sspn=11.384341,41.132813&ie=UTF8&ll=51.99841,2.988281&spn=10.314135,41.132813&z=5&iwloc=A">FIAP</a>\r\n\r\n<h3>>>> <a href="https://afup.org/pages/rendezvous/">S''inscrire à la conférence</a></h3>\r\n', 0, 1207611123, 1, NULL),
(354, 68, '', 'Appel à conférenciers', 'appel-conf-renciers', 'L''AFUP (Association Française des Utilisateurs de PHP) annonce l''appel à conférenciers pour le Forum PHP 2008.', '', 'Pour cet évènement unique en France nous recherchons les experts francophones qui souhaitent partager leurs expériences et leur savoir-faire. Une liste non-exhaustive inclue les sujets suivants:\r\n\r\n<ul>\n<li>Comment gérer un projet PHP (outils, méthodes, ...)\r</li>\n<li>Assurer la qualité du code\r</li>\n<li>Le Droit sur Internet\r</li>\n<li>Monter son entreprise autour de PHP\r</li>\n<li>La montée en charge\r</li>\n<li>Connecter des services (web services)\r</li>\n<li>Les interfaces riches (choix technologie, implémentation, ...)\r</li>\n</ul>\n\r\nPour postuler rendez vous sur cette page: <a href="https://afup.org/pages/forumphp2008/appel-a-conferenciers.php">Plus de Détails</a>', 0, 1207722263, 1, NULL);
INSERT INTO `afup_site_article` (`id`, `id_site_rubrique`, `surtitre`, `titre`, `raccourci`, `descriptif`, `chapeau`, `contenu`, `position`, `date`, `etat`, `id_personne_physique`) VALUES
(355, 9, '', '[17 Avril 2008] - Journée développeurs eZ Publish', '17-avril-2008-journ-e-d-veloppeurs-ez-publish', 'Venez participer à une demi journée technique sur eZ Publish le 17 Avril à Paris.', '', 'Il reste quelques places disponibles pour cet évènement qui aura lieu à Paris  le 17 avril de 14h à 18h30.\r\n\r\nN''hésitez pas à en faire part à vos développeurs ou architectes techniques afin qu''ils participent.\r\n\r\nIl s''agit de la quatrième journée Développeur organisée par eZ Systems, et de la deuxième se tenant à Paris.L''événement sera en partie une "non conférence", puisque nous proposons aux participants de nous soumettre leurs propositions de sujets.\r\n\r\nNous commencerons néanmoins par quelques présentations et un tutoriel sur eZ Find, et nous finirons par une présentation et une discussion autour de la Roadmap eZ Publish.\r\n\r\nL''événement sera hébergé par Sun Microsystems et se tiendra au "Sun Force Centre", 42 Avenue de Iena, Paris.\r\n\r\nUn cocktail de fin clôturera cette deuxième journée Parisienne, gracieusement offert par Sun Microsystems, notre hôte pour cette journée.\r\n\r\nLors de votre inscription, vous pourrez proposer un sujet que vous pourriez présenter. Nous contacterons toutes les personnes ayant proposées un sujet, pour réaliser une sélection si nécessaire et organiser l''intervention.\r\n\r\nNous restons à votre disposition et attendons votre réponse à\r\n<a href="http://info.fr@ez.no">info.fr@ez.no</a>.', 0, 1207810232, 1, NULL),
(357, 58, 'SilverLight', '[14/05/2008] conférence gratuite : Applications riches sur le Web avec PHP et Silverlight', '14-05-2008-conf-rence-gratuite-applications-riches-sur-le-web-avec-php-et-silverlight', 'Le web évolue et les technologies associées aussi. Avec l''AFUP et Microsoft, venez découvrir Silverlight, le format de client riche de Microsoft. ', 'Microsoft Silverlight est un plugin pour navigateur internet, qui permet de développer des applications web enrichies d''animations, de tracés de vecteurs, de retransmission audio et vidéo, caractéristiques d''une application internet riche. \r\nSilverlight a comme concurrents <a href="http://fr.wikipedia.org/wiki/Adobe_Flash">Adobe Flash</a>, <a href="http://fr.wikipedia.org/wiki/Adobe_Flex">Flex</a>, Java FX et le <a href="http://fr.wikipedia.org/wiki/Quicktime">Quicktime d''Apple</a>. La version 2.0 récemment sortie apporte plus d''interactivité et permet aux développeurs d''utiliser des outils de développement lors de la création d''applications Silverlight.', '<p>Si on en croit <a href=''http://ajaxian.com/archives/ajaxiancom-2006-survey-results''>les sondages</a> PHP est, de loin, la technologie la plus utilisée en relation avec Ajax. Dans ce cadre l''AFUP a organisé des conférences de veille sur le thème des Rich Internet Application.</p>\r\n\r\n<p>Après nos rendez vous sur <a href="http://fr.wikipedia.org/wiki/XUL">XUL</a>, <a href="http://fr.wikipedia.org/wiki/Adobe_Flex">Flex</a> et Ajax/HTML5 venez suivre avec nous ce dernier opus sur la technologie de Microsoft : Silverlight.</p>\r\n\r\n<p>Nous vous présenterons les interactions possibles entre PHP et Silverlight à l''aide de démonstrations et cas concrets. </p>\r\n\r\nInfos pratique :\r\n\r\n-* Date et heure : <strong>le mercredi 14 mai 2008 de 19h30 à 23h00</strong>\r\n-* Lieu : la <a href="http://maps.google.fr/maps?f=q&hl=fr&geocode=&q=151+rue+Montmartre+paris&sll=48.884025,2.40556&sspn=0.019754,0.033388&ie=UTF8&z=16&iwloc=addr">cantine</a>\r\n\r\n<h3>>>> <a href="https://afup.org/pages/rendezvous/?id=4">S''inscrire à la conférence</a></h3>\r\n', 0, 1209565517, 1, NULL),
(358, 58, 'BarCamp', '[07/06/2008]  PhpCamp & TestFest à La Cantine - Paris', '07-06-2008-phpcamp-testfest-la-cantine-paris', '', 'Une première en France, un BarCamp exclusivement orienté autour de PHP. Une occasion rêvé de participer activement au développement du langage lui-même (à travers la TestFest), de présenter des sujets qui vous tiennent à coeur et de participer à des échanges nombreux.', 'Un BarCamp est avant tout une rencontre fruit du désir des uns et des autres de partager et d''apprendre : un évènement intense avec des démos, des présentations et de l''interaction entre participants. La subtilité du PhpCamp tient juste au fait que PHP sera la techno de référence.\r\n\r\nTestFest en cours au niveau mondial, on y parlera forcément des tests si nécessaires pour le bon fonctionnement du PHP Core. Pour le reste c''est tout ouvert : outils, techniques, astuces, Open Source, communautés... Tous les tags sont permis.\r\n\r\nInfos pratiques :\r\n\r\n<ul>\n<li>Date : 07/06/2008\r</li>\n<li>Horaire : 10h00 - 20h00\r</li>\n<li>Capacité : 80 places\r</li>\n<li>Tarif : gratuit\r</li>\n<li>Lieu : La Cantine\r</li>\n<li>Adresse : 151 rue Montmartre / Passage des Panoramas / 12 Galerie Montmartre / 75002 Paris\r</li>\n<li>Métro : "Grands Boulevards" et "Bourse"\r</li>\n<li><a href="http://maps.google.com/maps?f=q&hl=fr&geocode=&q=151+rue+Montmartre,+Paris&sll=37.0625,-95.677068&sspn=38.281301,57.65625&ie=UTF8&z=16&iwloc=addr">Plan d''accès</a>\r</li>\n<li><a href="http://barcamp.org/PhpCampParis">Lien sur barcamp.org</a>\r</li>\n</ul>\n\r\n<h3><a href="https://afup.org/pages/rendezvous/?id=5">S''inscrire au PhpCamp</a></h3>\r\n', 0, 1210928764, 1, NULL),
(359, 9, '', '[10/07/2008] Rdv Technique : Yahoo lance "SearchMonkey"', '10-07-2008-rdv-technique-yahoo-lance-searchmonkey', 'Yahoo! a quelque chose à fêter avec vous ! Dans le style "SearchMonkey" !\r\n\r\n\r\n\r\nVous êtes développeur ? Venez célébrer avec nous le lancement de SearchMonkey. Au programme de la soirée: des démos en live, des goodies, et bien sûr, nourriture et boisson à l''avenant !', '', 'Lieu : La Cantine - Coworking Paris\r\n151 rue de Montmartre\r\nParis, Île-de-France 75002\r\n\r\n\r\n\r\nAvec SearchMonkey, les développeurs et les webmasters peuvent utiliser les standards du Web sémantique et les données structurées pour améliorer et enrichir les résultats de Yahoo! Search afin de les rendre plus utiles, plus pertinents et plus attrayants. Venez donc rencontrer l''équipe qui a créé le service : vous aurez toute liberté pour leur poser les questions qui vous tiennent à coeur.\r\n\r\nRSVP : si vous souhaitez participer, merci de nous répondre en nous envoyant votre nom et celui de votre entreprise à <a href="http://searchmonkeyevent@yahoo-inc.com">searchmonkeyevent@yahoo-inc.com</a>.\r\n\r\nNous espérons vous voir le 10 juillet!\r\n\r\n<a href="http://upcoming.yahoo.com/event/792617">Le lien sur Yahoo</a>\r\n', 0, 1215166934, 1, NULL),
(409, 9, '', 'PHP Solutions devient gratuit', 'PHP Solutions devient gratuit', '<p>Lancement de la nouvelle version du magazine PHP Solutions en version t&eacute;l&eacute;chargeable</p>', '', '<p>Par ailleurs, cette nouvelle formule devient mensuelle et disponible au format PDF et gratuite.</p>\r\n<p>&nbsp;</p>\r\n<p>Pour ce num&eacute;ro, le sommaire est le suivant :</p>\r\n<p>&nbsp;</p>\r\n<ul>\r\n<li> Les Google Webmaster Tools </li>\r\n<li> Pr&eacute;processeur CSS </li>\r\n<li> Administrer votre serveur Debian par SSH </li>\r\n<li> AJAX facile avec JQuery et Zend Framework </li>\r\n<li> CLI : PHP en ligne de commande </li>\r\n<li> Manipuler les cookies avec PHP </li>\r\n<li> Android ou gPhone </li>\r\n<li> MySQLND : une &eacute;conomie de ressources </li>\r\n<li> PHP et la s&eacute;curit&eacute; </li>\r\n<li> Un comparatif de forums PHP </li>\r\n<li> Puppy Linux / Toutou Linux une distribution tr&egrave;s l&eacute;g&egrave;re et &agrave; la pointe de la technologie !</li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p>Pour acc&eacute;der au t&eacute;l&eacute;chargement : <a href="http://phpsolmag.org/fr/magazine/1068-ajax-avec-jquery-et-zend-framework">num&eacute;ro 2010-04 :  AJAX avec JQuery et Zend Framework</a></p>', 0, 1273615200, 1, 151),
(410, 9, '', 'Rencontres Designers et Développeurs - Adobe & Zend', 'rencontres-designers-et-developpeurs-Adobe-Zend', '', '<p>Adobe organise un &eacute;v&eacute;nement le 17 mai et Zend y participera pour la partie PHP.</p>', '<p><br /> <br /> Les rencontres Designers et D&eacute;veloppeurs - Adobe &amp; Zend<br /> 17 mai 2010 - A&eacute;roclub de France, Paris<br /><br /> Venez d&eacute;couvrir les nouveaut&eacute;s de Flash Catalyst CS5, Flash Professional CS5, Flash Builder 4 et Flex 4.<br /> <br />Au cours de cet apr&egrave;s-midi, nous reviendrons sur les nouveaut&eacute;s majeures des outils de la Creative Suite 5 et de Flex 4 pour les designers interactifs, les web designers et les d&eacute;veloppeurs d''applications. <br /><br />Enfin vous pourrez assister &agrave; l''atelier de votre choix parmi les trois ateliers propos&eacute;s.<br /> <br />Agenda <br /> 13h30 &agrave; 14h00 - Accueil<br /> <br />14h00 &agrave; 15h30 - SESSION PL&Eacute;NI&Egrave;RE<br /> Nouveaut&eacute;s de Flash Pro CS5, Flash Catalyst CS5 &amp; Flash Builder 4.<br /> <br />15h45 &agrave; 17h00 - Un atelier au choix<br /> Atelier A - Flex 4 : skinning avanc&eacute; de composants avec Spark<br /> Atelier B - Flash Builder 4 &amp; PHP (Zend &amp; Adobe)<br /> Atelier C - Animation, cr&eacute;ation, d&eacute;veloppement, travail en &eacute;quipe, d&eacute;couvrez toutes les nouveaut&eacute;s de Flash CS5<br /> <br />17h30 &agrave; 19h00 - SESSION PL&Eacute;NI&Egrave;RE<br /> Adobe et le d&eacute;veloppement d''applications sur mobiles<br /> Optimiser du code ActionScript 3<br /> Update sur le Flash Player 10<br /> Adobe et le multi-touch<br /> <br />19h15 &agrave; 21h00 - Cocktail - &Eacute;changes autour d''un verre<br /> <br />Pour les inscriptions : <a name="http://events.adobe.co.uk/cgi-bin/event.cgi?country=fr&amp;eventid=9615" href="http://events.adobe.co.uk/cgi-bin/event.cgi?country=fr&amp;eventid=9615" target="_self">http://events.adobe.co.uk/cgi-bin/event.cgi?country=fr&amp;eventid=9615</a><br /><br /></p>', 0, 1273528800, 1, 151),
(361, 58, '', 'Compte rendu du rendez-vous e-TF1', 'compte-rendu-du-rendez-vous-e-tf1', 'En avril dernier, la société e-TF1 est intervenu lors d''un rendez-vous pour nous présenter l''organisation de ses développements PHP. Un retour d''expérience très enrichissant qui démontre que l''on peut réellement mettre en place une stratégie d''industrialisation en PHP, avec une gestion efficace de la qualité. ', 'Thierry Longis et Christophe Moine sont architectes logiciel. Depuis plusieurs années ils étudient et développent des outils et des méthodes pour l''efficacité du travail en équipe, la durabilité des développements et leur résistance à la charge au sein du groupe <a href="http://www.tf1.fr/">TF1</a>. Les outils qu''ils utilisent pour arriver à leur fin proviennent pour la majeure partie du monde de l''open-source. ', 'Constituée d''une équipe d''environ 25 développeurs, e-TF1 est une société de production web, filiale du groupe audio-visuel TF1. Son rôle est de réaliser et maintenir de nombreux projets web de toute taille, avec une équipe en renouvellement permanent. Dès lors, plusieurs questions se posent : \r\n\r\n-* Quelle architecture adopter pour privilégier la réutilisation ?\r\n-* Comment contrôler la qualité du code ? Les performances ?\r\n-* Comment transmettre les compétences techniques et métier ?\r\n-* Comment rendre les développements efficaces quand on travail en équipe ?\r\n\r\nThierry Longis et Christophe Moine ont une expérience de plusieurs années sur cette question. En charge de mettre en place la méthodologie et les outils adéquats, ils nous ont éclairé sur de nombreux points qui nous permettrait d''être plus efficaces avec PHP. En voici quelques exemples : \r\n\r\n"En tant que développeurs, nous passons énormément de temps à chercher : le fichier X à la ligne Y, la fonction qui effectue telle opération, etc. Ces recherches font non seulement perdre du temps mais déconcentrent, car elles obligent à se détacher des raisonnements essentiels. Une fois ce problème réglé par une organisation et des outils ingénieux, les travaux sont plus efficaces et plus agréables à mener."\r\n\r\n"Des règles de développement sont nécessaires pour homogénéiser le travail à plusieurs, ce qui nécessite souvent de changer les habitudes et d''avoir une bonne mémoire. Tout l''art de la méthodologie consiste à masquer la contrainte par un apprentissage ludique et guidé."\r\n\r\n-* <a href="http://www.phptv.fr/juin-2008">Le témoignage de Thierry et Christophe sur PHPTV, avec des démonstrations de leurs outils</a>\r\n-* Le support de la conférence : \r\n\r\n<doc19|center>', 0, 1216247103, 1, NULL),
(362, 9, 'Soutenu par ', '[19/09/2008] Apéro de lancement de la communauté MySQL France', '19-09-2008-ap-ro-de-lancement-de-la-communaut-mysql-france', 'Happy Hour MySQL le 19 septembre de 18h30 à 22h30 à la <a href="http://www.lacantine.org">Cantine</a>', 'Avec le soutien de <a href="http://fr.sun.com/startupessentials/">Sun Microsystems "Startups Essentials"</a> ', 'Vendredi 19 septembre 2008 à PARIS\r\n\r\nAfin de lancer l''association officielle des utilisateurs francophones de MySQL, <a href="http://www.lemug.fr">LE MUG.FR</a> (LE Mysql User Group) vous accueille autour d''un Happy Hour.\r\n\r\nCe RDV festif permettra à l''ensemble des développeurs de la communauté open source d''échanger autour des aspects techniques de MySQL, de rencontrer les utilisateurs et experts, de participer à la création de l''association, de suggérer vos idées, d''en devenir membre...\r\n\r\n<strong>Au programme :</strong>\r\n\r\n<ul>\n<li>Annonce du lancement <a href="http://www.lemug.fr">LEMUG.FR</a>\r</li>\n<li>Présentation technique : mise en place d''une architecture répartie, optimisation des performances.Retour utilisateur : MySQL chez Yahoo!\r</li>\n<li>Networking (apéro gratos !!!)\r</li>\n</ul>\n\r\nCette rencontre aura lieu *de 18h00 à 22h30*\r\n\r\n<strong>Le lieu : LA CANTINE</strong>\r\n\r\n151 rue de Montmartre,\r\n\r\n12 Galerie Montmartre - Paris 2^ème\r\n\r\n(Metros : Grands Boulevards ou Bourse)\r\n\r\n\r\n\r\n<strong>Reservation</strong>\r\n\r\n[vloquet@alx-communication.com\r\n->vloquet@alx-communication.com ]\r\n\r\nPour tout renseignement, tel. : 06 68 42 79 68', 0, 1218284002, 1, NULL),
(363, 69, '', 'PHP TV : la Web TV consacrée à PHP', 'php-tv-la-web-tv-consacr-e-php', 'L''<a href="http://www.phptv.fr/septembre-2008">édition de septembre 2008 du magazine PHP TV</a> est en ligne. <a href="http://www.phptv.fr">PHP TV</a> est une Web TV consacrée à la technologie PHP. <a href="http://www.phptv.fr/emissions">Deux émissions</a> sont actuellement en ligne et plusieurs sujets et événements sont traités tels que PHAR, les espaces de noms, le PHP Camp, les pratiques de PHP en entreprise... ', '<a href="http://www.phptv.fr">PHP TV</a> propose également un <a href="http://www.phptv.fr/rss.xml">flux rss</a>, un <a href="http://www.phptv.fr/podcast.xml">flux podcast</a> et une newsletter pour être tenu au courant des nouvelles émissions. ', 'Les sujets de l''<a href="http://www.phptv.fr/septembre-2008">émission de septembre</a> sont suivants : \r\n\r\n-* <a href="http://www.phptv.fr/requetes-preparees-pdo-php-wiki">News : requêtes préparées, PDO et le wiki de PHP</a>\r\n-* <a href="http://www.phptv.fr/reportage-barcamp-php-camp">Reportage : le premier barcamp français sur PHP</a>\r\n-* <a href="http://www.phptv.fr/debat-namespaces-espaces-de-noms">Débat : les espaces de noms (namespaces)</a>\r\n-* <a href="http://www.phptv.fr/interview-afup-arnaud-limbourg">Interview : Arnaud Limbourg, président de l''AFUP</a>\r\n\r\nL''<a href="http://www.phptv.fr/septembre-2008">émission du mois de juin</a> est également en ligne et traite du Google summer of Code, de PHAR et des pratiques de développement de la société e-TF1.', 0, 1220890553, 1, NULL),
(365, 9, 'Solution Linux', 'Appels à conférenciers pour Solutions Linux 2009', 'appels-conf-renciers-pour-solutions-linux-2009', 'Du 31 Mars au 2 Avril 2009 aura lieu la grande messe annuelle : solution Linux. Plusieurs milliers de personnes vont venir visiter le salon et une partie suivra les conférences.  \r\n\r\nVous connaissez bien PHP ? Vous avez développé ou participé au développement d''applications intéressantes sur PHP ? <a href="http://www.confsolutionslinuxparis.com">Faites le savoir !</a>', 'Le salon Solutions Linux aura lieu les 31 mars, 1 et 2 avril 2009, à la Porte de Versailles -Paris. C''est l''évènement phare de l''OpenSource.', 'Un délai supplémentaire a été accordé pour proposer des thèmes concernant PHP : dimanche 12 Octobre.\r\n\r\nL''appel à conférence : Votre plateforme internet et intranet avec PHP.\r\n\r\nTechnologie majoritairement adoptée sur Internet, PHP se positionne petit à petit comme incontournable dans les systèmes d''information d''entreprise. Notre journée consacrée à PHP se tiendra en deux temps : une matinée permettant un overview des possibilités et de l''écosystème de PHP ; une après midi plus pratique avec des cas d''utilisation et des mises en pratique. \r\n\r\nVous voulez vous exprimer ? <a href="http://www.confsolutionslinuxparis.com">GO</a>\r\n\r\n<a href="http://www.confsolutionslinuxparis.com/">http://www.confsolutionslinuxparis.com/</a>', 0, 1223400329, 1, NULL),
(366, 58, 'Tips', '20 octobre : Rencontre PHP 5.3 à Lille', '20-octobre-rencontre-php-5-3-lille', 'Vous êtes nombreux à vous déplacer au forum PHP depuis Lille, alors nous vous proposons une rencontre le 20 Octobre à Lille.', 'Une occasion d''acheter des éléPHPants sur Lille !', 'Venez retrouver des développeurs, architectes et experts PHP, de 19h30 à 21h30.\r\n\r\nAu programme, ce soir là uniquement : \r\n\r\n\r\n<ul>\n<li>Les nouveautés de PHP 5.3\r</li>\n<li><a href="http://www.aperophp.net/apero.php?id=312">Apéro PHP</a>\r</li>\n<li>Trafic d''éléPHPants\r</li>\n<li>Inscriptions AFUP\r</li>\n</ul>\n\r\n<doc21|center>\r\n(image piquée à : <a href="http://blog.onbebop.net/post/2008/03/11/elePHPant-story-1">http://blog.onbebop.net/post/2008/03/11/elePHPant-story-1</a>)\r\n\r\n<strong>Le lieu</strong> \r\nL''écart\r\n26 rue Jeanne d''Arc\r\n59000 LILLE\r\n\r\nL''entrée sera gratuite, et on travaille à trouver de quoi nourrir les affamés et assoiffés sur place. \r\n\r\nPour les éléPHPants, vous économiserez les frais de port, mais prévenez à l''avance pour les grosses quantités.', 0, 1223542919, 1, NULL),
(367, 49, 'LeMug.fr', '25 Octobre : 1er BARCAMP MySQL par LeMUG.fr', '25-octobre-1er-barcamp-mysql-par-lemug-fr', 'Un peu plus d''un mois après son lancement, l''association francophone des utilisateurs de MySQL organise son 1er BarCamp.\r\nL''occasion pour la communauté open source et les DBA de se retrouver et d''échanger autour de MySQL, et de contribuer activement au développement de la base de données.', 'Le MySQL User Group', 'Rendez-vous à La Cantine\r\nSamedi 25 octobre de 11h à 19h\r\n\r\nAu programme : démos, présentations et surtout interaction entre les participants.\r\n\r\nD''ores et déjà le Mug.fr lance le débat sur les sujets suivants :\r\n\r\n<ul>\n<li>MySQL encapsulé avec PDO\r</li>\n<li>MySQLi\r</li>\n<li>L''analyse d''un système en production\r</li>\n<li>La sécurité sous MySQL\r</li>\n<li>Echange d''expérience\r</li>\n</ul>\n\r\n<strong>Infos pratiques</strong>\r\n\r\nDate : Samedi 25 octobre 2008\r\nHoraire : 11h-19h\r\n\r\nDéjeuner offert\r\n\r\nTarif : Gratuit\r\n\r\nCapacité : 100 places\r\n\r\nLieu : LA CANTINE\r\n151 rue Montmartre,\r\nPassage des Panoramas\r\n12 Galerie Montmartre\r\nPARIS 2ème\r\n(M° Grands Boulevards ou Bourse)\r\n\r\nInscriptions : <a href="http://www.barcamp.org/BarCampLeMugParis">http://www.barcamp.org/BarCampLeMugParis</a>', 0, 1223549391, 1, NULL),
(373, 19, '', 'PHP partout chez 20minutes.fr', 'php-partout-chez-20minutes-fr', '<p>PHP est tr&egrave;s largement utilis&eacute; chez 20minutes.fr, il est m&ecirc;me utilis&eacute; partout. Nicolas Silberman, responsable technique, nous explique le p&eacute;rim&egrave;tre d''utilisation de PHP au sein de leur infrastructure qui g&egrave;re 40 millions de pages vues par mois.</p>', '', '<p><strong>Bonjour, est-ce que vous pourriez dans un premier temps nous pr&eacute;senter  votre profil ainsi que celui de votre soci&eacute;t&eacute; (nom, pr&eacute;nom, nombre d'' employ&eacute;s, chiffre d''affaire, etc.) ?</strong></p>\r\n<p>Je suis Nicolas Silberman, responsable technique nouveaux media chez 20minutes.fr. Le site 20minutes.fr, c''est plus d''une trentaine de personnes d&eacute;di&eacute;es au web dont la plupart sont des journalistes, 5 personnes &agrave; la technique, des commerciaux, marketing, etc.  En septembre 2008, 20minutes.fr a fait 3 560 000 visiteurs uniques selon Mediametrie NetRatings, et environ 40 millions de pages vues.</p>\r\n<p><strong>Quelles sont les caract&egrave;ristiques de votre plateforme technique ?</strong></p>\r\n<p>Nous avons environ 30 serveurs qui sont cloisonn&eacute;s en fonction de nos diff&eacute;rents applicatifs (le site, les projets satellites, l&rsquo;outil de gestion de contenu, base de donn&eacute;es, pr&eacute;production, etc.).  Ces serveurs sont des plateformes LAMP avec du Debian, Apache  et PHP 5. Tous nos serveurs sont prot&eacute;g&eacute;s par un firewall et un loadbalancer.</p>\r\n<p><strong>Apparement la grande majorit&eacute; de votre plate-forme tourne grace au  logiciel Open Source. Pourquoi ?</strong></p>\r\n<p>Nous utilisons PHP depuis le d&eacute;but de 20minutes.fr pour plusieurs raisons :</p>\r\n<p>&nbsp;</p>\r\n<ul>\r\n<li>Accessible (on trouve codeurs et prestataires)</li>\r\n<li>Communaut&eacute; active (notamment la communaut&eacute; fran&ccedil;aise) </li>\r\n<li>Documentation fournie // mailing list active </li>\r\n<li>Les gens partagent </li>\r\n<li>Le langage a fait ses preuves </li>\r\n<li>La roadmap PHP donne confiance</li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<ul>\r\n</ul>\r\n<p><strong>Pouvez-vous lister rapidement les diff&eacute;rents projets / applications  dans lesquels vous utilisez PHP aujourd''hui ?</strong></p>\r\n<p><em>&nbsp;</em> La r&eacute;ponse est tr&egrave;s facile : tous !</p>\r\n<p><strong>Quelle est la volum&eacute;trie de ces projets ? (nbr connexions, users  simultan&eacute;s, pages vues, etc.)</strong></p>\r\n<ul>\r\n<li>40 000 000 de pages vues par mois (sept 2008) </li>\r\n<li>pic &agrave; 900 Mbps de bande passante </li>\r\n<li>500 000 inscrits &agrave; notre newsletter quotidienne </li>\r\n<li>plus de 100 pages vues &agrave; la seconde </li>\r\n<li>plus de 5 000 requ&ecirc;tes &agrave; la seconde</li>\r\n</ul>\r\n<ul>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p><strong>Avec quoi utilisez vous PHP ? En particulier au niveau de la base de donn&eacute;es.</strong></p>\r\n<p>Nous utilisons MySQL 5 avec PHP sans license ou support particulier.</p>\r\n<p><strong>Comment voyez-vous l''&eacute;volution de PHP au sein de votre &eacute;quipe de  production ?</strong></p>\r\n<p>PHP va continuer &agrave; &ecirc;tre notre principal langage de d&eacute;veloppement. L''&eacute;volution au sein de l&rsquo;&eacute;quipe technique de 20minutes.fr sera principalement de migrer vers un framework fait maison ou non, et ainsi rendre notre code plus efficace.</p>', 0, 1228863600, 1, 0),
(377, 9, '', '[13/01/2009] - PHP et les frameworks', '13-01-2009-php-et-les-frameworks', 'Les Éditions Eyrolles organisent à la Cantine un événement PHP le 13 janvier 2009, en collaboration avec l''AFUP .', '', 'Retrouvez nos auteurs le 13 janvier 2009 de 19h à 22h à la Cantine à Paris pour un évènement convivial autour de PHP et de ses frameworks de développement !\r\n\r\n<a href="http://www.editions-eyrolles.com/Evenement/php-framework/">http://www.editions-eyrolles.com/Evenement/php-framework/</a>\r\n\r\nLa Cantine : 12 passage Montmartre - Galerie des Panoramas - 151 rue Montmartre - 75002 Paris Métro : Grands Boulevards / Bourse - Bus : 29, 39, 68, 74, 85\r\n\r\nInscription obligatoire ! Le nombre de places étant limitées, merci de confirmer votre présence par mail à : evenement@eyrolles.com.\r\n\r\n<h3>PHP, un langage et une communauté</h3>\r\n\r\n<ul>\n<li>PHP en entreprise, par Cyril Pierre de Geyer (<a href="http://www.anaska.com/livre-php-5-avance.php">PHP 5 avancé</a>) avec une intervention sur PEAR par Arnaud Limbourg, président de l''AFUP.\r</li>\n</ul>\n\r\n<ul>\n<li>Bonnes pratiques élémentaires, par Guillaume Ponçon (<a href="http://www.eyrolles.com/Informatique/Livre/best-practices-php-5-9782212116762">Best PRactices PHP 5</a>) où seront rappelés deux niveaux d''évidences, pour le codeur PHP et l''architecte PHP.\r</li>\n</ul>\n\r\n<ul>\n<li>PHP 5.3, par Eric Daspet (<a href="http://www.eyrolles.com/Informatique/Livre/php-5-avance-9782212123692">PHP 5 avancé</a>)\r</li>\n</ul>\noù l''on prendra connaissance des fonctions de PHP 5.3 et comment en tirer parti.\r\n\r\n<ul>\n<li>Comparaisons lapidaires avec Python, par un membre de l''afPy\r</li>\n</ul>\noù l''on passera en revue les différences avec cet autre langage très connu.\r\n\r\n<h3>Frameworks de développement : état de l''art et comparaison</h3>\r\n\r\n<ul>\n<li>Le Zend Framework, par Julien Pauli (<a href="http://www.eyrolles.com/Informatique/Livre/zend-framework-9782212123920">Zend Framework en pratique</a>)\r</li>\n</ul>\n\r\n<ul>\n<li>Symfony 1.2, par Fabien Potencier et Nicolas Perriault\r</li>\n</ul>\n\r\n<ul>\n<li>Jelix, par Laurent Jouanneau\r</li>\n</ul>\n\r\n<ul>\n<li>Comparaisons avec Django, par David Larlet\r</li>\n</ul>\n\r\n<ul>\n<li>Comparaisons avec Rails, par Christophe Porteneuve\r</li>\n</ul>\n\r\nLa soirée se clôra d''une table ronde conviviale et animée, nous l''espérons, avec de nombreuses questions-réponses portant sur les différents frameworks.\r\n\r\nDes frameworks de haut niveau pourront également être abordés par leurs contributeurs principaux. Ainsi les nouveautés de Drupal 7 seront-elles présentés par Damien Tournoud.\r\n', 0, 1231197557, 1, NULL),
(378, 9, '', '[14/01/2009]  Dernières tendances de l''Open Source', '14-01-2009-derni-res-tendances-de-l-open-source', 'LeMUG.FR vous invite, avec la participation des Clubs Utilisateurs GUSES (Solaris), JUG Paris (Java), OSS Get-Together Paris et la collaboration de SUN Microsystems, à débuter 2009 par une rencontre d''échanges et de débats autour des dernières tendances de l''open source, mercredi 14 janvier.', '', 'Cette rencontre sera en outre l''occasion de débattre avec Simon Phipps, Sun''s chief open source officer, de passage à Paris pour l''occasion.\r\n\r\nRDV mercredi 14 janvier 2009 à partir de 18h30,\r\n42 avenue d''Iéna, Paris 16°\r\n\r\nN''hésitez pas à faire du buzz !\r\n\r\nEn vous souhaitant d''excellentes fêtes de fin d''année,\r\nL''équipe LeMUG.FR', 0, 1231172160, 1, NULL),
(380, 9, 'Solution Linux', '[02/04/2009] Journée PHP lors de Solution Linux', '02-04-2009-journ-e-php-lors-de-solution-linux', 'A l''occasion de <a href="http://www.solutionslinux.fr/main.php">Solution Linux</a> se tient une journée PHP vous présente un condensé des incontournables du moment. \r\n\r\nDate : 2 Avril 2009, toute la journée\r\n\r\nLieu : Paris Expo - Porte de Versailles', 'Solutions Linux / Open Source vous permet de :\r\n\r\n<ul>\n<li>Rencontrer les associations et communautés du libre\r</li>\n<li>Se tenir informer des évolutions du marché\r</li>\n</ul>\n\r\n<strong>Jours et horaires d''ouverture</strong>\r\n\r\n<ul>\n<li>Mardi 31 mars 2009 : 9h00-18h00\r</li>\n<li>Mercredi 1er avril 2009 : 9h00-20h00\r</li>\n<li>Jeudi 2 avril 2009 : 9h00-18h00\r</li>\n</ul>\n\r\n<strong>Lieu</strong>\r\n \r\nParis - Porte de Versailles, Hall 2.2', '<ul>\n<li><a href="http://www.confsolutionslinuxparis.com/programme/">Le programme complet des tutoriaux pendant Solution Linux</a>\r</li>\n<li><a href="http://www.solutionslinux.fr/main.php">Le site de Solution Linux</a>\r</li>\n</ul>\n\r\nLa matinée sera orientée migration : le support de PHP 4 ayant été arrêté en 2008, il faut envisager de migrer ses applications vers PHP 5, profitez des retours\r\nd''expérience et des conseils des meilleurs experts. \r\n\r\nPour clôturer la matinée un retour d''expérience par le DSI du site 20minutes.fr. L''après-midi sera orienté Web 2 et CMS. Quels outils pour optimiser l''ergonomie de vos sites Web, quel CMS pour quel besoin, les meilleurs experts français viendront présenter les solutions\r\n\r\nLe programme de la journée :\r\n\r\n<ul>\n<li><strong>Introduction et présentation</strong> \r</li>\n</ul>\n\r\npar le président de séance Cyril PIERRE de GEYER d''<a href="http://www.anaska.com">Anaska</a>\r\n\r\n<ul>\n<li><strong>Migrer vos applications PHP 4 vers PHP5</strong>\r</li>\n</ul>\n\r\npar Eric DASPET de <a href="http://www.yahoo.fr">Yahoo</a>, Damien SEGUY et Julien PAULI d''<a href="http://www.anaska.com/">Anaska</a>\r\n\r\n<ul>\n<li><strong>Techniques de remaniement en PHP pour faciliter une migration</strong>\r</li>\n</ul>\n\r\npar Perrick PENET, <a href="http://www.noparking.net/">no parking</a>\r\n\r\n<ul>\n<li><strong>Retour d''experience site <a href="http://www.20minutes.fr">20minutes.fr</a></strong>\r</li>\n</ul>\n\r\npar Nicolas SILBERMAN, <a href="http://www.20minutes.fr">20minutes.fr</a>\r\n\r\n<ul>\n<li><strong>Les CMS PHP open source du marché</strong>\r</li>\n</ul>\n\r\npar Marine SOROKO, <a href="http://core-techs.fr/sites/core-techs/">Core-Techs</a>\r\net Julien MENICHINI, <a href="http://alterway.fr/">AlterWay</a>\r\n\r\n<ul>\n<li><strong>Présentation du framework Ajax OpenExt</strong>\r</li>\n</ul>\n\r\npar Sarah Haïm-LUBCZANSKI, <a href="http://www.anaska.com">Anaska</a>\r\n\r\n<ul>\n<li><strong>Zend Framework, Dojo, Flex : les RIA industrialisées avec PHP</strong>\r</li>\n</ul>\n\r\npar Gauthier DELAMARRE,\r\n<a href="http://www.zend.com">Zend Technologies France</a>', 0, 1235637470, 1, NULL),
(383, 9, '', 'PHP Solutions 2/2009', 'php-solutions-2-2009', '', 'Le nouveau numéro de PHP SOLUTIONS vient de sortir, sur le thème "Choisissez la meilleure technologie E-commerce". Un numéro différent des autres car en plus des nombreux sujets, ce numéro publie un résumé sur le Forum PHP 2008 organisé par AFUP.', 'Les sujets traités sont  :\r\n\r\n \r\n\r\n<ul>\r\n<li>Mise en production de PDO...\r\n</li>\r\n<li>JEU EN PHP...\r\n</li>\r\n<li>Plates-formes web pour l''e-commerce : comment choisir ?...\r\n</li>\r\n<li>Référencement naturel d''un site e-commerce...\r\n</li>\r\n<li>Le référencement internet, la visibilité contre la crise...\r\n</li>\r\n<li>Développement d''application pour Facebook...\r\n</li>\r\n<li>Détection des mots en PHP. De l''analyse à l''action...\r\n</li>\r\n<li>Programmation orientée aspect...\r\n</li>\r\n<li>PHP & Mashup...\r\n</li>\r\n<li>Sécurité et PHP...\r\n</li>\r\n</ul>\r\n\r\n\r\nPour plus de détails, <a href="http://www.phpsolmag.org/prt/view/actualies/issue/992.html">cliquez ici</a>', 0, 1237503600, 1, 0),
(403, 9, '', '16-18 Mars 2010 : PHP sera présent à Solution Linux', 'solution-linux-2010', '', '<p>L''Association Fran&ccedil;aise des Utilisateurs de PHP (AFUP) sera pr&eacute;sente sur le village associatif lors de Solution Linux du 16 au 18 Mars prochain.</p>', '<p>Venez visiter notre stand pour en conna&icirc;tre plus sur l''association et sur l''&eacute;cosyst&egrave;me PHP.</p>\r\n<p><a href="http://www.solutionslinux.fr">http://www.solutionslinux.fr</a></p>\r\n<p><strong>Un track formation traite &eacute;galement du sujet PHP :</strong></p>\r\n<p><a href="http://www.solutionslinux.fr/FormationsTutoriels_168_171.html">http://www.solutionslinux.fr/FormationsTutoriels_168_171.html</a></p>\r\n<p><em>PS : Les membres de l''association ont des r&eacute;ductions</em> sur la partie formation.</p>', 0, 1266879600, 1, 44),
(385, 58, 'BarCamp', '[09/05/2009]  PhpCamp & TestFest à La Cantine - Paris', '09-05-2009-phpcamp-testfest-la-cantine-paris', '', '', 'Un BarCamp est avant tout une rencontre fruit du désir des uns et des autres de partager et d''apprendre : un évènement intense avec des démos, des présentations et de l''interaction entre participants. La subtilité du PhpCamp tient juste au fait que PHP sera la techno de référence.\r\n\r\nTestFest en cours au niveau mondial, on y parlera forcément des tests si nécessaires pour le bon fonctionnement du PHP Core. Pour le reste c''est tout ouvert : outils, techniques, astuces, Open Source, communautés... Tous les tags sont permis.\r\n\r\nInfos pratiques :\r\n\r\n<ul>\n<li>Date : 09/05/2008\r</li>\n<li>Horaire : 10h00 - 20h00\r</li>\n<li>Capacité : 80 places\r</li>\n<li>Tarif : gratuit\r</li>\n<li>Lieu : La Cantine\r</li>\n<li>Adresse : 151 rue Montmartre / Passage des Panoramas / 12 Galerie Montmartre / 75002 Paris\r</li>\n<li>Métro : "Grands Boulevards" et "Bourse"\r</li>\n<li><a href="http://maps.google.com/maps?f=q&hl=fr&geocode=&q=151+rue+Montmartre,+Paris&sll=37.0625,-95.677068&sspn=38.281301,57.65625&ie=UTF8&z=16&iwloc=addr">Plan d''accès</a>\r</li>\n<li><a href="http://barcamp.org/PhpCampParis2">Lien sur barcamp.org</a>\r</li>\n</ul>\n\r\n<h3><a href="https://afup.org/pages/rendezvous/?id=6">S''inscrire au PhpCamp</a></h3>\r\n', 0, 1239190570, 1, NULL),
(386, 70, '', 'Forum PHP 2009 : Appel à conférenciers', 'forum-php-2009-appel-conf-renciers', '', 'L''AFUP (Association Française des Utilisateurs de PHP) annonce l''appel à conférenciers pour le Forum PHP 2009 qui se déroulera le 12 et 13 novembre 2009 à la cité des sciences.\r\n\r\n', 'Pour cet évènement unique en France nous recherchons les experts francophones qui souhaitent partager leurs expériences et leur savoir-faire.\r\n\r\nPour postuler, rendez-vous sur cette page: <a href="https://afup.org/pages/forumphp2009/appel-a-conferenciers.php">Plus de Détails</a>\r\n\r\nDe plus, nous avons aussi ouvert les inscriptions en prévente avec un tarif préférentiel (remise de 20€) \r\n<a href="https://afup.org/pages/forumphp2009/inscription.php">Inscriptions</a>\r\n\r\nLe site officiel du forum PHP 2009 <a href="https://afup.org/pages/forumphp2009/index.php">Cliquer ici</a>\r\n\r\n', 0, 1240351200, 1, 0),
(387, 9, '', 'Symfony Live : Conférence francophone sur Symfony à Paris les 11 & 12 juin 2009', 'symfony-live-conf-rence-francophone-sur-symfony-paris-les-11-12-juin-2009', '', 'La première conférence francophone entièrement consacrée à Symfony se déroulera les <a href="http://www.symfony-live.com">11 et 12 juin prochains à la Cité Universitaire</a> , à Paris. Cet événement, très attendu au sein de la communauté PHP, est organisé par Sensio Labs en partenariat avec l''AFUP.\r\nAu total, nous aurons droit à une vingtaine de sessions, sous forme de présentations sur des techniques avancées, de retours d''expériences, et d''échanges directs avec la Core Team.', '<p>Le programme vient d''être en grande partie dévoilé  :</p>\r\n\r\n<p>Parmi les thèmes abordés, les réseaux sociaux , la gestion de contenu ou les stratégies de migration. </p>\r\n<p>Les bonnes pratiques et les tests ne seront bien évidemment pas négligés. </p>\r\n\r\n<p>Pour ce qui concerne les nouveautés, Jonathan Wage et Fabien Potencier présenteront respectivement Doctrine et la version 2 de Symfony.</p>\r\n<p>Autres thèmes très attendus : les retours d''expériences présentés par des acteurs-phares de la scène médiatique, tels que le groupe Arianespace, L''Express, ou Yahoo!.</p>\r\n\r\n<p>Parmi les points d''orgue : une session consacrée à la migration de Dailymotion sous Symfony, qui vous permettra de découvrir l''envers du décor, et une première, un "Master Class" Symfony, au cours duquel Fabien Potencier réalisera dans les conditions du réel le refactoring d''une ou plusieurs applications qui lui auront été préalablement soumises.</p>\r\n\r\n<p>Pour vous y inscrire et obtenir plus d''informations, rendez-vous sur le site Web qui lui est consacré : </p>\r\n\r\n<p><a href="http://www.symfony-live.com">http://www.symfony-live.com</a>.</p>\r\n<p>', 0, 1242338400, 1, 0),
(390, 70, '', 'La 9ème édition du Forum PHP dans les starting-blocks', 'la-9-me-dition-du-forum-php-dans-les-starting-blocks', '', 'L''édition 2009 du rendez-vous incontournable des utilisateurs PHP en France s''installe à la Cité des Sciences de La Villette, les 12 et 13 novembre 2009.\r\n\r\nUne édition qui mettra à l''honneur le couple PHP/MySQL, avec un invité de marque, pour la 1ère fois en France : Michael "Monty" Widenius.', 'Paris, le 16 septembre 2009 -- L''événement estampillé AFUP (Association Française des Utilisateurs de PHP) réunira durant 2 jours quelques grands experts internationaux du monde PHP, qui viendront échanger autour des problématiques phares du langage open source. L''occasion de faire le point sur les évolutions fonctionnelle et technique, communautaire et entreprise de PHP.\r\n\r\n\r\nCette 9ème édition sera axée sur le couple PHP/MySQL, avec 8 conférences dédiées. LEMUG.fr, l''association francophone des utilisateurs de MySQL et partenaire de l''événement animera 3 conférences.\r\n\r\n\r\nLe Forum PHP accueillera en exclusivité et pour la première fois en France, Michael "Monty" Widenius, le créateur de MySQL. Suite au rachat de MySQL AB dont il était le co-fondateur, Monty a créé l''Open Database Alliance, un consortium et un lieu d''échanges pour tous les acteurs de l''écosystème de la base de données. Par ailleurs il fonde Monty Program ab, et poursuit le développement de MariaDB. A ce titre Monty présentera une conférence intitulée : « MariaDB the future of MySQL ». \r\n\r\n\r\nLe PHP se décline au féminin avec la participation de :\r\nZoe Slattery, PHP Women. Zoe a œuvré pour le compte d''IBM pendant 20 ans. En 2007, elle s''implique dans la promotion de PHP et développe des phases de tests.\r\nAnna Filina, PHP Québec, animera une conférence dédiée aux décideurs sur l''analyse des comportements des développeurs.\r\n\r\n\r\nReconnu mondialement comme une alternative de choix aux langages de programmation .Net ou J2EE, PHP est aujourd''hui une technologie mature qui entre dans une phase d''industrialisation. Largement adopté dans le monde de l''entreprise, PHP est un élément clé des infrastructures Web. \r\n\r\n\r\nL''édition 2009 sera l''opportunité d''aborder l''industrialisation, la professionnalisation et la maturation du langage PHP avec une formule qui fait son succès chaque année :\r\ndes conférences animées par les meilleurs experts internationaux\r\ndes retours d''expérience issus des grands comptes \r\ndes ateliers pratiques\r\n\r\n\r\n\r\n\r\n<h3><strong>...:: FOCUS PROGRAMME - À NE PAS MANQUER ::...</strong></h3>\r\n\r\n\r\n\r\n<strong>Conférences</strong>\r\n\r\n« MariaDB the future of MySQL » -  Michael "Monty" Widenius, le père de MySQL\r\n« Oui, PHP est industriel ! » - Damien Seguy, Alter Way Consulting\r\nConférences LEMUG.FR : Stéphane Varoqui ; Serge Frezefond, Directeur technique MySQL France, Sun Microsystems ; Olivier Dassini, Orange.\r\n« Jouons avec PHP 5.3 » - Fabien Potencier, créateur du framework PHP Symfony et Co-fondateur de Sensio Labs.\r\n« PHP and MySQL : a good match » - Johannes Schlüter, Sun Microsystems. Johannes est responsable de la publication de la version 5.3 de PHP.\r\n\r\n\r\n\r\n<strong>Témoignages utilisateurs</strong>\r\n\r\nRéplication MySQL, WAT TV\r\nMigration de J2EE vers PHP, M6 Web\r\nUltimedia et Jukebo 2.0, Digiteka\r\nRetour d''expérience, Orange\r\n\r\n\r\n\r\n\r\n<strong>A propos de l''AFUP</strong>\r\nL''Association Française des Utilisateurs de PHP (AFUP), est une association dont le principal but est de promouvoir le PHP auprès des professionnels et de participer à son développement.\r\n\r\n\r\nContact : bureau@afup.org\r\n\r\nEn savoir plus : <a href="https://afup.org/pages/forumphp2009/">https://afup.org/pages/forumphp2009/</a>\r\n\r\n\r\n\r\n\r\n', 0, 1253277363, 1, NULL),
(393, 62, '', 'Livre blanc "Industrialisez PHP"', 'livre-blanc-industrialisez-php', 'En près de 15 ans, PHP a conquis la plupart des entreprises. Au début utilisé pour des projets annexes, il est aujourd''hui au cœur du SI.\r\n\r\nLes projets se complexifient, les délais se raccourcissent : il est temps d''industrialiser les processus de développement.', '', 'Ce Livre Blanc dresse un état de l''art des outils et méthodes qui permettent aujourd''hui d''industrialiser ses développements PHP.\r\n\r\nURL : <a href="http://www.alterway.fr/publications/livre-blanc-industrialisation-php">http://www.alterway.fr/publications/livre-blanc-industrialisation-php</a>\r\n\r\n<ul>\n<li>Auteurs : Damien Seguy, Jean-Marc Fontaine\r</li>\n<li>Editeur : Alter Way\r</li>\n<li>Langue : Français\r</li>\n<li>Publication : 2009\r</li>\n</ul>\n\r\n', 0, 1254731192, 1, NULL),
(394, 9, '', 'Barcamp PHP', 'barcamp-php', '', 'La ville Toulouse accueille le premier Barcamp PHP, organisé par Linagora et en partenariat l''AFUP (Association Française des Utilisateurs de PHP) le 29 octobre 2009', 'Le thème du premier Barcamp sur le thème de PHP autour d''un Cheese and Wine.\r\n\r\nUne bonne occasion de voir ce que le Sud-Ouest est capable de faire avec PHP. Une excellente soirée en perspective !\r\n\r\nIl ne vous reste plus qu''à vous inscrire \r\n<a href="http://www.linagora.com/spip.php?article560">http://www.linagora.com/spip.php?article560</a>', 0, 1255379322, 1, NULL),
(398, 9, '', 'Gartner rédige un rapport sur PHP', 'gartner-r-dige-un-rapport-sur-php', '', 'La célèbre société de recherche et de conseil Gartner a consacré début décembre un rapport à PHP, son passé, son présent et son avenir.', 'Ce rapport estime que :\r\n\r\n<ul>\r\n<li>Le nombre de développeurs PHP dans le monde va passer de 4 millions cette année à 5 millions à l''horizon 2013 ;\r\n</li>\r\n<li>A court terme, PHP va rester une technologie web largement utilisée ;\r\n</li>\r\n<li>Sur le long terme, PHP rencontrera une concurrence de plus en plus forte d''autres technologies comme ASP.NET, Java, Python ou encore Ruby.\r\n</li>\r\n</ul>\r\n\r\nVoici les recommandations de Gartner :\r\n\r\n<ul>\r\n<li>Envisagez d''utiliser PHP pour les projets qui requièrent une combinaison de technologies Open Source et propriétaires pour construire des applications web simples.\r\n</li>\r\n<li>Envisagez PHP comme un outil spécialisé pour la création d''interface de consultation pour des architectures SOA.\r\n</li>\r\n<li>Envisagez d''adopter et de personnaliser des solutions PHP éprouvées comme Drupal ou MediaWiki  avant de partir de zéro.\r\n</li>\r\n</ul>\r\n\r\nSource : <a href="http://www.industrialisation-php.com/">Industrialisation PHP</a>', 0, 1262646000, 1, 0),
(399, 9, '', 'PHP solutions 01/2010', 'php-solutions-01-2010', '', '<p>Le nouveau num&eacute;ro de PHP SOLUTIONS vient de sortir, sur le th&egrave;me "Int&eacute;grez .NET &agrave; PHP !".</p>', '<p>En plus dans le num&eacute;ro vous trouverez, entre autres, des articles sur :</p>\r\n<p>&nbsp;</p>\r\n<p>Nouvelles fonctionnalit&eacute;s de Symfony.</p>\r\n<p>&Eacute;dition de documents OpenOffice ODF avec PHP.</p>\r\n<p>Manipuler les r&eacute;pertoires avec PHP.</p>\r\n<p>Comment r&eacute;ussir son r&eacute;f&eacute;rencement web ?</p>\r\n<p>E-commerce, comment cr&eacute;er et fonctionner une boutique en ligne ?</p>\r\n<p>Puissance des d&eacute;marches descriptives.}}}</p>\r\n<p>Envoi de mails en PHP.</p>\r\n<p>D&eacute;couvrez BeEF Exploitation.</p>\r\n<p>Et de nombreaux autres articles &agrave; ne pas manquer !</p>\r\n<p>En exclusivit&eacute;, sur le CD joint au magazine, nous vous pr&eacute;sentons le cours vid&eacute;o sur PHP et PDO r&eacute;alis&eacute; par Christophe Villeneuve du groupe Alter Way Solutions.</p>\r\n<p>Pour en savoir plus, visitez notre site :</p>\r\n<p><a href="http://phpsolmag.org/fr/magazine/990-integrez-net-a-php">http://phpsolmag.org/fr/magazine/990-integrez-net-a-php</a></p>', 0, 1263942000, 1, 151),
(401, 58, '', 'Comment pirater PHP sans se fatiguer ', 'comment-pirater-php-sans-se-fatiguer', '', 'Damien Seguy de Alter Way et Co-fondateur de l''AFUP (Association Française des utilisateurs de PHP) va animer une conférence le 10 février 2010 à 13h00 aux TechDays à Paris.', '<p>TechDays est un évènement organisé par Microsoft et Damien Seguy présentera sous la forme d''un atelier les différentes failles depuis l''extérieur mais aussi de l''intérieur.</p>\r\n\r\n<p><img src="https://afup.org/images/techdays-microsoft.png" /></p>\r\n\r\n<p>Le but est de permettre de corriger les erreurs de sécurité : <a href="http://www.microsoft.com/france/mstechdays/programmes/parcours.aspx?Key=&AUDIENCE=&PRODUIT=&LEVEL=&SpkID=2d0b0ad2-6cd4-4e1f-9215-ac33886ad506">Plus de Détails</a></p>', 0, 1265324400, 1, 0),
(402, 58, '', 'Evènement soirée GIT', 'evenement-soiree-git', 'Pour terminer en beauté l''évènement Symfony live 2010 qui se déroulera le 16 et 17 février 2010, organisé par Sensio Labs en partenariat avec l''AFUP (Association Française des utilisateurs de PHP), une soirée spéciale est prévue.', '', '<p>Cette soirée de clôture est organisé par Sensio Labs et GitHub sur le thème GIT avec la présence de Scott Chacon de GitHub à Paris.</p>\r\n\r\n<p><img src="https://afup.org/images/git.png" /></p>\r\n<p><img src="https://afup.org/images/git-hub.png" /></p>\r\n\r\n<p>Elle est destinée à l''ensemble des développeurs PHP, mais aussi aux autres langages comme Python, Perl, Ruby.</p>\r\n\r\n<p>Pour connaître le lieu et toutes les informations, il faut consulter le communiqué du site symfony-live : <a href="http://www.symfony-live.com/github-meetup#git">http://www.symfony-live.com/github-meetup#git</a></p>\r\n\r\n\r\n', 0, 1265670000, 1, 0),
(404, 9, '', 'Nouveau bureau de l''AFUP', 'bureau-2010', '<p>Suite &agrave; l''assembl&eacute;e g&eacute;n&eacute;rale tenue en f&eacute;vrier 2010, voici la composition du bureau 2010 de l''association</p>', '', '<p>Cette ann&eacute;e, il y a plusieurs vice-* en raison d''une actualit&eacute; tr&egrave;s charg&eacute;e (les 10 ans de l''association et les 15 ans de PHP)</p>\r\n<p>&nbsp;</p>\r\n<ul>\r\n<li>Pr&eacute;sident : Nicolas Silberman</li>\r\n<li>Tr&eacute;sorier : Perrick Penet</li>\r\n<li>S&eacute;cr&eacute;taire : Hugo Hamon</li>\r\n</ul>\r\n<ul>\r\n<li>Vice-Pr&eacute;sident : Olivier Hoareau</li>\r\n<li>2nd Vice-Pr&eacute;sident : Cyril Pierre de Geyer</li>\r\n<li>Vice-Tr&eacute;sorier :<em>&nbsp;en cours</em></li>\r\n<li>2nd Vice-Tr&eacute;sorier : Christophe Villeneuve</li>\r\n<li>Vice-secr&eacute;taire : Rapha&euml;l Rougeron</li>\r\n<li>2nd Vice-Secr&eacute;taire : Gauthier Delamarre</li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p>Retrouvez ici <a href="../site/?route=vie-associative-afup/222/bureaux-de-l-afup-fondateurs-et-conseil-d-administration">l''historique des bureaux</a></p>', 0, 1266966000, 1, 326),
(408, 9, '', '[Cnet] PHP et Ruby pour l''avenir', 'php-ruby-avenir', '<p>Les jeunes g&eacute;n&eacute;rations de d&eacute;veloppeurs ont un fort attrait pour les  langages dynamiques Open Source comme Ruby ou PHP au d&eacute;triment des  mod&egrave;les plus anciens que sont .NET ou Java.</p>', '', '<p>Lesquels (.net et Java) voient leur popularit&eacute; progressivement s''&eacute;roder. Avec la mise &agrave; la retraite de la vieille &eacute;cole, ces environnements phares ne vont-ils bient&ocirc;t plus repr&eacute;senter qu&rsquo;une faible part dans l&rsquo;arsenal technologique des d&eacute;veloppeurs ? <a href="http://news.cnet.com/8301-13505_3-20002569-16.html?part=rss&amp;tag=feed&amp;subj=TheOpenRoad">Matt Asay le pense.</a></p>\r\n<p>&nbsp;</p>\r\n<p><a href="http://www.lemagit.fr/article/securite-google-sun-developpement-cloud-computing-php-ruby-windows7/6128/1/php-ruby-idoles-des-jeunes-google-sun-meme-combat-windows-roc-cloud-crise-est-pas-catalyseur-arrieres-maintenance-pas-payer/">Issu d''un article de LeMagIT</a></p>', 0, 1271628000, 1, 44);
INSERT INTO `afup_site_article` (`id`, `id_site_rubrique`, `surtitre`, `titre`, `raccourci`, `descriptif`, `chapeau`, `contenu`, `position`, `date`, `etat`, `id_personne_physique`) VALUES
(413, 9, '', 'PHP Experts: get involved in the Paris PHP Forum 2010!', 'PHP Experts: get involved in the Paris PHP Forum 2010 ', '<p>Actively take part in PHP 15th anniversary during the Paris PHP Forum on November 9 and 10 in "la Cit&eacute; des Sciences de La Villette".</p>', '<p>This year, it is <strong>PHP </strong><strong>15th anniversary</strong> and <strong>10th anniversary of AFUP</strong> (Association of French PHP Users). For this occasion, the Afup organize the most ambitious PHP Forum ever. Many talks and debates are planned, as well as an exhibition room for teams leading open source projects to get in touch with a professionnal audience (developpers, decision makers, medias...).</p>', '<p>Are you expert for a specific PHP related domain? Did you deploy one or several PHP applications (CMS, e-commerce, CRM, EDMS) in a particular context (heavy load, famous customer, innovating project)? Are you taking part in an Open Source project? Come and share your experience!<br /><br />For the 2010 edition, the following themes will be hilighted:</p>\r\n<ul>\r\n<li><strong>PHP from A to Z:</strong> starting with PHP, successfully driving a PHP project, how to chose a hosting company?</li>\r\n<li><strong>PHP based tools:</strong> CMS and CMF, e-commerce and business tools, on-line payment, CRM and ERP</li>\r\n<li><strong>PHP Industrialization:</strong> performances, tests, single sign-on, frameworks...</li>\r\n<li><strong>PHP related technologies:</strong> Javascript, HTML 5, microformats...</li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p>To submit your own talk topic, visit <a href="../forumphp2010/appel-a-conferenciers-en.php">https://afup.org/pages/forumphp2010/appel-a-conferenciers-en.php</a> and fill the request form on-line the <strong>before June, 30 2010</strong>.<br /><br />Do you want to talk about another theme? Don''t you have any public, speaking experience? Do you need organization informations regarding your attending?</p>\r\n<p>Contact Sarah:&nbsp; organisation@afup.org</p>', 0, 1276725600, 1, 151),
(414, 9, '', 'Forum PHP 2010 : prolongation du tarif prévente !', 'forumphp2010-prolongation-tarif-prevente', '<p>Le tarif pr&eacute;vente pour le Forum PHP 2010 est prolong&eacute; jusqu''au 15 juillet 2010, profitez-en !</p>', '', '<p>L''AFUP prolonge <strong>jusqu''au 15 juillet 2010</strong> le tarif pr&eacute;vente&nbsp; : b&eacute;n&eacute;ficiez de <strong>20 &euro; de r&eacute;duction sur le pass 2 jours.</strong></p>\r\n<p>La r&eacute;duction s''applique aussi aux tarifs &eacute;tudiants, demandeurs d''emploi et membres Afup, profitez-en !</p>\r\n<p><a href="../forumphp2010/inscription.php">Inscription au Forum PHP 2010</a></p>', 0, 1277330400, 1, 516),
(415, 9, '', 'PHP TestFest 2010 à Lille le 21 août 2010', 'testFest2010', '', '<p>La <a href="http://wiki.php.net/qa/testfest-2010"><q>TestFest</q> 2010</a> fran&ccedil;aise aura lieu le samedi 21 ao&ucirc;t 2010 dans les locaux de la   soci&eacute;t&eacute; <a href="http://www.noparking.net/">No Parking</a>, situ&eacute;e sur le  site  de l''<a href="http://www.euratechnologies.com/">Euratechnologies</a> au 165  avenue de Bretagne &agrave; Lille, dans le b&acirc;timent Leblanc au  troisi&egrave;me  &eacute;tage.</p>', '<div class="post-content">\r\n<p>Elle d&eacute;butera &agrave; partir de 13 h 30, et l''<a href="../site/"><abbr title="Association Fran&ccedil;aise  des Utilisateurs de PHP">AFUP</abbr></a> vous offrira le verre de  l''amiti&eacute; lors de votre arriv&eacute;e.</p>\r\n<p>Comme la participation est gratuite, il vous suffit, si vous  souhaitez participer, de venir avec votre ordinateur portable afin de  pouvoir commencer &agrave; &eacute;crire <a href="http://blog.mageekbox.net/?post/2010/06/27/C-est-la-f%C3%AAte-du-test">vos  premiers tests</a>, dans une ambiance d&eacute;contract&eacute;e et conviviale !</p>\r\n<p>Afin de faciliter l''organisation, je vous remercie par avance  d''envoyer un courrier &eacute;lectronique &agrave; l''adresse  phpTestFest2010{aT}mageekbox[dot]net.</p>\r\n</div>', 0, 1281909600, 1, 44),
(417, 9, '', '15 ans de PHP, 10 ans d''AFUP : un programme riche pour cette anée 2010', 'forumphp2010-annonce-programme', '', '<p><strong>Rasmus Lerdorf</strong>, cr&eacute;ateur de PHP, sera l''invit&eacute; d''honneur de cette &eacute;dition anniversaire : les 9 et 10 novembre 2010, Cit&eacute; des Sciences de La Villette.</p>\r\n<p>En ouvrant un cycle de conf&eacute;rences d&eacute;di&eacute; &agrave; des profils fonctionnels,  l''Association Fran&ccedil;aise des Utilisateurs de PHP entend int&eacute;grer un  public plus large, pour initier les chefs de projets &agrave; PHP.</p>\r\n<p><strong><a href="../forumphp2010/">Le site du forum PHP 2010</a></strong></p>', '<p>&nbsp;Parmi les th&egrave;mes abord&eacute;s :</p>\r\n<ul>\r\n<li><a href="../forumphp2010/sessions.php"><strong>PHP de A &agrave; Z</strong> </a>: D&eacute;buter en PHP, R&eacute;ussir un projet avec PHP, Choisir son   h&eacute;bergement...</li>\r\n<li><a href="../forumphp2010/sessions.php"><strong>Les outils   bas&eacute;s sur PHP</strong> </a>: Drupal , outils de e-commerce et de business,   CRM et ERP...</li>\r\n<li><a href="../forumphp2010/sessions.php"><strong>L''industrialisation   de PHP</strong> </a>: Performances, tests, authentification centralis&eacute;e,   frameworks...</li>\r\n<li><a href="../forumphp2010/sessions.php"><strong>Technologies   autour de PHP</strong> </a>:  HTML 5, r&eacute;f&eacute;rencement...</li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<ul>\r\n<li><strong><a href="../forumphp2010/sessions.php">La liste des   conf&eacute;rences</a></strong></li>\r\n<li><strong><a href="../forumphp2010/deroulement.php">Le   d&eacute;roulement des journ&eacute;es</a></strong></li>\r\n<li><strong><a href="../forumphp2010/conferenciers.php">Les   conf&eacute;renciers</a></strong></li>\r\n</ul>\r\n<p><strong><br /></strong></p>\r\n<p>Pour vous inscrire, ne perdez pas de temps, <a href="../forumphp2010/inscription.php;"><strong>r&eacute;servez votre place au forum PHP</strong></a> !</p>', 0, 1283464800, 1, 12),
(418, 9, '', 'PHP Solutions Septembre 2010', 'PHP et sécurité', '', '<p>Le nouveau num&eacute;ro de PHP SOLUTIONS vient de sortir, sur le th&egrave;me "PHP et la s&eacute;curit&eacute;".</p>', '<p>En plus dans le num&eacute;ro vous trouverez, entre autres, des articles sur :</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Au sommaire :</strong><br />Les actualit&eacute;s PHP</p>\r\n<ul>\r\n<li>Cr&eacute;ation d''un composant MVC Joomla!</li>\r\n<li>&nbsp;S&eacute;curit&eacute; des sessions PHP</li>\r\n<li> S&eacute;curisation d&rsquo;un r&eacute;pertoire avec .htaccess et .htpasswd</li>\r\n<li>&nbsp;Faire communiquer Flash et PHP</li>\r\n<li>&nbsp; Usages avanc&eacute;s des sessions avec la POO</li>\r\n<li> Les applications WEB 2.0</li>\r\n<li>&nbsp; BYOOS solutions partenaire du d&eacute;veloppement DURABLE. Le logiciel OPEN SOURCE DJAFOREST au service de la protection de l''environnement !</li>\r\n</ul>\r\n<p><a href="http://phpsolmag.org/fr/magazine/1489-php-et-securite">T&eacute;l&eacute;chargement du magazine : PHP et s&eacute;curit&eacute;</a></p>', 0, 1283810400, 1, 151),
(419, 9, '', 'Les Aéroports de Lyon sous le CMS eZ Publish', 'Les Aéroports de Lyon sous le CMS eZ Publish', '', '<!-- 		@page { margin: 2cm } 		P { margin-bottom: 0.21cm } -->\r\n<p style="margin-bottom: 0cm;">Le site des A&eacute;roports de Lyon a &eacute;t&eacute; d&eacute;voil&eacute; voici quelques jours. Il a  &eacute;t&eacute; r&eacute;alis&eacute; par les soci&eacute;t&eacute;s Open Wide et Brainstorming en collaboration  avec l''A&eacute;roport de Lyon.<strong></strong></p>', '<p><br />Par ailleurs, le site internet a &eacute;t&eacute; prim&eacute; comme site du mois par l''&eacute;diteur eZ Systems en juin dernier.<br /><br />Le projet est bas&eacute; sur une solution CMS eZ Publish en PHP,&nbsp; et a vocation de promouvoir les services propos&eacute;s par les A&eacute;roports de Lyon pour ses clients. <br /><br />Le site propose &eacute;norm&eacute;ment d''informations comme :</p>\r\n<ul>\r\n<li>Les acc&egrave;s &agrave; l''a&eacute;roport</li>\r\n<li>Les diff&eacute;rents services (Boutiques, H&ocirc;tels, Restaurants)</li>\r\n<li>Les vols</li>\r\n<li>Les compagnies</li>\r\n<li>Les offres des partenaires</li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>Actuellement, le projet est en cours d''&eacute;volution pour proposer prochainement de nouvelles fonctionnalit&eacute;s sur les supports mobiles (ex iPhone) et aussi des avatars virtuels.<br /><br />http://www.lyonaeroports.com/</p>', 0, 1283896800, 1, 151),
(420, 9, '', 'Communiqué de presse : Le rendez-vous incontournable de la scène PHP fête les 15 ans de PHP !', 'Communiqué de presse : Le rendez-vous incontournable de la scène PHP fête les 15 ans de PHP', '', '', '<p>Le communiqu&eacute; de presse du Forum PHP 2010, organis&eacute; par l''AFUP (Association Fran&ccedil;aise des Utilisateurs de PHP) est d&eacute;sormais disponible : n''h&eacute;sitez pas &agrave; le faire circuler autour de vous</p>\r\n<p>&nbsp;</p>\r\n<p><a href="../../templates/forumphp2010/pdf/CP-ForumPHP_2010.pdf">T&eacute;l&eacute;charger le Communiqu&eacute; de presse</a> (PDF - 220 Ko).</p>', 0, 1284069600, 1, 151),
(421, 9, '', 'Devenez fan de l''AFUP!', 'Devenez fan de l''AFUP!', '<p>L''AFUP a d&eacute;sormais sa page Fan sur Facebook: rejoignez-nous!</p>', '', '<p>Rejoignez-nous sur <a href="http://www.facebook.com/pages/AFUP/148661101838283#!/pages/AFUP/148661101838283?v=wall">Facebook</a> et tenez vous au courant des derni&egrave;res actualit&eacute;s de l''AFUP et du Forum PHP 2010!</p>', 0, 1285020000, 1, 541),
(422, 9, '', 'Microsoft migre vers PHP', 'microsoft-migre-vers-php', '', '<p>Microsoft annonce que la plateforme de blog par d&eacute;faut des 30 millions d''utilisateurs de Live va migrer vers PHP / Wordpress.</p>', '<p>Pour Cyril PIERRE de GEYER, vice pr&eacute;sident de l''AFUP c''est une  nouvelle preuve de la force de PHP qui est, avec son &eacute;cosyst&egrave;me, la  plateforme incontournable du Web.</p>\r\n<p>&nbsp;</p>\r\n<p>Source :</p>\r\n<p><a href="http://www.readwriteweb.com/archives/microsoft_moves_its_blogging_platform_to_wordpress.php">http://www.readwriteweb.com/archives/microsoft_moves_its_blogging_platform_to_wordpress.php</a></p>', 0, 1285711200, 1, 3),
(423, 4, '', 'Apéro PHP à Nantes ', 'aperoPHPNantes', '', '<p>le  jeudi 28/10/2010 &agrave; 19:00 aura lieu un ap&eacute;ro PHP &agrave; Nantes. C''est   l''occasion de discuter et d''&eacute;changer concernant PHP et les technos   annexes.</p>\r\n<p>Inscrivez vous :</p>\r\n<p><a href="http://aperophp.net/apero.php?id=762">http://aperophp.net/apero.php?id=762</a></p>', '<p>le  jeudi 28/10/2010 &agrave; 19:00 aura lieu un ap&eacute;ro PHP &agrave; Nantes. C''est  l''occasion de discuter et d''&eacute;changer concernant PHP et les technos  annexes.</p>\r\n<p>Inscrivez vous !</p>', 0, 1287612000, 1, 44),
(424, 9, '', 'Communiqué de presse : l''AFUP reçoit en exclusivité SkySQL Ab et Monty Program Ab', 'Communiqué de presse : l AFUP recoit en exclusivité SkySQL Ab et Monty Program Ab', '', '', '<p>L''AFUP f&eacute;d&egrave;re l''ensemble des communaut&eacute;s PHP et re&ccedil;oit en exclusivit&eacute; SkySQL Ab et Monty Program Ab<br /><br />Une &eacute;dition exceptionnelle pour f&ecirc;ter les 15 ans de PHP</p>\r\n<p>&nbsp;</p>\r\n<p><a href="../../templates/forumphp2010/pdf/L-AFUP recoit SkySQL et Monty Program.pdf">T&eacute;l&eacute;charger le Communiqu&eacute; de presse</a> (PDF - 109 Ko).</p>', 0, 1287698400, 1, 151),
(425, 9, '', 'Le Forum met en avant les projets Open Source', 'Le Forum met en avant les projets Open Source', '', '', '<p>Apr&egrave;s l''appel &agrave; candidature lanc&eacute; il y a quelques semaines, la s&eacute;lection est tomb&eacute;e ! Voici les projets Open Source d&eacute;velopp&eacute;s en PHP et les communaut&eacute;s qui seront repr&eacute;sent&eacute;s lors du Forum PHP 2010, dans un espace qui leur sera enti&egrave;rement d&eacute;di&eacute; : Hoa, RBS Change, CakePHP-fr, Fine FS, Jelix, Magix CMS, Symfony et Drupal.</p>', 0, 1287612000, 1, 151),
(426, 9, '', 'SkySQL en exclusivité pour le Forum PHP 2010 !', 'SkySQL en exclusivité pour le Forum PHP 2010', '', '<p>Michael &laquo; Monty &raquo; Widenius &ndash; Monty Program Ab- et Kaj Arn&ouml; &ndash; SkySQL Ab-   nous font l''honneur d''animer ensemble la conf&eacute;rence de cl&ocirc;ture du Forum   PHP 2010, ayant pour th&egrave;me <a href="https://afup.org/pages/forumphp2010/sessions.php#511">&laquo; Etat de l''art de l''&eacute;cosyst&egrave;me MySQL &raquo;</a>.</p>', '<p>Au programme, le futur de MySQL et la pr&eacute;sentation de leur alternative &agrave;  Oracle, SkySQL.</p>\r\n<p>Que cela signifie-t-il pour l''&eacute;cosyst&egrave;me des  partenaires, d&eacute;veloppeurs, clients, utilisateurs professionnels et la  communaut&eacute; des contributeurs de MySQL ?</p>\r\n<p>Que peut-on attendre du futur de MySQL : forks, correction des bugs,  support commercial et feuille de route ?</p>', 0, 1288044000, 1, 151),
(427, 9, '', 'Forum PHP 2010 : Zeev Suraski répond présent.', 'Forum PHP 2010 : Zeev Suraski répond présent', '', '<p>Zend Technologies, partenaire du Forum PHP 2010, nous propose une conf&eacute;rence intitul&eacute;e <a href="https://afup.org/pages/forumphp2010/sessions.php#512">&laquo; Le paradoxe des performances PHP &raquo;</a>,  anim&eacute;e par Zeev Suraski (co-fondateur de Zend Technologies).</p>', '<p>Ces derni&egrave;res ann&eacute;es, de nombreuses fonctions ont &eacute;t&eacute; ajout&eacute;es &agrave; PHP 5,  mais paradoxalement, il est &eacute;galement devenu significativement plus  rapide avec chaque sortie majeure.</p>\r\n<p>&nbsp;</p>\r\n<p>Cette conf&eacute;rence d&eacute;crira les composants de PHP, la machine virtuelle de  PHP et les plus importants changements et optimisations de PHP5 li&eacute;s &agrave;  la performance.</p>', 0, 1288130400, 1, 151),
(428, 9, '', 'PHP Solutions Novembre 2010', 'PHP Solutions Novembre 2010', '', '', '<p>Le nouveau num&eacute;ro de PHP SOLUTIONS vient de sortir, avec comme dossier principal "Ajax et PHP".</p>\r\n<p>&nbsp;</p>\r\n<p>En plus dans le num&eacute;ro vous trouverez, entre autres, des articles sur :</p>\r\n<ul>\r\n<li>Cr&eacute;ez votre propre h&eacute;bergement</li>\r\n<li>AJAX et PHP</li>\r\n<li>SQL : langage de d&eacute;finition des donn&eacute;es</li>\r\n<li>Introduction &agrave; la s&eacute;curit&eacute; web</li>\r\n<li>Solution de stockage bas&eacute;e sur ZFS et Ubuntu</li>\r\n</ul>\r\n<p><a href="http://phpsolmag.org/fr/magazine/1545-ajax-et-php">T&eacute;l&eacute;chargement du magazine : Ajax et PHP</a></p>', 0, 1288652400, 1, 151),
(429, 9, '', 'Roy Rubin, fondateur de Magento, invité de dernière minute au Forum PHP 2010 !', 'Roy Rubin, fondateur de Magento, invité de dernière minute au Forum PHP 2010', '', '', '<p>Roy Rubin nous fera l''honneur de sa pr&eacute;sence lors de la conf&eacute;rence  ''Magento, un framework du E-commerce'' men&eacute;e par Hubert Desmarest et  Guillaume Babik.  Magento, ou la meilleure solution de ecommerce open  source? Tous les deux, accompagn&eacute;s de leur invit&eacute; de marque, nous en  parleront &agrave; travers l''exemple du site SmartBox.fr, d&eacute;velopp&eacute; sous  Magento  en fonction des besoins propres aux m&eacute;tiers de SmartBox.</p>', 0, 1288738800, 1, 151),
(430, 9, '', 'Weka complète notre thématique sur les performances du PHP !', 'Weka complète notre thématique sur les performances du PHP', '', '', '<p>Cette ann&eacute;e, l''AFUP souhaite notamment mettre l''accent sur  l''optimisation des performances des sites. Qui de mieux pour l''illustrer  que Weka, leader du march&eacute; fran&ccedil;ais du social gaming, accueillant tous  les jours plus de 600 000 visiteurs uniques et d&eacute;livrant plus de 30 millions de pages vues par  jour sur des applications sociales et interactives ? Comment faire face &agrave;  une telle probl&eacute;matique de tr&egrave;s forte volum&eacute;trie ? Weka nous fera  b&eacute;n&eacute;ficier de son exp&eacute;rience lors de la conf&eacute;rence ''Jeux sociaux &amp;  Cloud Computing : une histoire de scalabilit&eacute;''.</p>', 0, 1288738800, 1, 151),
(431, 9, '', 'Le Forum PHP 2010 est COMPLET !', 'Le Forum PHP 2010 est COMPLET !', '', '', '<p>Encore une fois, le Forum PHP cl&ocirc;ture ses inscriptions quelques jours  avant l''&eacute;v&egrave;nement ! Vous serez plus de 450 &agrave; nous rejoindre pour cette  &eacute;dition exceptionnelle. Rendez-vous mardi 9 et mercredi 10 novembre pour  c&eacute;l&eacute;brer avec nous les 15 ans du PHP en compagnie des meilleurs experts  mondiaux ! Et merci &agrave; vous !</p>', 0, 1288825200, 1, 516),
(432, 9, '', 'Communiqué de presse : L''AFUP propulse le Forum PHP au sommet pour sa 10ème édition', 'L''AFUP propulse le Forum PHP au sommet pour sa 10ème édition', '', '', '<p>2010 est l''ann&eacute;e de tous les records : espace d''&eacute;changes et de  mutualisation des comp&eacute;tences, le Forum PHP, via le soutien sans faille  d''une &eacute;quipe d''experts passionn&eacute;s, a r&eacute;uni les 9 et 10 novembre derniers  plus de 500 visiteurs par jour, soit 35% de plus qu''en 2009.</p>\r\n<p>&nbsp;</p>\r\n<p><a href="../../templates/forumphp2010/pdf/bilan_forum_php_2010.pdf">T&eacute;l&eacute;charger le Communiqu&eacute; de presse</a> (PDF - 85 Ko).</p>', 0, 1290034800, 1, 151),
(433, 58, '', 'Les Traits s''invitent dans PHP 5.4!', 'les-traits-sinvitent-dans-php54', '', '', '<p>L''AFUP continue sur la belle lanc&eacute;e du Forum PHP: le prochain RDV AFUP est d''ores et d&eacute;j&agrave; annonc&eacute;!<br />Il se tiendra le mercredi 15 d&eacute;cembre, &agrave; 19h30 &agrave; La Cantine &agrave; Paris.</p>\r\n<p>Stefan Marr et Fr&eacute;d&eacute;ric Hardy nous proposeront deux conf&eacute;rences sur les Traits. Fonctionnalit&eacute; propos&eacute;e par certains langages informatiques, les Traits permettent de simplifier la r&eacute;utilisation de code sans passer par l''h&eacute;ritage de classe qui pose rapidement des probl&egrave;mes de conception. Ils nous expliqueront ce que sont les Traits, ce pour quoi ils sont utiles et comment ils devraient &ecirc;tre utilis&eacute;s avec PHP.</p>\r\n<p><br />Stefan Marr est le lead-developpeur sur cette fonctionnalit&eacute; de PHP. Quant &agrave; Fr&eacute;d&eacute;ric Hardy, il est architecte d''application, administrateur syst&egrave;me et infographiste ergonome. Il est &eacute;galement l''auteur du blog&nbsp;<a style="color: #993333;" href="http://blog.mageekbox.net/">http://blog.mageekbox.net/</a>.</p>\r\n<p><br />Inscrivez vous d&egrave;s maintenant &agrave; ce Rendez-Vous AFUP en vous rendant &agrave; l''adresse&nbsp;<a style="color: #993333;" href="https://afup.org/pages/rendezvous/">https://afup.org/pages/rendezvous/</a><br /><br />La Cantine est situ&eacute;e au 151 rue Montmartre, Passage des Panoramas 12 Galerie Montmartre, 75002 Paris</p>', 0, 1291244400, 1, 541),
(434, 19, '', 'Sébastien Barbieri, RTBF: le choix de l''Open Source ', 'sebastien-barbieri-rtbf-le-choix-de-lopen-source', '', '', '<p>&nbsp;</p>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">S&eacute;bastien Barbieri travaille pour la RTBF ( Radio T&eacute;l&eacute;vision Belge Francophone) : au c&oelig;ur de l''activit&eacute; de son d&eacute;partement Nouveaux M&eacute;dias, la mise en ligne du contenu cr&eacute;&eacute; par la RTBF. Il nous parle de son activit&eacute; et de la plate-forme technique :</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">&laquo; Pour faire face &agrave; tant de contenu (&eacute;missions de radio, TV, VOD, documents des journalistes, billets, news feed, etc.), le d&eacute;partement a choisi une plate-forme technique totalement Home-Made, et principalement Open Source. La question financi&egrave;re, dans une entreprise de 2700 employ&eacute;s, est n&eacute;gligeable : ce n''est donc pas l''aspect &eacute;conomique qui a guid&eacute; ce choix, mais bien les atouts qu''offrent les logiciels Open Source. Le d&eacute;partement a en effet des besoins tr&egrave;s pr&eacute;cis auxquels les outils communs ne permettent pas de r&eacute;pondre, signalant trop rapidement leurs limites et leur manque de flexibilit&eacute;.</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Au contraire, l''Open Source dispose de documentation abondante, de support disponible facilement, et d''outils bug proof. Enfin, comme cons&eacute;quence logique et afin de travailler dans un environnement plus proche de la r&eacute;alit&eacute; (m&ecirc;me environnement que sur les serveurs) nous avons &eacute;t&eacute; amen&eacute;s &agrave; choisir Ubuntu comme station de travail.</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Le site Web de la radio Classic21 a &eacute;t&eacute; le premier &agrave; &ecirc;tre d&eacute;velopp&eacute; en PHP : il a ouvert la voie pour tous les sites de la RTBF, aujourd''hui d&eacute;velopp&eacute;s sous PHP (avec Mysql et Sqlite), tout comme le middleend, les outils d''admin, une partie du backend, les APIs... Des sites qui rencontrent un trafic important : environ 4 000 000 requ&ecirc;tes php / jour, 500 000 pages vues / jour... Il fallait donc un serveur d''application capable de tenir la charge correctement avec une technologie maitris&eacute;e de A &agrave; Z.&nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">D&rsquo;autre part, d&rsquo;un point de vue des co&ucirc;ts op&eacute;rationnels &agrave; long terme, le choix du PHP s''imposait : le PHP a le gros avantage d''&ecirc;tre un langage accessible permettant de puiser dans un pool presque infini de d&eacute;veloppeurs, de par le monde, et &agrave; des prix tr&egrave;s corrects &raquo;.&nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">S&eacute;bastien Barbieri nous explique qu''il a pu constater en Belgique que moins de 33% des membres d''une &eacute;quipe de PHP &eacute;taient dipl&ocirc;m&eacute;s en informatique, les autres &eacute;tant en majorit&eacute; des self made men ou des &eacute;tudiants ayant arr&ecirc;t&eacute;s les &eacute;tudes en informatique. Gr&acirc;ce &agrave; sa documentation importante et ses exemples foisonnants, PHP rend en effet accessible et disponible un langage pour le web s&eacute;duisant : pas de framework obligatoire, pas de guide line obligatoire... Simplicit&eacute; et efficacit&eacute;.&nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Aujourd''hui, toute l''&eacute;quipe de S&eacute;bastien Barbieri est capable de d&eacute;velopper en PHP. Et un nouveau venu n''aura besoin que de quelques jours pour apprendre...</div>\r\n<p>S&eacute;bastien Barbieri travaille pour la RTBF ( Radio T&eacute;l&eacute;vision Belge Francophone) : au c&oelig;ur de l''activit&eacute; de son d&eacute;partement Nouveaux M&eacute;dias, la mise en ligne du contenu cr&eacute;&eacute; par la RTBF. Il nous parle de son activit&eacute; et de la plate-forme technique :</p>\r\n<p><br />&laquo; Pour faire face &agrave; tant de contenu (&eacute;missions de radio, TV, VOD, documents des journalistes, billets, news feed, etc.), le d&eacute;partement a choisi une plate-forme technique totalement Home-Made, et principalement Open Source. La question financi&egrave;re, dans une entreprise de 2700 employ&eacute;s, est n&eacute;gligeable : ce n''est donc pas l''aspect &eacute;conomique qui a guid&eacute; ce choix, mais bien les atouts qu''offrent les logiciels Open Source. Le d&eacute;partement a en effet des besoins tr&egrave;s pr&eacute;cis auxquels les outils communs ne permettent pas de r&eacute;pondre, signalant trop rapidement leurs limites et leur manque de flexibilit&eacute;.</p>\r\n<p><br />Au contraire, l''Open Source dispose de documentation abondante, de support disponible facilement, et d''outils bug proof. Enfin, comme cons&eacute;quence logique et afin de travailler dans un environnement plus proche de la r&eacute;alit&eacute; (m&ecirc;me environnement que sur les serveurs) nous avons &eacute;t&eacute; amen&eacute;s &agrave; choisir Ubuntu comme station de travail.</p>\r\n<p><br />Le site Web de la radio Classic21 a &eacute;t&eacute; le premier &agrave; &ecirc;tre d&eacute;velopp&eacute; en PHP : il a ouvert la voie pour tous les sites de la RTBF, aujourd''hui d&eacute;velopp&eacute;s sous PHP (avec Mysql et Sqlite), tout comme le middleend, les outils d''admin, une partie du backend, les APIs... Des sites qui rencontrent un trafic important : environ 4 000 000 requ&ecirc;tes php / jour, 500 000 pages vues / jour... Il fallait donc un serveur d''application capable de tenir la charge correctement avec une technologie maitris&eacute;e de A &agrave; Z.&nbsp;</p>\r\n<p>D&rsquo;autre part, d&rsquo;un point de vue des co&ucirc;ts op&eacute;rationnels &agrave; long terme, le choix du PHP s''imposait : le PHP a le gros avantage d''&ecirc;tre un langage accessible permettant de puiser dans un pool presque infini de d&eacute;veloppeurs, de par le monde, et &agrave; des prix tr&egrave;s corrects &raquo;.&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>S&eacute;bastien Barbieri nous explique qu''il a pu constater en Belgique que moins de 33% des membres d''une &eacute;quipe de PHP &eacute;taient dipl&ocirc;m&eacute;s en informatique, les autres &eacute;tant en majorit&eacute; des self made men ou des &eacute;tudiants ayant arr&ecirc;t&eacute;s les &eacute;tudes en informatique. Gr&acirc;ce &agrave; sa documentation importante et ses exemples foisonnants, PHP rend en effet accessible et disponible un langage pour le web s&eacute;duisant : pas de framework obligatoire, pas de guide line obligatoire... Simplicit&eacute; et efficacit&eacute;.&nbsp;</p>\r\n<p>Aujourd''hui, toute l''&eacute;quipe de S&eacute;bastien Barbieri est capable de d&eacute;velopper en PHP. Et un nouveau venu n''aura besoin que de quelques jours pour apprendre...</p>\r\n<p>&nbsp;</p>', 0, 1292281200, 1, 541),
(435, 9, '', 'Le Forum PHP 2010, filmé à un rythme d''enfer', 'forum-php-2010-filme-a-un-rythme-denfer', '', '', '<p>Le journaliste Ludovic Tichit a&nbsp;couvert l''actualit&eacute; du Libre tout le mois de novembre 2010 pour le magazine DSIsionnel. Vid&eacute;o d&eacute;cal&eacute;e et d&eacute;coiffante, avec l''elephpant et Rasmus Lerdorf en guest stars! C''est par ici: <a href="http://www.dsisionnel.com/Article,42,Paris,-novembre-2010-:-l''Open-Source-dans-tous-ses-etats.html">http://www.dsisionnel.com/Article,42,Paris,-novembre-2010-:-l''Open-Source-dans-tous-ses-etats.html</a></p>', 0, 1294354800, 1, 541),
(436, 19, '', 'Pixmania, une confiance historique en PHP', 'Pixmania-une-confiance-historique-en-PHP', '', '', '<p>&nbsp;</p>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">+ pourriez-vous, dans un premier temps, nous pr&eacute;senter votre profil ainsi que celui de votre soci&eacute;t&eacute;?</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Je m''appelle Eric Tinoco, je suis chef de projet IT des sites e-commerce UK/IE du groupe Dixons Retail. Dixons Retail, ce sont 40 000 personnes, employ&eacute;es dans plusieurs groupes, dont le groupe Pixmania (1400 employ&eacute;s) qui lui-m&ecirc;me poss&egrave;de la soci&eacute;t&eacute; E-Merchant (200 employ&eacute;s). Le chiffre d''affaire de Pixmania est d''environ 900 millions d''euros.</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">+ Quelle est, plus en d&eacute;tails, l''activit&eacute; de votre d&eacute;partement ?</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Mon d&eacute;partement est le service IT du groupe PIXMANIA, en charge du d&eacute;veloppement de la plateforme e-commerce "E-Merchant" qui inclus PIXMANIA, les sites du groupe DSG, la partie e/Commerce de Bouygues Telecom &hellip;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">+ Quelles sont les caract&eacute;ristiques de votre plate-forme technique ?</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">ORACLE / PHP5 / SQL RELAY / APACHE / LIGHTTPD</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">+ Apparemment la grande majorit&eacute; de votre plate-forme tourne gr&acirc;ce au logiciel Open Source. Pourquoi ? ( choix technique ou financier ? )</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Le choix de l''Open Source est un choix historique, qui a &eacute;t&eacute; fait d&egrave;s la fondation du groupe. Il s''inscrit dans la volont&eacute; de d&eacute;velopper une plateforme innovante tout en s''appuyant sur des technologies d''avenir et tr&egrave;s document&eacute;es.</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">+ Quel est le premier projet sur lequel vous avez mis en &oelig;uvre du PHP ?</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Le premier projet PHP de Pixmania est le site lui-m&ecirc;me qui, depuis sa cr&eacute;ation, est b&acirc;ti en PHP. &nbsp;C''est un projet qui a d&eacute;but&eacute; il y a 10 ans maintenant et qui suit les &eacute;volutions PHP au fur et &agrave; mesure. &nbsp;Une migration en PHP 5.3 est d''ailleurs dans la roadmap de notre plateforme (qui inclut le site Pixmania.com)</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">+ Pouvez-vous lister rapidement les diff&eacute;rents projets / applications dans lesquels vous utilisez PHP aujourd''hui ?</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Nous utilisons PHP pour g&eacute;rer aussi bien le front office des sites www.dixons.co.uk, www.currys.co.uk &amp; www.pcworld.co.uk, que pour le back office (Content Management system, Order Management, Product Management, Cron management et jobs multi-interfaces).</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">+ Quelle est la volum&eacute;trie de ces projets ?</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Sur la journ&eacute;e la plus charg&eacute;e, &ccedil;a peut grimper &agrave; plus de 2.5 millions de visiteurs uniques et plus de 20 millions de pages vues sur la partie front office.&nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Sur la partie back office, certaines applications tournent avec plus de 1000 utilisateurs simultan&eacute;s pendant les heures ouvr&eacute;es.</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">+ Pourquoi avoir retenu ce serveur d''application ?</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">PHP reste une technologie d''avenir : les versions &eacute;voluent continuellement et l''exp&eacute;rience s''accumule, le mod&egrave;le objet par exemple est devenu un point fort. Au niveau de la volum&eacute;trie des transactions, Oracle continue de nous suivre : peu de soucis en perspective c&ocirc;t&eacute; base de donn&eacute;es.</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">+ Un r&eacute;cent sondage sur hotscripts.com d&eacute;note que PHP est le langage pr&eacute;f&eacute;r&eacute; des informaticiens (56,9%avec 15500 voies), avez-vous ce sentiment chez vous ?</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">OUIIIIIIIII :)&nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">+ Quel est le ratio de votre &eacute;quipe technique qui est susceptible de d&eacute;velopper en PHP ? Pouvez-vous le comparer aux autres langages que vous utilisez (Perl, C ... ) ?</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">100% des membres de l''&eacute;quipe d&eacute;veloppent en PHP. 60% d''entre eux savent d&eacute;velopper en C/C++ , et 20% en JAVA.</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">+ Quelles sont les principales briques logicielles que vous utilisez ? (application : Phorum, visiteur, FUDForum, Wordpress... / framework : zend, symfony, ez components, PEAR...) ?&nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Nous utilisons principalement un framework maison et Code Igniter.</div>\r\n<p><strong> Pourriez-vous, dans un premier temps, nous pr&eacute;senter votre profil ainsi que celui de votre soci&eacute;t&eacute;?</strong></p>\r\n<p>Je m''appelle Eric Tinoco, je suis chef de projet IT des sites e-commerce UK/IE du groupe Dixons Retail. Dixons Retail, ce sont 40 000 personnes, employ&eacute;es dans plusieurs groupes, dont le groupe Pixmania (1400 employ&eacute;s) qui lui-m&ecirc;me poss&egrave;de la soci&eacute;t&eacute; E-Merchant (200 employ&eacute;s). Le chiffre d''affaire de Pixmania est d''environ 900 millions d''euros.</p>\r\n<p><strong>Quelle est, plus en d&eacute;tails, l''activit&eacute; de votre d&eacute;partement ?</strong></p>\r\n<p><strong>&nbsp;</strong>Mon d&eacute;partement est le service IT du groupe PIXMANIA, en charge du d&eacute;veloppement de la plateforme e-commerce "E-Merchant" qui inclus PIXMANIA, les sites du groupe DSG, la partie e/Commerce de Bouygues Telecom &hellip;</p>\r\n<p><strong>Quelles sont les caract&eacute;ristiques de votre plate-forme technique ?</strong></p>\r\n<p><strong>&nbsp;</strong>ORACLE / PHP5 / SQL RELAY / APACHE / LIGHTTPD</p>\r\n<p><strong>Apparemment la grande majorit&eacute; de votre plate-forme tourne gr&acirc;ce au logiciel Open Source. Pourquoi ? ( choix technique ou financier ?</strong><strong>)</strong></p>\r\n<p><strong>&nbsp;</strong>Le choix de l''Open Source est un choix historique, qui a &eacute;t&eacute; fait d&egrave;s la fondation du groupe. Il s''inscrit dans la volont&eacute; de d&eacute;velopper une plateforme innovante tout en s''appuyant sur des technologies d''avenir et tr&egrave;s document&eacute;es.</p>\r\n<p><strong> Quel est le premier projet sur lequel vous avez mis en &oelig;uvre du PHP ?</strong></p>\r\n<p><strong>&nbsp;</strong>Le premier projet PHP de Pixmania est le site lui-m&ecirc;me qui, depuis sa cr&eacute;ation, est b&acirc;ti en PHP. &nbsp;C''est un projet qui a d&eacute;but&eacute; il y a 10 ans maintenant et qui suit les &eacute;volutions PHP au fur et &agrave; mesure. &nbsp;Une migration en PHP 5.3 est d''ailleurs dans la roadmap de notre plateforme (qui inclut le site Pixmania.com)</p>\r\n<p><strong>Pouvez-vous lister rapidement les diff&eacute;rents projets / applications dans lesquels vous utilisez PHP aujourd''hui ?</strong></p>\r\n<p><strong>&nbsp;</strong>Nous utilisons PHP pour g&eacute;rer aussi bien le front office des sites www.dixons.co.uk, www.currys.co.uk &amp; www.pcworld.co.uk, que pour le back office (Content Management system, Order Management, Product Management, Cron management et jobs multi-interfaces).</p>\r\n<p><strong>Quelle est la volum&eacute;trie de ces projets ?</strong></p>\r\n<p>Sur la journ&eacute;e la plus charg&eacute;e, &ccedil;a peut grimper &agrave; plus de 2.5 millions de visiteurs uniques et plus de 20 millions de pages vues sur la partie front office.&nbsp;Sur la partie back office, certaines applications tournent avec plus de 1000 utilisateurs simultan&eacute;s pendant les heures ouvr&eacute;es.</p>\r\n<p><strong>Pourquoi avoir retenu ce serveur d''application ?</strong></p>\r\n<p>PHP reste une technologie d''avenir : les versions &eacute;voluent continuellement et l''exp&eacute;rience s''accumule, le mod&egrave;le objet par exemple est devenu un point fort. Au niveau de la volum&eacute;trie des transactions, Oracle continue de nous suivre : peu de soucis en perspective c&ocirc;t&eacute; base de donn&eacute;es.</p>\r\n<p><strong>Un r&eacute;cent sondage sur hotscripts.com d&eacute;note que PHP est le langage pr&eacute;f&eacute;r&eacute; des informaticiens (56,9%avec 15500 voies), avez-vous ce sentiment chez vous ?</strong></p>\r\n<p>OUIIIIIIIII :)&nbsp;</p>\r\n<p><strong>Quel est le ratio de votre &eacute;quipe technique qui est susceptible de d&eacute;velopper en PHP ? Pouvez-vous le comparer aux autres langages que vous utilisez (Perl, C ... ) ?</strong></p>\r\n<p><strong>&nbsp;</strong>100% des membres de l''&eacute;quipe d&eacute;veloppent en PHP. 60% d''entre eux savent d&eacute;velopper en C/C++ , et 20% en JAVA.</p>\r\n<p><strong>Quelles sont les principales briques logicielles que vous utilisez ? (application : Phorum, visiteur, FUDForum, Wordpress... / framework : zend, symfony, ez components, PEAR...) ?&nbsp;</strong></p>\r\n<p><strong>&nbsp;</strong>Nous utilisons principalement un framework maison et Code Igniter.</p>', 0, 1294700400, 1, 541),
(437, 9, '', 'Lillois, prochain apéro PHP le 20 janvier 2011!', 'Lillois-prochain-apéro-PHP-le-20-janvier-2011', '', '', '<p>Le prochain ap&eacute;ro PHP lillois aura lieu le jeudi 20 janvier &agrave; 19h au Caf&eacute; Citoyen, 7 Place du Vieux March&eacute; aux Chevaux &agrave; Lille.</p>\r\n<p>Une tr&egrave;s bonne opportunit&eacute; pour se souhaiter la bonne ann&eacute;e, discuter PHP et boire un verre dans une ambiance conviviale!</p>\r\n<p>Pour s''inscrire, c''est par ici: <a href="http://aperophp.net/apero.php?id=782">http://aperophp.net/apero.php?id=782</a></p>', 0, 1294700400, 1, 541),
(438, 9, '', 'Nantes lance un resto PHP le 27 janvier', 'Nantes-lance-un-resto-PHP-le-27-janvier', '', '', '<div style="color: #000000; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 10px; background-image: initial; background-attachment: initial; background-origin: initial; background-clip: initial; background-color: #ffffff; margin: 8px;">\r\n<p>L''ap&eacute;ro ne durait pas assez longtemps pour pouvoir discuter suffisamment de PHP: nos membres nantais se lancent donc dans le resto PHP! RDV pour le tout premier du genre au Flesselles le jeudi 27 janvier. Confirmez votre venue sur le site Ap&eacute;ro PHP: <a href="http://aperophp.net/apero.php?id=802">http://aperophp.net/apero.php?id=802</a></p>\r\n</div>', 0, 1294873200, 1, 541);
INSERT INTO `afup_site_article` (`id`, `id_site_rubrique`, `surtitre`, `titre`, `raccourci`, `descriptif`, `chapeau`, `contenu`, `position`, `date`, `etat`, `id_personne_physique`) VALUES
(440, 19, '', 'Plus de 700 outils développés sous PHP chez BNP Paribas', 'plus-de-700-outils-développés-sous-PHP-chez-BNPParibas', '', '', '<p>&nbsp;</p>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Mon d&eacute;partement s''occupe de fournir les services informatiques pour le groupe BNP Paribas. &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Quelles sont les caract&eacute;ristiques de votre plate-forme technique ? &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Le package standard pour les serveurs PHP est le suivant : &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">RHEL 4.8 (en cours de migration vers 5.0) &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Zend Core 2.5 / Zend Platform 3.6 (en cours de migration vers Zend Server 5.0) &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">A noter toutefois, qu''il existe d''autres plates-formes non bas&eacute;es sur ces produits (une plate-forme sous Microsoft Windows Server, une autre sous Solaris). &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Apparemment la grande majorit&eacute; de votre plate-forme tourne gr&acirc;ce au logiciel Open Source. Pourquoi ? ( choix technique ou financier ? ) &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">La technologie PHP a &eacute;t&eacute; choisie pour offrir une contrepartie plus &eacute;conomique &agrave; Java couramment utilis&eacute; au sein du groupe (IBM Websphere, Oracle). Le choix technologique pour la mise en oeuvre de PHP a &eacute;t&eacute; fait de mani&egrave;re &agrave; r&eacute;duire les co&ucirc;ts tout en s''assurant le support de Redhat pour Linux et de Zend pour PHP. &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Quel est le premier projet sur lequel vous avez mis en &oelig;uvre du PHP ? &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">A ma connaissance, la premi&egrave;re application PHP d&eacute;velopp&eacute;e dans le groupe fut une application pour g&eacute;rer le processus de commande de cartes de visites dans le r&eacute;seau d''agences BNP Paribas en 2001. &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Pouvez-vous lister rapidement les diff&eacute;rents projets / applications dans lesquels vous utilisez PHP aujourd''hui ? &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Le nombre d''application PHP dans le groupe est estim&eacute; &agrave; environ 700 outils. Les applications vont du site institutionnel simple aux workflows plus ou moins complexes. &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Voici quelques exemples notables : &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">=&gt; portail Intranet du groupe (site institutionnel localis&eacute; suivant la localisation du collaborateur. Ce site est la page d''accueil du navigateur de l''ensemble des collaborateurs du groupe) &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">=&gt; site de l''Atelier (www.atelier.fr) sur Internet. &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">=&gt; site des cartes de voeux BNP Paribas permettant aux collaborateurs d''envoyer des voeux &agrave; des personnes &agrave; l''int&eacute;rieur et &agrave; l''ext&eacute;rieur du groupe (op&eacute;ration renouvel&eacute;e tous les ans depuis 2002) &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Quelle est la volum&eacute;trie de ces projets ? (nombre de connexions, users simultan&eacute;s, pages vues, etc.) &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">A titre d''exemple, le portail Intranet du groupe enregistre environ 3 &agrave; 4 millions de hits par jours. La population des utilisateurs de cette application est l''ensemble des collaborateurs du groupe. &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Les workflows les plus complexes et les plus utilis&eacute;s comptabilisent dans les 400 000 requ&ecirc;tes PHP par jour, pour 1 000 000 de hits. &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Pourquoi avoir retenu ce serveur d''application ? &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Le choix de Zend a &eacute;t&eacute; naturel &eacute;tant donn&eacute;e son implication toute particuli&egrave;re dans l''univers PHP. &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Avec quoi utilisez vous PHP ? (Oracle, XML, Postgres, Mysql, ...) &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Les SGBD utilis&eacute;s avec PHP sont essentiellement Oracle (standard groupe) et MySQL (mais consid&eacute;r&eacute; comme non standard groupe). &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Un r&eacute;cent sondage sur hotscripts.com d&eacute;note que PHP est le langage pr&eacute;f&eacute;r&eacute; des informaticiens (56,9%avec 15500 voies), avez-vous ce sentiment chez vous ? &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">La culture dans l''entreprise favorise grandement Java pour tous les d&eacute;veloppements consid&eacute;r&eacute;s comme sensibles. La population d''informaticiens du groupe est donc naturellement plut&ocirc;t orient&eacute;e Java &agrave; ce jour. &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Quelles sont les principales briques logicielles que vous utilisez ? (application : Phorum, visiteur, FUDForum, Wordpress... / framework : zend, symfony, ez components, PEAR...) ? &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">La grande disparit&eacute; de l''utilisation de PHP au sein du groupe rend la r&eacute;ponse &agrave; cette question assez ardue. &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Quoi qu''il en soit, notre entit&eacute; fournit aux d&eacute;veloppeurs un framework maison qui est bas&eacute; sur ZendFramework. &nbsp;</div>\r\n<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">De plus, parmi les applications sur lesquelles j''ai une certaine visibilit&eacute;, je peux dire que les produits suivants sont utilis&eacute;s : Synfony, Drupal, Wordpress.&nbsp;</div>\r\n<p><strong>Bonjour, Yannick Mahe. Pourriez-vous, dans un premier temps, nous pr&eacute;senter le profil de votre soci&eacute;t&eacute;?</strong></p>\r\n<p>BNP Paribas est pr&eacute;sent dans plus de 80 pays dans le monde et compte plus de 200 000 collaborateurs. &nbsp;<br /><br /><strong>Quelles est, plus en d&eacute;tails, l''activit&eacute; de votre d&eacute;partement ? &nbsp;</strong></p>\r\n<p>Mon d&eacute;partement s''occupe de fournir les services informatiques pour le groupe BNP Paribas. &nbsp;<br /><br /><strong>Quelles sont les caract&eacute;ristiques de votre plate-forme technique ? </strong>&nbsp;<br /><br />Le package standard pour les serveurs PHP est le suivant : &nbsp;<br />RHEL 4.8 (en cours de migration vers 5.0)&nbsp;<br />Zend Core 2.5 &nbsp;<br />Zend Platform 3.6 (en cours de migration vers Zend Server 5.0) &nbsp;<br />A noter toutefois, qu''il existe d''autres plates-formes non bas&eacute;es sur ces produits (une plate-forme sous Microsoft Windows Server, une autre sous Solaris). <br />&nbsp;<br /><strong>Apparemment la grande majorit&eacute; de votre plate-forme tourne gr&acirc;ce au logiciel Open Source. Pourquoi ? ( choix technique ou financier ? ) &nbsp;<br /></strong><br />La technologie PHP a &eacute;t&eacute; choisie pour offrir une contrepartie plus &eacute;conomique &agrave; Java couramment utilis&eacute; au sein du groupe (IBM Websphere, Oracle). Le choix technologique pour la mise en oeuvre de PHP a &eacute;t&eacute; fait de mani&egrave;re &agrave; r&eacute;duire les co&ucirc;ts tout en s''assurant le support de Redhat pour Linux et de Zend pour PHP. &nbsp;<br /><br /><strong>Quel est le premier projet sur lequel vous avez mis en &oelig;uvre du PHP? &nbsp;<br /></strong><br />A ma connaissance, la premi&egrave;re application PHP d&eacute;velopp&eacute;e dans le groupe fut une application pour g&eacute;rer le processus de commande de cartes de visites dans le r&eacute;seau d''agences BNP Paribas en 2001. &nbsp;<br /><br /><strong>Pouvez-vous lister rapidement les diff&eacute;rents projets / applications dans lesquels vous utilisez PHP aujourd''hui ?<br /></strong><br />Le nombre d''application PHP dans le groupe est estim&eacute; &agrave; environ 700 outils. Les applications vont du site institutionnel simple aux workflows plus ou moins complexes. &nbsp;<br />Voici quelques exemples notables : &nbsp;</p>\r\n<ul>\r\n<li>portail Intranet du groupe (site institutionnel localis&eacute; suivant la localisation du collaborateur. Ce site est la page d''accueil du navigateur de l''ensemble des collaborateurs du groupe) &nbsp;</li>\r\n<li>&nbsp;site de l''Atelier (www.atelier.fr) sur Internet. &nbsp;</li>\r\n<li>site des cartes de voeux BNP Paribas permettant aux collaborateurs d''envoyer des voeux &agrave; des personnes &agrave; l''int&eacute;rieur et &agrave; l''ext&eacute;rieur du groupe (op&eacute;ration renouvel&eacute;e tous les ans depuis 2002) &nbsp;</li>\r\n</ul>\r\n<p>&nbsp;</p>\r\n<p><strong>Quelle est la volum&eacute;trie de ces projets ? (nombre de connexions, users simultan&eacute;s, pages vues, etc.) </strong>&nbsp;<br /><br />A titre d''exemple, le portail Intranet du groupe enregistre environ 3 &agrave; 4 millions de hits par jours. La population des utilisateurs de cette application est l''ensemble des collaborateurs du groupe. &nbsp;Les workflows les plus complexes et les plus utilis&eacute;s comptabilisent dans les 400 000 requ&ecirc;tes PHP par jour, pour 1 000 000 de hits. <br />&nbsp;<br /><strong>Pourquoi avoir retenu ce serveur d''application ? <br />&nbsp;</strong><br />Le choix de Zend a &eacute;t&eacute; naturel &eacute;tant donn&eacute;e son implication toute particuli&egrave;re dans l''univers PHP. &nbsp;<br /><br /><strong>Avec quoi utilisez vous PHP ? (Oracle, XML, Postgres, Mysql, ...) &nbsp;</strong><br /><br />Les SGBD utilis&eacute;s avec PHP sont essentiellement Oracle (standard groupe) et MySQL (mais consid&eacute;r&eacute; comme non standard groupe). &nbsp;<br /><br /><strong>Un r&eacute;cent sondage sur hotscripts.com d&eacute;note que PHP est le langage pr&eacute;f&eacute;r&eacute; des informaticiens (56,9%avec 15500 voies), avez-vous ce sentiment chez vous ? &nbsp;<br /></strong><br />La culture dans l''entreprise favorise grandement Java pour tous les d&eacute;veloppements consid&eacute;r&eacute;s comme sensibles. La population d''informaticiens du groupe est donc naturellement plut&ocirc;t orient&eacute;e Java &agrave; ce jour. &nbsp;<br /><br /><strong>Quelles sont les principales briques logicielles que vous utilisez ? (application : Phorum, visiteur, FUDForum, Wordpress... / framework : zend, symfony, ez components, PEAR...) ? &nbsp;<br /></strong><br />La grande disparit&eacute; de l''utilisation de PHP au sein du groupe rend la r&eacute;ponse &agrave; cette question assez ardue. &nbsp;Quoi qu''il en soit, notre entit&eacute; fournit aux d&eacute;veloppeurs un framework maison qui est bas&eacute; sur ZendFramework. &nbsp;<br />De plus, parmi les applications sur lesquelles j''ai une certaine visibilit&eacute;, je peux dire que les produits suivants sont utilis&eacute;s : Synfony, Drupal, Wordpress.&nbsp;</p>', 0, 1295478000, 1, 541),
(441, 9, '', 'L''Assemblée Générale et la journée de développement sont annoncées le 26 février', 'lassemblee-generale-et-la-journee-de-developpement-sont-annoncees', '', '', '<p>Bloquez d''ores et d&eacute;j&agrave; votre 26 f&eacute;vrier: l''AFUP a besoin de vous! En effet, l''Assembl&eacute;e G&eacute;n&eacute;rale 2011 de l''AFUP se tiendra le samedi 26 f&eacute;vrier &agrave; 18h30, au sein de la Maison des Associations Solidaires. La MAS est situ&eacute;e au 10/18, rue des terres au cur&eacute;, Paris XIII&egrave;me.&nbsp;Chaque membre de l''AFUP est donc convi&eacute; &agrave; y participer, ou en cas d''impossibilit&eacute;, &agrave; transmettre son pouvoir pour l''&eacute;lection du nouveau bureau.&nbsp;</p>\r\n<p>L''ordre du jour de l''Assembl&eacute;e G&eacute;n&eacute;rale est le suivant:&nbsp;<br />- Bilan moral pr&eacute;sent&eacute; par le Pr&eacute;sident<br />- Bilan financier pr&eacute;sent&eacute; par le Tr&eacute;sorier<br />- Election du nouveau bureau<br />- Pr&eacute;sentation de l''activit&eacute; 2011<br />- Discussion sur le Forum PHP 2011</p>\r\n<p>Comme chaque ann&eacute;e, l''AG sera pr&eacute;c&eacute;d&eacute;e par la journ&eacute;e de d&eacute;veloppement de l''AFUP. D&egrave;s 9h, toujours &agrave; la MAS, nous vous proposons de nous rejoindre pour discuter ensemble des am&eacute;liorations &agrave; effectuer sur le site de l''AFUP, de l''ap&eacute;ro PHP, du back-office, etc, et de d&eacute;velopper de nouvelles fonctionnalit&eacute;s dans la journ&eacute;e! Pizzas, boissons et ambiance conviviale assur&eacute;es. inscrivez-vous d&egrave;s maintenant sur le Wiki, ou contactez-nous &agrave; communication@afup.org</p>', 0, 1295910000, 1, 541);

-- --------------------------------------------------------

--
-- Structure de la table `afup_site_feuille`
--

CREATE TABLE IF NOT EXISTS `afup_site_feuille` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) DEFAULT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `lien` varchar(255) DEFAULT NULL,
  `alt` varchar(255) DEFAULT NULL,
  `position` mediumint(9) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `etat` tinyint(4) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Contenu de la table `afup_site_feuille`
--

INSERT INTO `afup_site_feuille` (`id`, `id_parent`, `nom`, `lien`, `alt`, `position`, `date`, `etat`, `image`) VALUES
(1, 0, 'Colonne de droite', '/', 'Colonne de droite', 0, 978303600, 1, NULL),
(18, 1, 'Livre blanc', 'livre-blanc-php/62', '', 0, 1253916000, 1, 'livre-blanc.png'),
(22, 21, 'Retours d''expérience', 'retours-d-experience/19', '', 0, 1254002400, 1, NULL),
(23, 21, 'Annuaire prestataires', '/pages/annuaire/', '', 0, 1254002400, 1, NULL),
(3, 1, 'Annuaire prestataires', '/pages/annuaire/', 'Annuaires des prestataires', 1, 978303600, 1, 'pastille_prestataires.gif'),
(5, 0, 'Colonne de gauche - bas', '/', '', 0, NULL, NULL, NULL),
(21, NULL, 'Colonne de gauche - haut', '/', NULL, NULL, NULL, NULL, NULL),
(6, 5, 'Rendez-vous', 'evenements/58', 'Evènements organisés par l''AFUP', 1, 978303600, 1, NULL),
(7, 5, 'Vie associative', 'vie-associative/4', 'Vie associative', 2, 978303600, 1, NULL),
(8, 5, 'Devenir membre', 'vie-associative/56/devenir-membre', 'Devenir membre', 3, 978303600, 1, NULL),
(9, 0, 'Entreprises qui font du PHP', '/', '', 0, 1253916000, 1, NULL),
(12, 9, 'IBM', 'retours-d-exp/326/php-et-ibm-quelles-interactions-possibles', '', 0, 1253916000, 1, 'ibm.gif'),
(13, 9, 'Itool', 'retours-d-exp/282/mod', '', 0, 1253916000, 1, 'itool.gif'),
(14, 9, 'LeMonde.fr', 'retours-d-exp/289/lemonde-fr-ajoute-une-couche-xul-sur-son-back-office-php', '', 0, 1253916000, 1, 'le-monde.gif'),
(15, 9, 'SugarCRM', 'retours-d-exp/313/sugarcrm-un-logiciel-crm-performant-commercial-et-open-source', '', 0, 1253916000, 1, 'sugarcrm.gif'),
(16, 9, 'Etat français', 'retours-d-exp/317/l-etat-fran', '', 0, 1253916000, 1, 'etat-francais.gif'),
(17, 9, 'Flickr', 'retours-d-exp/297/flickr-le-service-de-partage-de-photo-de-yahoo-utilise-php', '', 0, 1253916000, 1, 'flickr.gif'),
(19, 1, 'Vidéos', 'http://www.phptv.fr/', '', 0, 1253916000, 1, 'videos.png'),
(20, 1, 'Forum PHP', '/pages/forumphp2010/', '', -1, 1253916000, 1, 'forumphp.png'),
(24, 21, 'Actualités', '06-actualit/9', '', 0, 1254002400, 1, NULL),
(25, 5, 'Contact', 'faq/53/comment-contacter-l-afup', '', 4, 1254088800, 1, NULL),
(26, 5, 'Espace Membres', '/admin', '', 9, 1266015600, 1, NULL),
(27, 1, 'C''était Hier', 'http://dai.ly/aybtwy', '', -2, 1291244400, 1, 'afup_cetait_hier.jpg'),
(30, 1, 'Rendez-Vous', 'evenements/58', '', 0, 1292367600, 1, 'afup_rendez_vous.jpg'),
(29, 9, 'rtbf', 'retours-d-exp/434/sebastien-barbieri-rtbf-le-choix-de-lopen-source', '', 0, 1292281200, 1, 'logo_rtbf_be49px.jpg'),
(31, 9, 'Pixmania', 'https://afup.org/pages/site/?route=retours-d-experience/436/Pixmania-une-confiance-historique-en-PHP', '', 0, 1294873200, 1, 'e-merchant49px2'),
(32, 9, 'BNP Paribas', 'https://afup.org/pages/site/?route=rubrique/440/plus-de-700-outils-développés-sous-PHP-chez-BNPParibas', '', 0, 1295478000, 1, 'BNPP_BL_Q49px2.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `afup_site_rubrique`
--

CREATE TABLE IF NOT EXISTS `afup_site_rubrique` (
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

--
-- Contenu de la table `afup_site_rubrique`
--

INSERT INTO `afup_site_rubrique` (`id`, `id_parent`, `nom`, `raccourci`, `contenu`, `descriptif`, `position`, `date`, `etat`, `id_personne_physique`, `icone`) VALUES
(4, 0, 'Vie associative', 'vie-associative-afup', 'L''AFUP, Association Française des Utilisateurs de PHP, est une association dont le principal but est de promouvoir le langage PHP auprès des professionnels et de participer à son développement.\r\n\r\nVous trouverez ici une présentation de l''Association Française des Utilisateurs de PHP (AFUP), ses statuts, ses objectifs et ses moyens.', 'Vie au sein l''Association Française des Utilisateurs de PHP', 9, 1266015600, 1, 0, ''),
(6, 4, 'FAQ', 'faq', '', 'Retrouvez ici les réponses aux questions fréquentes que chacun se pose à propos de l''association, de ses objectifs à ses moyens en passant par ses méthodes.', 0, 1266056802, 1, NULL, NULL),
(10, 0, '08. Groupes de travail', '08-groupes-de-travail', 'Afin de canaliser les énergies et de coordonner les actions, l''AFUP dispose de Groupes de Travail.\r\n\r\nCes groupes sont formés sur la base du volontariat et permettent de rassembler les membres plus actifs dans les domaines concernés.\r\n\r\nVoici la liste des groupes actuellement constitués :', 'Les différents Groupes de travail de l''AFUP se répartissent les tâches courantes', 0, 1266056802, 1, NULL, NULL),
(9, 0, 'Actualités', 'actualites', '<p>L’actualité de PHP est généralement très riche. L’AFUP à pour objectif, à travers cette rubrique, de vous proposer des actualités sur les points suivants :</p>\r\n\r\n<ul>\r\n<li>Conférences, forums et salons en rapport avec PHP</li>\r\n<li>Rendez-vous AFUP</li>\r\n<li>Sorties majeures de PHP</li>\r\n<li>Annonces en rapport avec PHP</li>\r\n</ul>\r\n\r\n\r\n', 'Soyez informés des nouveautés PHP', 9, 1266015600, 1, 0, 'actualites.png'),
(12, 10, 'Communication Externe', 'communication-externe', '', 'Groupe de travail chargé d''actions de communication ciblées sur les professionnels et institutionnels.', 0, 1266056802, 1, NULL, NULL),
(19, 0, 'Retours d''expérience', 'retours-d-experience', 'Qui utilise PHP et pourquoi ? Dans cette rubrique de nombreuses sociétés ont accepté de répondre à nos questions concernant leur utilisation de PHP.', 'Ils font confiance à PHP', 9, 1266015600, 1, 0, ''),
(22, 0, '_Divers', 'divers', '', 'Rubrique servant à la rédaction d''articles divers.', 0, 1266056802, 1, NULL, NULL),
(27, 12, '02. Projets en cours', '02-projets-en-cours', '', '', 0, 1266056802, 1, NULL, NULL),
(46, 53, 'PHP - Questions fréquentes', 'php-questions-fr-quentes', '', 'FAQ PHP', 0, 1266056802, 1, NULL, NULL),
(53, 0, '01. PHP', '01-php', 'PHP est une plateforme de développement informatique principalement dédiée au Web. Il est distribué via une licence propre qui permet sa rediffusion, son utilisation et sa modification librement et gratuitement.\r\n\r\nVous trouverez ici :\r\n<ul>\n<li>Toutes les informations relatives à son installation et aux différentes solutions non commerciales permettant d''obtenir du support.\r</li>\n<li>Des liens vous permettant de télécharger PHP and co.\r</li>\n<li>Une FAQ.</li>\n</ul>', 'Tout pour PHP', 0, 1266056802, 1, NULL, NULL),
(54, 52, 'Forum PHP 2001 ', 'forum-php-2001', 'Pour la première fois en France un salon sur le langage le plus dynamique du web à eu lieu fin 2001 organisé par la société sezam france.\r\n\r\nLe FORUM PHP 2001 à accueilli les principaux développeurs et spécialistes PHP du monde :\r\nRasmus Lerdorf, Thies C. ARNTZEN (PHPGroup),\r\nZeev SURASKI (PHPGroup, Zend), Armel FAUVEAU (Globalis),\r\nHellekin WOLF (Assurance Qualité PHP), Nicolas Hoizey (Clever age),\r\nCyril PIERRE de GEYER (Kaptive Kaptive formation), Raphael GOULAIS (Alcove), Habib GUERGACHI (SQLI),...\r\n\r\nDe nombreux sujets ont été traités, vous pouvez retrouver une partie des supports de présentation dans la partie droite.\r\n', 'Forum PHP 2001- 11/2001', 0, 1266056802, 1, NULL, NULL),
(56, 52, 'Forum PHP 2003', 'forum-php-2003', 'Le forum PHP 2003 a été organisé les 26 et 27 Novembre 2003 au club confair à Paris.\r\n\r\nLe Forum PHP 2003 a permi de découvrir cette plate-forme de développement au travers de 3 prismes : \r\n\r\n<ul>\n<li>son co-créateur Zeev Suraski, \r</li>\n<li>des retours d’entreprises : Cermex, Capitol, FM Logistic, ...\r</li>\n<li>des conférences dispensées par les meilleurs experts français.\r</li>\n</ul>\n\r\nDeux temps forts ont ponctués cette 3ème édition du Forum PHP : \r\n\r\n<ul>\n<li>la 1ère démonstration publique de PHP 5 en France par Zeev Suraski (co-créateur de PHP), \r</li>\n<li>la 1ère démonstration publique en Europe de MySQL 5 par Guilhem Bichot, co-développeur de MySQL 5.\r</li>\n</ul>\n\r\nDevant la demande croissante des entreprises de trouver des solutions bâties avec la plate-forme PHP, l’edition 2003 à consacrée sa première journée aux « décideurs ». Cette 3ème édition a permi de découvrir des facettes méconnues de PHP : \r\n\r\n<ul>\n<li>interaction avec SAP, \r</li>\n<li>développement client-serveur avec PHP-GTK, \r</li>\n<li>interopérabilité avec J2EE et .NET, \r</li>\n<li>optimisation du code grâce à UML, \r</li>\n<li>règles de sécurité à respecter, \r</li>\n<li>gestion des erreurs, \r</li>\n</ul>\n...\r\n', 'Forum PHP 2003 - 11/2003', 0, 1266056802, 1, NULL, NULL),
(47, 52, 'Forum PHP 2002', 'forum-php-2002', 'Pour la seconde fois en France un salon sur le langage le plus dynamique du web a eu lieu courant décembre 2002.\r\nLe FORUM PHP 2002 à accueilli les principaux développeurs et spécialistes PHP du monde :\r\n\r\nZeev Suraski (PHPGroup, Zend), Thies C. ARNTZEN (PHPGroup),\r\nDerick RETHANS (PHPGroup), Shane CARAVEO, Macromedia, Armel FAUVEAU (Globalis), Nicolas Hoizey (Clever age), Cyril PIERRE de GEYER (Kaptive Anaska), Frederic BORDAGE, Emmanuel FAIVRE, ...\r\n', 'Forum PHP 2002 - 12/2002', 0, 1266056802, 1, NULL, NULL),
(49, 0, 'Certification PHP', 'certification-php', 'Afin de faire valider officiellement votre niveau de connaissances théoriques et de compétences pratiques de la plateforme PHP, l''AFUP met en place la Certification PHP. \r\n\r\nLes points clefs sont détaillés ci-dessous. \r\n\r\nUn document complet est disponible en format PDF :<a href="https://afup.org/certification_afup.pdf">la Certification AFUP</a>. Vous y trouverez les conditions et le programme des épreuves.\r\n', 'Faites valider vos connaissances théoriques et compétences pratiques de la plateforme PHP !', 0, 1266056802, 1, NULL, NULL),
(52, 0, '05. Forum PHP ', '05-forum-php', 'Le Forum PHP est l’occasion pour les utilisateurs, les prestataires et plus globalement l’ensemble de la communauté PHP française de se réunir autour de deux journées de conférences animées par les meilleurs spécialistes mondiaux et français.\r\n\r\n\r\nLe Forum PHP est la seule occasion de l’année de découvrir cette plate-forme de développement au travers de 3 prismes :\r\n\r\n<ul>\n<li>ses auteurs,\r</li>\n<li>des retours d’entreprises,\r</li>\n<li>des conférences dispensées par les meilleurs experts français.\r</li>\n</ul>\n\r\nDevant la demande croissante des entreprises de trouver des solutions bâties avec la plate-forme PHP, le forum PHP consacre sa première journée aux « décideurs ». ', 'La rencontre annuelle des développeurs PHP francophones.', 0, 1266056802, 1, NULL, NULL),
(58, 0, 'Rendez-vous de l''AFUP', 'rendez-vous-de-l-afup', '<p>L''AFUP organise r&eacute;guli&egrave;rement des conf&eacute;rences sur des th&egrave;mes en rapport avec PHP.</p>', '', 9, 1266015600, 1, 0, '4136780490_441f9ba2dc.jpg'),
(62, 0, 'Livres blancs PHP', 'livres-blancs-php', 'Les livres blancs PHP en entreprise.', 'Les livres blancs PHP en entreprise.', 9, 1266015600, 1, 0, ''),
(63, 53, 'Briques logicielles en PHP', 'briques-logicielles-en-php', 'Cette rubrique a pour objet de mettre en lumière quelques briques logicielles développées en PHP.', 'Briques logicielles en PHP', 0, 1266056802, 1, NULL, NULL),
(64, 52, 'Forum PHP 2004', 'forum-php-2004', '', 'Forum PHP 2004 - 18-19/11/2004', 0, 1266056802, 1, NULL, NULL),
(65, 52, 'Forum PHP 2005', 'forum-php-2005', '', 'Forum PHP 2005 - 9-10/11/2005', 0, 1266056802, 1, NULL, NULL),
(66, 52, 'Forum PHP 2006', 'forum-php-2006', '', 'Le forum PHP 2006 - 9 et 10 novembre', 0, 1266056802, 1, NULL, NULL),
(67, 52, 'Forum PHP 2007', 'forum-php-2007', '', 'Forum PHP 2007 - 21 et 22 novembre 2007', 0, 1266056802, 1, NULL, NULL),
(68, 52, 'Forum PHP 2008', 'forum-php-2008', 'Forum PHP 2008 - 8 et 9 décembre 2008', '', 0, 1266056802, 1, NULL, NULL),
(69, 0, 'PHPTV', 'phptv', 'Les événements et informations relatées par PHPTV', '', 9, 1266015600, 1, 0, ''),
(70, 52, 'Forum PHP 2009', 'forum-php-2009', 'Forum PHP 2009 - 12 et 13 novembre 2009', '', 0, 1266056802, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `afup_tags`
--

CREATE TABLE IF NOT EXISTS `afup_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(255) DEFAULT NULL,
  `id_source` int(11) DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL,
  `id_personne_physique` int(11) DEFAULT NULL,
  `date` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `source` (`source`,`id_source`,`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `annuairepro_Activite`
--

CREATE TABLE IF NOT EXISTS `annuairepro_Activite` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `Nom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `annuairepro_Activite`
--

INSERT INTO `annuairepro_Activite` (`ID`, `Nom`) VALUES
(1, 'Développement au forfait'),
(2, 'Développement en régie'),
(3, 'Conseil / Architecture'),
(4, 'Formation'),
(5, 'Editeur (logiciels PHP et pour PHP)'),
(0, 'Hébergement');

-- --------------------------------------------------------

--
-- Structure de la table `annuairepro_ActiviteMembre`
--

CREATE TABLE IF NOT EXISTS `annuairepro_ActiviteMembre` (
  `Membre` int(11) NOT NULL DEFAULT '0',
  `Activite` int(11) NOT NULL DEFAULT '0',
  `EstPrincipale` enum('True','False') DEFAULT NULL,
  UNIQUE KEY `Membre` (`Membre`,`Activite`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `annuairepro_FormeJuridique`
--

CREATE TABLE IF NOT EXISTS `annuairepro_FormeJuridique` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `Nom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `annuairepro_FormeJuridique`
--

INSERT INTO `annuairepro_FormeJuridique` (`ID`, `Nom`) VALUES
(1, 'Entreprise Individuelle'),
(2, 'Profession libérale'),
(3, 'EURL/SARL'),
(4, 'SA/SAS'),
(5, 'Association');

-- --------------------------------------------------------

--
-- Structure de la table `annuairepro_MembreAnnuaire`
--

CREATE TABLE IF NOT EXISTS `annuairepro_MembreAnnuaire` (
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
  `id_pays` varchar(2) NOT NULL DEFAULT 'FR',
  `NumeroFormateur` varchar(255) DEFAULT NULL,
  `MembreAFUP` tinyint(1) DEFAULT NULL,
  `Valide` tinyint(1) DEFAULT NULL,
  `DateCreation` datetime DEFAULT NULL,
  `TailleSociete` int(11) NOT NULL DEFAULT '0',
  `Password` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `RaisonSociale` (`RaisonSociale`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `annuairepro_MembreAnnuaire_iso`
--

CREATE TABLE IF NOT EXISTS `annuairepro_MembreAnnuaire_iso` (
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
  `id_pays` varchar(2) NOT NULL DEFAULT 'FR',
  `NumeroFormateur` varchar(255) DEFAULT NULL,
  `MembreAFUP` tinyint(1) DEFAULT NULL,
  `Valide` tinyint(1) DEFAULT NULL,
  `DateCreation` datetime DEFAULT NULL,
  `TailleSociete` int(11) NOT NULL DEFAULT '0',
  `Password` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `RaisonSociale` (`RaisonSociale`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `annuairepro_MembreAnnuaire_seq`
--

CREATE TABLE IF NOT EXISTS `annuairepro_MembreAnnuaire_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=773 ;

--
-- Contenu de la table `annuairepro_MembreAnnuaire_seq`
--

INSERT INTO `annuairepro_MembreAnnuaire_seq` (`id`) VALUES
(772);

-- --------------------------------------------------------

--
-- Structure de la table `annuairepro_TailleSociete`
--

CREATE TABLE IF NOT EXISTS `annuairepro_TailleSociete` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `Nom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `annuairepro_TailleSociete`
--

INSERT INTO `annuairepro_TailleSociete` (`ID`, `Nom`) VALUES
(1, 'Une personne'),
(2, 'Entre 2 et 5 personnes'),
(3, 'Entre 6 et 10 personnes'),
(4, 'Plus de 10 personnes');

-- --------------------------------------------------------

--
-- Structure de la table `annuairepro_Zone`
--

CREATE TABLE IF NOT EXISTS `annuairepro_Zone` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `Nom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `annuairepro_Zone`
--

INSERT INTO `annuairepro_Zone` (`ID`, `Nom`) VALUES
(1, '01 - Ile de France'),
(2, '02 - Nord Ouest'),
(3, '03 - Nord Est'),
(4, '04 - Sud Est'),
(5, '05 - Sud Ouest');

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
  `numero_operation` varchar(100) DEFAULT NULL,
  `nom_frs` varchar(50) NOT NULL,
  `montant` double(11,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `idmode_regl` tinyint(5) NOT NULL,
  `date_regl` date NOT NULL,
  `obs_regl` varchar(255) NOT NULL,
  `idevenement` tinyint(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `compta_categorie`
--

CREATE TABLE IF NOT EXISTS `compta_categorie` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `idevenement` int(11) NOT NULL,
  `categorie` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Contenu de la table `compta_categorie`
--

INSERT INTO `compta_categorie` (`id`, `idevenement`, `categorie`) VALUES
(1, 0, 'Facture'),
(2, 0, 'Remboursement'),
(3, 1, 'Inscription'),
(4, 0, 'Cotisation AFUP'),
(5, 12, 'Banque - Compte courant'),
(6, 12, 'Banque - Remise Cheque'),
(7, 12, 'Banque - Retour Impaye'),
(8, 0, 'La Poste'),
(10, 12, 'Banque - Livret A'),
(11, 1, 'Communication'),
(12, 1, 'Divers'),
(13, 1, 'Goodies'),
(14, 1, 'Hotel'),
(15, 1, 'Location'),
(16, 1, 'Nourriture'),
(17, 1, 'Sponsor'),
(18, 1, 'Transport'),
(20, 0, 'Stock'),
(22, 0, 'Administratif'),
(23, 0, 'Banque - Espece'),
(24, 0, 'Banque - Paypal'),
(25, 0, 'Prestation'),
(26, 8, 'A déterminer');

-- --------------------------------------------------------

--
-- Structure de la table `compta_evenement`
--

CREATE TABLE IF NOT EXISTS `compta_evenement` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `evenement` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Contenu de la table `compta_evenement`
--

INSERT INTO `compta_evenement` (`id`, `evenement`) VALUES
(1, 'Forum 2008'),
(2, 'RV AFUP'),
(3, 'Tresorerie'),
(4, 'Forum 2007'),
(5, 'AG'),
(8, 'A déterminer'),
(9, 'Barcamp'),
(10, 'Salon Linux'),
(11, 'Adhesion AFUP'),
(12, 'Compte Courant'),
(13, 'Site Internet'),
(14, 'PHP TV'),
(15, 'Journee Dev'),
(16, 'Stock'),
(17, 'Forum 2009'),
(18, 'Compte Livret A'),
(19, 'Livre blanc'),
(20, 'Compte Espece'),
(21, 'Symfony live 2009'),
(22, 'Forum 2010'),
(23, 'Compte Paypal'),
(24, 'Forum 2011');

-- --------------------------------------------------------

--
-- Structure de la table `compta_operation`
--

CREATE TABLE IF NOT EXISTS `compta_operation` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `operation` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `compta_operation`
--

INSERT INTO `compta_operation` (`id`, `operation`) VALUES
(1, 'Depense'),
(2, 'Recette');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `compta_reglement`
--

CREATE TABLE IF NOT EXISTS `compta_reglement` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `reglement` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `compta_reglement`
--

INSERT INTO `compta_reglement` (`id`, `reglement`) VALUES
(1, 'Espece'),
(2, 'Carte Bleue'),
(3, 'Virement'),
(4, 'Cheque'),
(5, 'Prelevement'),
(6, 'Solde banque'),
(7, 'Provision'),
(8, 'paypal');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
