CREATE TABLE `afup_sessions_note` (
  `session_id` int(11) NOT NULL,
  `note` tinyint(4) NOT NULL,
  `salt` char(32) NOT NULL,
  `date_soumission` date NOT NULL,
  PRIMARY KEY  USING BTREE (`note`,`session_id`,`salt`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `afup_sessions_vote` (
  `id_personne_physique` int(11) NOT NULL,
  `id_session` int(11) NOT NULL,
  `a_vote` tinyint(1) default '0',
  PRIMARY KEY  (`id_session`,`id_personne_physique`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;