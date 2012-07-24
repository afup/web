ALTER TABLE `afup_rendezvous` DROP `slides`;


CREATE TABLE IF NOT EXISTS `afup_rendezvous_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_rendezvous` int(11) NOT NULL,
  `fichier` int(255) NOT NULL,
  `url` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `afup_rendezvous_slides` CHANGE `fichier` `fichier` INT( 255 ) NULL ,
CHANGE `url` `url` VARCHAR( 250 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ;

ALTER TABLE `afup_rendezvous` ADD `url_externe` VARCHAR( 255 ) NOT NULL ;