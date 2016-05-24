CREATE  TABLE IF NOT EXISTS `afup`.`compta_compte` (
  `id` TINYINT(2) NOT NULL AUTO_INCREMENT ,
  `nom_compte` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;

INSERT INTO `afup`.`compta_compte` (`id` ,`nom_compte`)
VALUES (NULL , 'Compte courant'),
(NULL , 'Caisse'),
(NULL , 'Livret A')
);

ALTER TABLE `afup`.`compta` ADD COLUMN `idcompte` TINYINT(2) NOT NULL DEFAULT 1  AFTER `idevenement` ;

