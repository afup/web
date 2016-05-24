CREATE TABLE `afup_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` mediumtext,
  `lancement` int(11) DEFAULT '0',
  `cloture` int(11) DEFAULT '0',
  `date` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `afup_votes_poids` (
  `id_vote` int(11) NOT NULL DEFAULT '0',
  `id_personne_physique` int(11) NOT NULL DEFAULT '0',
  `commentaire` mediumtext,
  `poids` tinyint(4) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  UNIQUE KEY `id_vote` (`id_vote`,`id_personne_physique`)
) ENGINE=MyISAM;