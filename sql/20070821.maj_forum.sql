DROP TABLE IF EXISTS `afup_inscriptions_forum`;

CREATE TABLE `afup_inscription_forum` (
  `id` int(5) unsigned NOT NULL auto_increment,
  `date` int(11) unsigned NOT NULL default '0',
  `reference` varchar(255)  NOT NULL default '',
  `type_inscription` tinyint(1) unsigned NOT NULL default '0',
  `montant` float NOT NULL default '0',
  `informations_reglement` varchar(255)  default NULL,
  `civilite` varchar(4)  NOT NULL default '',
  `nom` varchar(40)  NOT NULL default '',
  `prenom` varchar(40)  NOT NULL default '',
  `email` varchar(100)  NOT NULL default '',
  `telephone` varchar(40)  default NULL,
  `citer_societe` tinyint(1) unsigned NOT NULL default '0',
  `newsletter_afup` tinyint(1) unsigned NOT NULL default '0',
  `newsletter_nexen` tinyint(1) unsigned NOT NULL default '0',
  `commentaires` text ,
  `etat` tinyint(1) unsigned NOT NULL default '0',
  `id_forum` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_forum` (`id_forum`),
  KEY `reference` (`reference`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Inscriptions au forum PHP';

DROP TABLE IF EXISTS `afup_facturation_forum`;
CREATE TABLE `afup_facturation_forum` (
  `reference` varchar(255)  NOT NULL default '',
  `montant` float NOT NULL default '0',
  `date_reglement` int(11) unsigned default NULL,
  `type_reglement` tinyint(1) unsigned NOT NULL default '0',
  `informations_reglement` varchar(255)  default NULL,
  `email` varchar(100)  NOT NULL default '',
  `societe` varchar(40)  default NULL,
  `nom` varchar(40)  default NULL,
  `prenom` varchar(40)  default NULL,
  `adresse` text  NOT NULL,
  `code_postal` varchar(10)  NOT NULL default '',
  `ville` varchar(50)  NOT NULL default '',
  `id_pays` char(2)  NOT NULL default '',
  `autorisation` varchar(20)  default NULL,
  `transaction` varchar(20)  default NULL,
  `etat` tinyint(1) unsigned NOT NULL default '0',
  `id_forum` smallint(6) NOT NULL,
  `date_facture` int(11) unsigned default NULL,
  PRIMARY KEY  (`reference`),
  KEY `id_pays` (`id_pays`),
  KEY `id_forum` (`id_forum`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Facturation pour le forum PHP';

CREATE TABLE `afup_forum` (
  `id` smallint(6) NOT NULL auto_increment,
  `titre` varchar(50) NOT NULL,
  `nb_places` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `afup_forum` (`id`, `titre`, `nb_places`) VALUES 
(1, 'Forum 2006', 200),
(2, 'Forum 2007', 400);
