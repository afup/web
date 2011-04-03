ALTER TABLE annuairepro_MembreAnnuaire ADD id_pays VARCHAR( 2 ) NOT NULL DEFAULT 'FR' AFTER Zone;
ALTER TABLE annuairepro_MembreAnnuaire_iso ADD id_pays VARCHAR( 2 ) NOT NULL DEFAULT 'FR' AFTER Zone;