ALTER TABLE `afup_rendezvous` DROP `slides`;


CREATE TABLE IF NOT EXISTS `afup_rendezvous_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_rendezvous` int(11) NOT NULL,
  `fichier` int(255) NOT NULL,
  `url` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


