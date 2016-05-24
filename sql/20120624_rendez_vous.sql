ALTER TABLE `afup_rendezvous` ADD `id_antenne` INT( 11 ) NOT NULL ;
ALTER TABLE `afup_rendezvous` ADD `inscription` TINYINT( 1 ) NOT NULL ;
ALTER TABLE `afup_rendezvous` CHANGE `inscription` `inscription` TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `afup_rendezvous` ADD `slides` VARCHAR( 255 ) NOT NULL;