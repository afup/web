ALTER TABLE `afup_rendezvous` ADD `adresse` MEDIUMTEXT NOT NULL AFTER `lieu` ;
ALTER TABLE `afup_rendezvous` ADD `plan` VARCHAR(255) NOT NULL AFTER `lieu` ;
ALTER TABLE `afup_rendezvous` ADD `url` VARCHAR(255) NOT NULL AFTER `lieu` ;
