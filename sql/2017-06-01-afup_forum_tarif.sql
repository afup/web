CREATE TABLE `afup_forum_tarif` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `technical_name` varchar(64) NOT NULL,
  `pretty_name` varchar(255) NOT NULL,
  `public` tinyint(1) unsigned NOT NULL,
  `default_price` float NOT NULL,
  `active` tinyint(1) NOT NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

INSERT INTO afup_forum_tarif (id, technical_name, pretty_name, public, default_price, active)
VALUES
  (99, 'AFUP_FORUM_PREMIERE_JOURNEE', 'Première journée', 1, 150, 1),
  (1, 'AFUP_FORUM_DEUXIEME_JOURNEE', 'Deuxième journée', 1, 150, 1),
  (2, 'AFUP_FORUM_2_JOURNEES', '2 Jours', 1, 250, 1),
  (3, 'AFUP_FORUM_2_JOURNEES_AFUP', '2 Jours AFUP', 1, 150, 1),
  (4, 'AFUP_FORUM_2_JOURNEES_ETUDIANT', '2 Jours étudiant', 0, 150, 1),
  (5, 'AFUP_FORUM_2_JOURNEES_PREVENTE', '2 Jours prévente', 0, 150, 1),
  (6, 'AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE', '2 Jours prévente AFUP', 0, 150, 0),
  (7, 'AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE', '2 Jours étudiant prévente', 0, 150, 0),
  (8, 'AFUP_FORUM_2_JOURNEES_COUPON', '2 jours coupon', 0, 200, 0),
  (9, 'AFUP_FORUM_ORGANISATION', 'Organisation', 0, 0, 1),
  (10, 'AFUP_FORUM_SPONSOR', 'Sponsor', 0, 0, 1),
  (11, 'AFUP_FORUM_PRESSE', 'Presse', 0, 0, 1),
  (12, 'AFUP_FORUM_CONFERENCIER', 'Conférencier', 0, 0, 1),
  (13, 'AFUP_FORUM_INVITATION', 'Invitation', 0, 0, 1),
  (14, 'AFUP_FORUM_PROJET', 'Projet PHP', 0, 0, 1),
  (15, 'AFUP_FORUM_2_JOURNEES_SPONSOR', '2 Jours par sponsor', 0, 200, 1),
  (16, 'AFUP_FORUM_PROF', 'Enseignement supérieur', 0, 0, 1),
  (17, 'AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE', '', 0, 100, 0),
  (18, 'AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE', '', 0, 100, 0),
  (19, 'AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION', '', 0, 150, 0),
  (20, 'AFUP_FORUM_PREMIERE_JOURNEE_AFUP', 'Jour 1 AFUP', 0, 100, 0),
  (21, 'AFUP_FORUM_DEUXIEME_JOURNEE_AFUP', 'Jour 2 AFUP', 0, 100, 0),
  (22, 'AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT', 'Jour 1 Etudiant', 0, 100, 0),
  (23, 'AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT', 'Jour 2 Etudiant', 0, 100, 0)
;

UPDATE afup_forum_tarif SET id=0 WHERE id=99;

CREATE TABLE `afup_forum_tarif_event` (
  `id_tarif` int unsigned NOT NULL,
  `id_event` int unsigned NOT NULL,
  `price` float NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `description` varchar(255) NOT NULL
) COMMENT='' ENGINE='InnoDB';

ALTER TABLE `afup_forum_tarif_event`
  ADD PRIMARY KEY `id_tarif_id_event` (`id_tarif`, `id_event`);
ALTER TABLE `afup_forum_tarif_event`
  ADD INDEX `id_event` (`id_event`);

ALTER TABLE `afup_forum_tarif`
  ADD `day` set('one','two') NOT NULL;
UPDATE `afup_forum_tarif` SET
  `day` = 1
WHERE ((`id` = '0') OR (`id` = '17') OR (`id` = '20') OR (`id` = '22'));

UPDATE `afup_forum_tarif` SET
  `day` = 3
WHERE ((`id` = '2') OR (`id` = '3') OR (`id` = '4') OR (`id` = '5') OR (`id` = '6') OR (`id` = '7') OR (`id` = '8') OR (`id` = '9') OR (`id` = '10') OR (`id` = '11') OR (`id` = '12') OR (`id` = '13') OR (`id` = '14') OR (`id` = '15') OR (`id` = '16') OR (`id` = '19'));

UPDATE `afup_forum_tarif` SET
  `day` = 2
WHERE ((`id` = '1') OR (`id` = '18') OR (`id` = '21') OR (`id` = '23'));
