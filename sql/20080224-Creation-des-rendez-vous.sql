# CocoaMySQL dump
# Version 0.5
# http://cocoamysql.sourceforge.net
#
# Host: localhost (MySQL 4.1.16-standard)
# Database: afup
# Generation Time: 2008-02-24 22:08:10 +0100
# ************************************************************

# Dump of table afup_rendezvous
# ------------------------------------------------------------

CREATE TABLE `afup_rendezvous` (
  `id` int(11) NOT NULL auto_increment,
  `titre` varchar(255) default NULL,
  `accroche` mediumtext,
  `theme` mediumtext,
  `debut` int(11) default NULL,
  `fin` int(11) default NULL,
  `lieu` varchar(255) default NULL,
  `capacite` mediumint(9) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table afup_rendezvous_inscrits
# ------------------------------------------------------------

CREATE TABLE `afup_rendezvous_inscrits` (
  `id` int(11) NOT NULL auto_increment,
  `id_rendezvous` int(11) default NULL,
  `nom` varchar(255) default NULL,
  `entreprise` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `telephone` varchar(255) default NULL,
  `presence` tinyint(4) default NULL,
  `confirme` tinyint(4) default 0,
  `creation` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



