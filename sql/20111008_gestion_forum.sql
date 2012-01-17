ALTER TABLE `afup_forum` ADD COLUMN `annee` INT(11) NULL DEFAULT NULL  AFTER `date_fin` , ADD COLUMN `date_fin_appel_projet` INT(11) NULL DEFAULT NULL  AFTER `annee` , ADD COLUMN `date_fin_appel_conferencier` INT(11) NULL DEFAULT NULL  AFTER `date_fin_appel_projet` , ADD COLUMN `date_fin_prevente` INT(11) NULL DEFAULT NULL  AFTER `date_fin_appel_conferencier` , ADD COLUMN `date_fin_vente` INT(11) NULL DEFAULT NULL  AFTER `date_fin_prevente` ;

ALTER TABLE `afup_forum` ADD `path` VARCHAR( 100 ) NULL AFTER `titre` ;

CREATE  TABLE IF NOT EXISTS `afup_forum_coupon` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `id_forum` INT(11) NOT NULL ,
  `texte` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;

CREATE  TABLE IF NOT EXISTS `afup_partenaires` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `nom` VARCHAR(100) NOT NULL ,
  `logo` VARCHAR(100) NULL DEFAULT NULL ,
  `presentation` TEXT NOT NULL ,
  `site` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;

CREATE  TABLE IF NOT EXISTS `afup_forum_partenaires` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `id_forum` INT(11) NOT NULL ,
  `id_partenaire` INT(11) NOT NULL ,
  `id_niveau_partenariat` INT(11) NOT NULL ,
  `ranking` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;

CREATE  TABLE IF NOT EXISTS `afup_niveau_partenariat` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `titre` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;

UPDATE `afup_forum` SET `path` = 'phptourlille2011',
`annee` = '2011',
`date_fin_appel_projet` = '1319147999',
`date_fin_appel_conferencier` = '1306879199',
`date_fin_prevente` = '1314741600',
`date_fin_vente` = '1322002800' WHERE `afup_forum`.`id` =6;

INSERT INTO afup_forum_coupon VALUES(null, 6, 'INTERNIM'),
(null, 6, 'ADOBE'),
(null, 6, 'ZEND'),
(null, 6, 'ELAO'),
(null, 6, 'DEVELOPPEZ'),
(null, 6, 'MICROSOFT'),
(null, 6, 'WEKA'),
(null, 6, 'VACONSULTING'),
(null, 6, 'CLEVERAGE'),
(null, 6, 'ENI'),
(null, 6, 'ALTERWAY'),
(null, 6, 'EMERCHANT'),
(null, 6, 'LINAGORA'),
(null, 6, 'OXALIDE'),
(null, 6, 'BUSINESSDECISION'),
(null, 6, 'EYROLLES'),
(null, 6, 'PROGRAMMEZ'),
(null, 6, 'PHPSOLUTIONS'),
(null, 6, 'RBSCHANGE'),
(null, 6, 'JELIX'),
(null, 6, 'CAKEPHPFR'),
(null, 6, 'HOA'),
(null, 6, 'DRUPAL'),
(null, 6, 'MAGIXCMS'),
(null, 6, 'FINEFS'),
(null, 6, 'SOLUTIONSLOGICIELS'),
(null, 6, 'SYMFONY'),
(null, 6, 'DOLIBARR'),
(null, 6, 'PICPHPSQLI'),
(null, 6, 'CRISISCAMP'),
(null, 6, 'RBS'),
(null, 6, 'OBM'),
(null, 6, 'EURATECH'),
(null, 6, 'POLENORD');

INSERT INTO afup_partenaires VALUES(null, 'EuraTechnologies', 'logo_euratechnologies.png', 'Lieu de convergence des acteurs des projets et des innovations, le pôle d\'excellence EuraTechnologies a pour vocation de développer le parc d\'activités à un échelon international, d\'accompagner les entreprises du pôle dans leur développement technologique, commercial et stratégique, de favoriser l\'émergence de projets TIC et de nouveaux talents, et de proposer des outils et un environnement répondant aux besoins des entreprises.', 'http://www.euratechnologies.com/'),
(null, 'Pôle Nord', 'logo_polenord.png', 'Pôle Nord est l\'association d\'éditeurs de logiciels libre et open-source du Nord-Pas-de-Calais. Elle a pour objet la promotion et le développement des acteurs du Free/Libre and Open Source Software (FLOSS) de la région Nord-Pas-de-Calais.', 'http://www.polenord.info/'),
(null, 'Pôle Ubiquitaire', 'logo_pole-ubiquitaire.png', 'LE POLE UBIQUITAIRE est un réseau informel piloté par une gouvernance d\'experts qui s\'appuie sur un outil unique, pour installer la région Nord-Pas-de-Calais comme leader d\'un écosystème économique d\'avenir, l\'ubiquitaire.', 'http://www.pole-ubiquitaire.fr/'),
(null, 'FrenchWeb', 'logo_frenchweb.png', 'Le magazine des professionnels du net francophone, a pour mission de présenter les initiatives des acteurs français d\'internet. Il regroupe une communauté de plus de 12 000 professionnels, entrepreneurs, experts.
L\'information multimédia en continu, les interviews des experts, les fiches pratiques : rejoignez vite le CLUB Frenchweb pour tout savoir sur l\'internet B2B !', 'http://frenchweb.fr/'),
(null, 'TooLinux', 'logo_toolinux.png', 'TOOLINUX.com est un quotidien d\'information sur Linux et les logiciels Libres. Généraliste, il offre chaque jour une revue de presse en ligne et des articles traitant du mouvement opensource, de l\'économie du libre ainsi que des logiciels Linux ou multi-plateformes. Depuis l\'été 2006, TOOLINUX.com s\'ouvre à la problématique de l\'interopérabilité des solutions informatiques.', 'http://www.toolinux.com'),
(null, 'Programmez !', 'logo_programmez.png', 'Avec plus de 30.000 lecteurs mensuels, PROGRAMMEZ ! s\'est imposé comme un magazine de référence des développeurs.', 'http://www.programmez.com/');

INSERT INTO afup_niveau_partenariat VALUES (NULL , 'Platinum'),
(NULL , 'Gold'),
(NULL , 'Silver'),
(NULL , 'Bronze'),
(NULL , 'Partenaires');

INSERT INTO afup_forum_partenaires VALUES (NULL, 6, 1, 5, 1),
(NULL, 6, 2, 5, 2),
(NULL, 6, 3, 5, 3),
(NULL, 6, 4, 5, 4),
(NULL, 6, 5, 5, 5),
(NULL, 6, 6, 5, 6);
