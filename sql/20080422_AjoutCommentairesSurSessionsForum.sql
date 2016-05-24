CREATE TABLE `afup_forum_sessions_commentaires` (
  `id` int(11) NOT NULL auto_increment,
  `id_session` int(11) default NULL,
  `id_personne_physique` int(11) default NULL,
  `commentaire` mediumtext,
  `date` int(10) default NULL,
  `public` tinyint(4) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
