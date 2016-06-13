CREATE TABLE `afup_aperos` (
  `id` int(11) NOT NULL auto_increment,
  `id_organisateur` int(11) NOT NULL,
  `id_ville` int(11) NOT NULL,
  `date` int(10) NOT NULL,
  `lieu` varchar(70) NOT NULL,
  `etat` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `afup_aperos_inscrits` (
  `id` int(11) NOT NULL auto_increment,
  `pseudo` varchar(20) NOT NULL,
  `mot_de_passe` varchar(32) NOT NULL,
  `nom` varchar(70) NOT NULL,
  `prenom` varchar(70) NOT NULL,
  `email` varchar(255) NOT NULL,
  `site_web` varchar(255) NOT NULL,
  `id_ville` int(11) NOT NULL,
  `date_inscription` int(10) NOT NULL,
  `etat` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`pseudo`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `afup_aperos_participants` (
  `id` bigint(21) NOT NULL auto_increment,
  `id_aperos` int(11) NOT NULL,
  `id_inscrits` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `afup_aperos_villes` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `nom` char(50) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
