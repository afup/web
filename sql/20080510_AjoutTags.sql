CREATE TABLE `afup_tags` (
  `id` int(11) NOT NULL auto_increment,
  `source` varchar(255) default NULL,
  `id_source` int(11) default NULL,
  `tag` varchar(255) default NULL,
  `id_personne_physique` int(11) default NULL,
  `date` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
