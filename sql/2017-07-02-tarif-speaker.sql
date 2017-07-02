ALTER TABLE `afup_forum_tarif`
  ADD `cfp_submitter_only` tinyint(1) unsigned DEFAULT 0
;


INSERT INTO `afup_forum_tarif` (`technical_name`, `pretty_name`, `public`, `members_only`, `default_price`, `active`, `day`, `cfp_submitter_only`) VALUES
('CFP_SUBMITTER',	'Personne ayant proposé une conférence - 2 jours',	1,	0,	150,	1,	'one,two', 1)
;

SET @ID_CFP_SUBMITTER = (SELECT id FROM afup_forum_tarif WHERE technical_name = 'CFP_SUBMITTER');

INSERT INTO `afup_forum_tarif_event` (`id_tarif`, `id_event`, `price`, `date_start`, `date_end`, `description`) VALUES
  (@ID_CFP_SUBMITTER,	17,	150,	'2017-06-12 19:47:02',	'2017-09-16 23:59:59',	'Personne ayant proposé une conférence')
;
