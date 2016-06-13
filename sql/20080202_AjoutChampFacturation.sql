ALTER TABLE `afup_inscription_forum` ADD `facturation` TINYINT( 4 ) NOT NULL AFTER `etat` ;
ALTER TABLE `afup_facturation_forum` ADD `facturation` TINYINT( 4 ) NOT NULL AFTER `etat` ;
UPDATE `afup_inscription_forum` SET `facturation` = 1 WHERE `etat` = 7 ;
UPDATE `afup_facturation_forum` SET `facturation` = 1 WHERE `etat` = 7 ;