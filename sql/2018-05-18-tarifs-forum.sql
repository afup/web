SET @ID_EVENT = 19;

DELETE FROM afup_forum_tarif_event WHERE id_event = @ID_EVENT;


SET @ID_EARLY_BIRD = (SELECT id FROM afup_forum_tarif WHERE technical_name = 'EARLY_BIRD');
INSERT INTO `afup_forum_tarif_event` (`id_tarif`, `id_event`, `price`, `date_start`, `date_end`, `description`) VALUES
  (@ID_EARLY_BIRD,	@ID_EVENT,	250,	'2018-05-18 00:00:00',	'2018-07-01 23:59:59',	'Early bird - 2 jours')
;

SET @ID_EARLY_BIRD_AFUP = (SELECT id FROM afup_forum_tarif WHERE technical_name = 'EARLY_BIRD_AFUP');
INSERT INTO `afup_forum_tarif_event` (`id_tarif`, `id_event`, `price`, `date_start`, `date_end`, `description`) VALUES
  (@ID_EARLY_BIRD_AFUP,	@ID_EVENT,	150,	'2018-05-18 00:00:00',	'2018-07-01 23:59:59',	'Early bird - 2 jours AFUP')
;


SET @ID_CFP_SUBMITTER = (SELECT id FROM afup_forum_tarif WHERE technical_name = 'CFP_SUBMITTER');
INSERT INTO `afup_forum_tarif_event` (`id_tarif`, `id_event`, `price`, `date_start`, `date_end`, `description`) VALUES
  (@ID_CFP_SUBMITTER,	@ID_EVENT,	150,	'2018-05-18 00:00:00',	'2018-09-17 23:59:59',	'Personne ayant proposé une conférence')
;

SET @ID_AFUP_FORUM_PREMIERE_JOURNEE = (SELECT id FROM afup_forum_tarif WHERE technical_name = 'AFUP_FORUM_PREMIERE_JOURNEE');
INSERT INTO `afup_forum_tarif_event` (`id_tarif`, `id_event`, `price`, `date_start`, `date_end`, `description`) VALUES
  (@ID_AFUP_FORUM_PREMIERE_JOURNEE,	@ID_EVENT,	175,	'2018-05-18 00:00:00',	'2018-09-17 23:59:59',	'Journée du jeudi 17 mai')
;

SET @ID_AFUP_FORUM_DEUXIEME_JOURNEE = (SELECT id FROM afup_forum_tarif WHERE technical_name = 'AFUP_FORUM_DEUXIEME_JOURNEE');
INSERT INTO `afup_forum_tarif_event` (`id_tarif`, `id_event`, `price`, `date_start`, `date_end`, `description`) VALUES
  (@ID_AFUP_FORUM_DEUXIEME_JOURNEE,	@ID_EVENT,	175,	'2018-05-18 00:00:00',	'2018-09-17 23:59:59',	'Journée du vendredi 18 mai')
;

SET @ID_AFUP_FORUM_2_JOURNEES = (SELECT id FROM afup_forum_tarif WHERE technical_name = 'AFUP_FORUM_2_JOURNEES');
INSERT INTO `afup_forum_tarif_event` (`id_tarif`, `id_event`, `price`, `date_start`, `date_end`, `description`) VALUES
  (@ID_AFUP_FORUM_2_JOURNEES,	@ID_EVENT,	275,	'2018-05-18 00:00:00',	'2018-09-17 23:59:59',	'2 jours')
;

SET @ID_AFUP_FORUM_2_JOURNEES_AFUP = (SELECT id FROM afup_forum_tarif WHERE technical_name = 'AFUP_FORUM_2_JOURNEES_AFUP');
INSERT INTO `afup_forum_tarif_event` (`id_tarif`, `id_event`, `price`, `date_start`, `date_end`, `description`) VALUES
  (@ID_AFUP_FORUM_2_JOURNEES_AFUP,	@ID_EVENT,	175,	'2018-05-18 00:00:00',	'2018-09-17 23:59:59',	'2 jours AFUP')
;



SET @ID_LATE_BIRD = (SELECT id FROM afup_forum_tarif WHERE technical_name = 'LATE_BIRD');
INSERT INTO `afup_forum_tarif_event` (`id_tarif`, `id_event`, `price`, `date_start`, `date_end`, `description`) VALUES
  (@ID_LATE_BIRD,	@ID_EVENT,	325,	'2018-05-18 00:00:00',	'2018-10-18 23:59:59',	'Late bird -  2 jours')
;

SET @ID_LATE_BIRD_AFUP = (SELECT id FROM afup_forum_tarif WHERE technical_name = 'LATE_BIRD_AFUP');
INSERT INTO `afup_forum_tarif_event` (`id_tarif`, `id_event`, `price`, `date_start`, `date_end`, `description`) VALUES
  (@ID_LATE_BIRD_AFUP,	@ID_EVENT,	225,	'2018-05-18 00:00:00',	'2018-10-18 23:59:59',	'Late bird -  2 jours - AFUP')
;

SET @ID_LATE_BIRD_PREMIERE_JOURNEE = (SELECT id FROM afup_forum_tarif WHERE technical_name = 'LATE_BIRD_PREMIERE_JOURNEE');
INSERT INTO `afup_forum_tarif_event` (`id_tarif`, `id_event`, `price`, `date_start`, `date_end`, `description`) VALUES
  (@ID_LATE_BIRD_PREMIERE_JOURNEE,	@ID_EVENT,	225,	'2018-05-18 00:00:00',	'2018-10-18 23:59:59',	'Journée du jeudi 17 mai - Late bird')
;

SET @ID_LATE_BIRD_DEUXIEME_JOURNEE = (SELECT id FROM afup_forum_tarif WHERE technical_name = 'LATE_BIRD_DEUXIEME_JOURNEE');
INSERT INTO `afup_forum_tarif_event` (`id_tarif`, `id_event`, `price`, `date_start`, `date_end`, `description`) VALUES
  (@ID_LATE_BIRD_DEUXIEME_JOURNEE,	@ID_EVENT,	225,	'2018-05-18 00:00:00',	'2018-10-18 23:59:59',	'Journée du vendredi 18 mai - Late bird')
;




