# CocoaMySQL dump
# Version 0.5
# http://cocoamysql.sourceforge.net
#
# Host: localhost (MySQL 4.1.16-standard)
# Database: afup
# Generation Time: 2007-12-02 19:07:49 +0100
# ************************************************************

# Dump of table afup_planete_billet
# ------------------------------------------------------------

DROP TABLE IF EXISTS `afup_planete_billet`;

CREATE TABLE `afup_planete_billet` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table afup_planete_flux
# ------------------------------------------------------------

DROP TABLE IF EXISTS `afup_planete_flux`;

CREATE TABLE `afup_planete_flux` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `feed` varchar(255) default NULL,
  `etat` tinyint(4) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



