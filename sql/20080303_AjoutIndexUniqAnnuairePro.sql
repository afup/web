ALTER TABLE `annuairepro_MembreAnnuaire` ADD UNIQUE (
`RaisonSociale`
);
ALTER TABLE `annuairepro_MembreAnnuaire` CHANGE `ID` `ID` INT( 11 ) NOT NULL AUTO_INCREMENT;