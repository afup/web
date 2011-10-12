CREATE TABLE `afup`.`afup_blacklist` (
`id` INT NOT NULL AUTO_INCREMENT ,
`email` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM ;

ALTER TABLE `afup`.`afup_blacklist` ADD UNIQUE `mail_unique` ( `email` ) ;
