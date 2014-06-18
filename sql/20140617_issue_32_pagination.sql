ALTER TABLE `afup_site_rubrique` ADD `pagination` SMALLINT NOT NULL DEFAULT 0;
UPDATE `afup_web`.`afup_site_rubrique` SET `pagination` = '25' WHERE `afup_site_rubrique`.`id` = 9;
