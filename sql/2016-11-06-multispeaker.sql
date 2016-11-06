CREATE TABLE `afup_sessions_invitation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `talk_id` int(11) NOT NULL,
  `state` tinyint(3) unsigned NOT NULL,
  `submitted_on` datetime NOT NULL,
  `submitted_by` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `talk_id_email` (`talk_id`,`email`)
) ENGINE=InnoDB;