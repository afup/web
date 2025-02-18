<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class UpdateTablesEngineToInnoDb extends AbstractMigration
{
    public function change(): void
    {
        $this->execute("ALTER TABLE `afup_conferenciers_sessions` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_conferenciers` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_contacts` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_cotisations` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_email` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_forum_planning` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_forum_salle` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_forum_sessions_commentaires` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_inscriptions_rappels` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_logs` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_oeuvres` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_pays` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_personnes_morales` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_personnes_physiques` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_planete_billet` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_planete_flux` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_presences_assemblee_generale` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_sessions_note` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_sessions_vote` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_sessions` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_site_article` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_site_feuille` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_site_rubrique` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_tags` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_votes_poids` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `afup_votes` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `annuairepro_ActiviteMembre` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `annuairepro_Activite` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `annuairepro_FormeJuridique` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `annuairepro_MembreAnnuaire_iso` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `annuairepro_MembreAnnuaire_seq` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `annuairepro_MembreAnnuaire` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `annuairepro_TailleSociete` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `annuairepro_Zone` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `compta_categorie` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `compta_evenement` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `compta_operation` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `compta_periode` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `compta_reglement` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `compta_simulation` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `compta` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `rdv_afup` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `wikini_acls` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `wikini_links` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `wikini_pages` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `wikini_referrers` ENGINE=InnoDB;");
        $this->execute("ALTER TABLE `wikini_users` ENGINE=InnoDB;");
    }
}
