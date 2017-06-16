INSERT INTO afup_forum_tarif_event
(id_tarif, id_event, price, date_start, date_end, description)

  SELECT afup_forum_tarif.id, afup_forum.id, afup_forum_tarif.default_price, from_unixtime(afup_forum.date_fin_appel_conferencier), from_unixtime(afup_forum.date_debut), (afup_forum_tarif.pretty_name)
  FROM afup_forum
    JOIN afup_forum_tarif ON afup_forum_tarif.id < 100
  WHERE afup_forum.id < 17
;
