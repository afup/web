DROP TABLE `afup_forum_partenaires`, `afup_partenaires`;

CREATE  TABLE `afup`.`afup_forum_partenaires` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `id_forum` INT(11) NOT NULL ,
  `id_niveau_partenariat` INT(11) NOT NULL ,
  `ranking` INT(11) NOT NULL ,
  `nom` VARCHAR(100) NOT NULL ,
  `presentation` TEXT NULL DEFAULT NULL ,
  `logo` VARCHAR(100) NULL DEFAULT NULL ,
  `site` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;

INSERT INTO afup_forum_partenaires VALUES (NULL, 6,  5, 1, 'EuraTechnologies', 'Lieu de convergence des acteurs des projets et des innovations, le pôle d\'excellence EuraTechnologies a pour vocation de développer le parc d\'activités à un échelon international, d\'accompagner les entreprises du pôle dans leur développement technologique, commercial et stratégique, de favoriser l\'émergence de projets TIC et de nouveaux talents, et de proposer des outils et un environnement répondant aux besoins des entreprises.', 'logo_euratechnologies.png', 'http://www.euratechnologies.com/'),
(NULL, 6,  5, 2, 'Pôle Nord', 'Pôle Nord est l\'association d\'éditeurs de logiciels libre et open-source du Nord-Pas-de-Calais. Elle a pour objet la promotion et le développement des acteurs du Free/Libre and Open Source Software (FLOSS) de la région Nord-Pas-de-Calais.', 'logo_polenord.png', 'http://www.polenord.info/'),
(NULL, 6,  5, 3, 'Pôle Ubiquitaire', 'LE POLE UBIQUITAIRE est un réseau informel piloté par une gouvernance d\'experts qui s\'appuie sur un outil unique, pour installer la région Nord-Pas-de-Calais comme leader d\'un écosystème économique d\'avenir, l\'ubiquitaire.', 'logo_pole-ubiquitaire.png', 'http://www.pole-ubiquitaire.fr/'),
(NULL, 6,  5, 4, 'FrenchWeb', 'Le magazine des professionnels du net francophone, a pour mission de présenter les initiatives des acteurs français d\'internet. Il regroupe une communauté de plus de 12 000 professionnels, entrepreneurs, experts.
L\'information multimédia en continu, les interviews des experts, les fiches pratiques : rejoignez vite le CLUB Frenchweb pour tout savoir sur l\'internet B2B !', 'logo_frenchweb.png', 'http://frenchweb.fr/'),
(NULL, 6,  5, 5, 'TooLinux', 'TOOLINUX.com est un quotidien d\'information sur Linux et les logiciels Libres. Généraliste, il offre chaque jour une revue de presse en ligne et des articles traitant du mouvement opensource, de l\'économie du libre ainsi que des logiciels Linux ou multi-plateformes. Depuis l\'été 2006, TOOLINUX.com s\'ouvre à la problématique de l\'interopérabilité des solutions informatiques.', 'logo_toolinux.png', 'http://www.toolinux.com'),
(NULL, 6,  5, 6, 'Programmez !', 'Avec plus de 30.000 lecteurs mensuels, PROGRAMMEZ ! s\'est imposé comme un magazine de référence des développeurs.', 'logo_programmez.png', 'http://www.programmez.com/');
