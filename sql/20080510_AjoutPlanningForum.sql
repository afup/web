ALTER TABLE `afup_sessions` ADD `plannifie` tinyint(1) DEFAULT NULL ;

CREATE TABLE `afup_forum_planning` (
  `id` int(11) NOT NULL auto_increment,
  `id_session` int(11) default NULL,
  `debut` int(10) default NULL,
  `fin` int(10) default NULL,
  `id_salle` smallint(4) default NULL,
  `id_forum` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `afup_forum_salle` (
  `id` smallint(4) NOT NULL auto_increment,
  `nom` varchar(255) default NULL,
  `id_forum` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;