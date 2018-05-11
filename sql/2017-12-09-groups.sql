CREATE TABLE `afup_mailing_lists` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `members_only` tinyint(1) unsigned NOT NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';
ALTER TABLE `afup_mailing_lists`
ADD `category` varchar(12) NOT NULL;
ALTER TABLE `afup_mailing_lists`
ADD `auto_registration` tinyint(1) NOT NULL;
INSERT INTO afup_mailing_lists (email, name, description, members_only, category, auto_registration) VALUES
("bordeaux@afup.org", "Antenne AFUP Bordeaux","Echange entre les membres de la communauté AFUP de Bordeaux", 0, "office", 0),
("emploi@afup.org", "Emploi AFUP","Un lieu pour chercher un nouvel emploi ou proposer une offre d'emploi", 1, "member", 0),
("entraide@afup.org", "Entraide AFUP","Une question sur un bout de code ou un librairie ? Posez-votre question aux autres membres de l'AFUP ou venez apporter votre aide", 1, "member", 0),
("lille@afup.org", "Antenne AFUP Lille","Echange entre les membres de la communauté AFUP de Lille", 0, "office", 0),
("livreblanc@afup.org", "Livre Blanc AFUP","Echanges sur le projet de livre blanc", 1, "member", 0),
("luxembourg@afup.org", "Antenne AFUP Luxembourg","Echange entre les membres de la communauté AFUP de Luxembourg", 0, "office", 0),
("lyon@afup.org", "Antenne AFUP Lyon","Echange entre les membres de la communauté AFUP de Lyon", 0, "office", 0),
("marseille@afup.org", "Antenne AFUP Marseille","Echange entre les membres de la communauté AFUP de Marseille", 0, "office", 0),
("membres@afup.org", "Membres AFUP","Seul le bureau peut envoyer un mail à l'ensemble des membres regroupés dans cette mailing list", 1, "member", 1),
("montpellier@afup.org", "Antenne AFUP Montpellier","Echange entre les membres de la communauté AFUP de Montpellier", 0, "office", 0),
("nantes@afup.org", "Antenne AFUP Nantes","Echange entre les membres de la communauté AFUP de Nantes", 0, "office", 0),
("orleans@afup.org", "Antenne AFUP Orléans","Echange entre les membres de la communauté AFUP de Orléans", 0, "office", 0),
("paris@afup.org", "Antenne AFUP Paris","Echange entre les membres de la communauté AFUP de Paris", 0, "office", 0),
("php-internals@afup.org", "PHP Internals","Equivalent français d'internals@php.net, réservé aux membres AFUP", 1, "member", 1),
("poitiers@afup.org", "Antenne AFUP Poitiers","Echange entre les membres de la communauté AFUP de Poitiers", 0, "office", 0),
("reims@afup.org", "Antenne AFUP Reims","Echange entre les membres de la communauté AFUP de Reims", 0, "office", 0),
("toulouse@afup.org", "Antenne AFUP Toulouse","Echange entre les membres de la communauté AFUP de Toulouse", 0, "office", 0),
("valence@afup.org", "Antenne AFUP Valence","Echange entre les membres de la communauté AFUP de Valence", 0, "office", 0);
