<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class UpdateTablesEncoding extends AbstractMigration
{
    public function change(): void
    {
        $this->execute("ALTER TABLE afup_antenne CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_assemblee_generale CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_assemblee_generale_question CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_badge CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_compta_facture CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_compta_facture_details CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_conferenciers CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_conferenciers_sessions CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_contacts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_cotisations CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_email CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_facturation_forum CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_forum CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_forum_coupon CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_forum_partenaires CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_forum_planning CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_forum_salle CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_forum_sessions_commentaires CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_forum_special_price CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_forum_sponsor_scan CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_forum_sponsors_tickets CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_forum_tarif CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_forum_tarif_event CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_inscription_forum CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_inscriptions_rappels CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_logs CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_meetup CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_niveau_partenariat CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_oeuvres CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_pays CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_personnes_morales CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_personnes_morales_invitations CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_personnes_physiques CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_personnes_physiques_badge CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_planete_billet CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_planete_flux CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_presences_assemblee_generale CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_sessions CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_sessions_invitation CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_sessions_note CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_sessions_vote CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_sessions_vote_github CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_site_article CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_site_feuille CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_site_rubrique CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_speaker_suggestion CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_subscription_reminder_log CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_tags CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_techletter CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_techletter_subscriptions CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_techletter_unsubscriptions CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_throttling CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_user_github CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_vote_assemblee_generale CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_votes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_votes_poids CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE annuairepro_Activite CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE annuairepro_ActiviteMembre CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE annuairepro_FormeJuridique CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE annuairepro_MembreAnnuaire CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE annuairepro_MembreAnnuaire_iso CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE annuairepro_TailleSociete CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE annuairepro_Zone CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE compta CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE compta_categorie CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE compta_compte CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE compta_evenement CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE compta_operation CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE compta_periode CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE compta_regle CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE compta_reglement CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE compta_simulation CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE rdv_afup CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE scan CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE sessions CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE tweet CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE wikini_acls CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE wikini_links CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE wikini_pages CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE wikini_referrers CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE wikini_users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
    }
}
