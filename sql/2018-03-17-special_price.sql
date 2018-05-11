ALTER TABLE `afup_inscription_forum`
ADD `special_price_token` VARCHAR(255) NULL AFTER `member_type`;

INSERT INTO afup_forum_tarif (technical_name, pretty_name, public, members_only, default_price, active, day)
values ("SPECIAL_PRICE", "Tarif spécial", 0, 0, 0, 1, "one,two");

CREATE TABLE `afup_forum_special_price` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_event` int(10) unsigned NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `price` float DEFAULT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `description` varchar(255) NOT NULL
) COMMENT='' ENGINE='InnoDB';


ALTER TABLE `afup_forum_special_price`
ADD `created_on` datetime NOT NULL,
ADD `creator_id` int(10) unsigned NOT NULL
;
