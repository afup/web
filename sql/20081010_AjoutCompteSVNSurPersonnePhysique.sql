ALTER TABLE `afup_personnes_physiques` ADD `compte_svn` VARCHAR(100) DEFAULT NULL;

CREATE TABLE `afup_oeuvres` (
  `id` int(11) NOT NULL auto_increment,
  `id_personne_physique` smallint(5) unsigned default NULL,
  `categorie` varchar(255) default NULL,
  `valeur` smallint(5) default NULL,
  `date` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;