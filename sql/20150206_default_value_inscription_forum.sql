ALTER TABLE `afup_dev`.`afup_inscription_forum`
CHANGE COLUMN `citer_societe` `citer_societe` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
CHANGE COLUMN `newsletter_afup` `newsletter_afup` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
CHANGE COLUMN `newsletter_nexen` `newsletter_nexen` TINYINT(1) UNSIGNED NULL DEFAULT '0'
