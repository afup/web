CREATE TABLE `afup_forum_sponsors_tickets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `max_invitations` tinyint unsigned NOT NULL,
  `used_invitations` tinyint unsigned NOT NULL DEFAULT '0',
  `id_forum` int NOT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `creator_id` int unsigned NOT NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';
ALTER TABLE `afup_forum_sponsors_tickets`
  ADD UNIQUE `token` (`token`);
CREATE TABLE `afup_throttling` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(64) NOT NULL,
  `object_id` int(10) unsigned DEFAULT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `afup_inscription_forum`
  CHANGE `coupon` `coupon` varchar(255) COLLATE 'latin1_swedish_ci' NULL DEFAULT '' AFTER `reference`,
  COMMENT='Inscriptions au forum PHP';
ALTER TABLE `afup_forum_sponsors_tickets`
  ADD `contact_email` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `token`;
