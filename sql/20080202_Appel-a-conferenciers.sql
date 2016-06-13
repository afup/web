CREATE TABLE `afup_conferenciers` (
  `conferencier_id` INT NOT NULL AUTO_INCREMENT,
  `id_forum` SMALLINT NOT NULL,
  `civilite` VARCHAR(5) NOT NULL,
  `nom` VARCHAR(70) NOT NULL,
  `prenom` VARCHAR(50) NOT NULL,
  `email` VARCHAR(65) NOT NULL,
  `societe` VARCHAR(120),
  `biographie` TEXT NOT NULL,
  PRIMARY KEY (`conferencier_id`),
  key(`id_forum`)
);

CREATE TABLE `afup_conferenciers_sessions` (
  `session_id`  INT NOT NULL AUTO_INCREMENT,
  `conferencier_id` INT NOT NULL,
  PRIMARY KEY (`session_id`, `conferencier_id`)
);

CREATE TABLE `afup_sessions` (
  `session_id` int(11) NOT NULL auto_increment,
  `id_forum` smallint(6) NOT NULL default '0',
  `date_soumission` date NOT NULL default '0000-00-00',
  `titre` varchar(255) NOT NULL default '',
  `abstract` text NOT NULL,
  `journee` tinyint(1) NOT NULL default '0',
  `genre` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`session_id`)
);
