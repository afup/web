CREATE TABLE `afup_site_article` (
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

CREATE TABLE `afup_site_feuille` (
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

CREATE TABLE `afup_site_rubrique` (
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