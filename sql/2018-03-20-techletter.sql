CREATE TABLE `afup_techletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sending_date` datetime NOT NULL,
  `techletter` text,
  `sent_to_mailchimp` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
