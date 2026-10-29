<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ParametrageSponsoring extends AbstractMigration
{
    public function up(): void
    {
        // Une seule ligne : réglages de la page /become-sponsor
        $this
            ->table('afup_parametrage_sponsoring', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('mode', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('texte_fr', 'text', ['null' => false])
            ->addColumn('texte_en', 'text', ['null' => false])
            ->create()
        ;

        // Reprise du comportement et des textes en production avant cette migration
        $this
            ->table('afup_parametrage_sponsoring')
            ->insert([
                'id' => 1,
                'mode' => 'generique',
                'texte_fr' => <<<'MD'
                    ## Sponsorisez l’AFUP !
                    Soutenir l’AFUP, c’est soutenir le langage avec lequel vous construisez vos projets, c’est dynamiser l’écosystème PHP dont dépend votre infrastructure technique, d’autant plus à l’ère de l’IA.\
                    Sponsorisez l’AFUP et ses activités, devenez un acteur de premier rang pour l’animation de la communauté PHP en France.\
                    Notre dossier de sponsoring est en phase de préparation : inscrivez-vous pour le recevoir, d’ici la fin de l’année 2026.
                    MD,
                'texte_en' => <<<'MD'
                    ## Sponsor AFUP!
                    Supporting AFUP means backing the language your projects are built on and strengthening the PHP ecosystem your tech stack relies on, which matters more than ever in the age of AI.\
                    By sponsoring AFUP and its activities, you take a front-row role in growing the PHP community in France.\
                    Our sponsorship pack is in the works: sign up and we'll send it to you before the end of 2026.
                    MD,
            ])
            ->saveData()
        ;
    }

    public function down(): void
    {
        $this->table('afup_parametrage_sponsoring')->drop()->save();
    }
}
