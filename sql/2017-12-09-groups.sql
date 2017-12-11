CREATE TABLE `afup_mailing_lists` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `members_only` tinyint(1) unsigned NOT NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';
ALTER TABLE `afup_mailing_lists`
ADD `category` varchar(12) NOT NULL;
ALTER TABLE `afup_mailing_lists`
ADD `auto_registration` tinyint(1) NOT NULL;
