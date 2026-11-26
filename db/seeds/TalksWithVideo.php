<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

/**
 * Talks d'une édition passée dédiée au bloc « Dernières vidéos » de la home :
 * des talks publiés avec vidéo, dont les créneaux ne sont pas dans l'ordre des identifiants,
 * et des talks qui ne doivent pas y apparaître (clinique, sans vidéo, non planifié,
 * créneau pas encore terminé).
 * Édition à part pour ne pas changer les données des autres éditions.
 */
class TalksWithVideo extends AbstractSeed
{
    public const ID_EVENT = 4;

    #[\Override]
    public function getDependencies(): array
    {
        // Event et Session vident les tables remplies ici.
        return ['Event', 'Session'];
    }

    public function run(): void
    {
        $this->table('afup_forum')
            ->insert([
                'id' => self::ID_EVENT,
                'titre' => 'Rencontres vidéo 2019',
                'path' => 'rencontres-video-2019',
                'nb_places' => 100,
                'date_debut' => '2019-05-23',
                'date_fin' => '2019-05-24',
                'annee' => 2019,
            ])
            ->save();

        $notEndedYet = new \DateTime('+10 days 10:00:00')->format('Y-m-d H:i:s');

        $talks = [
            100 => ['titre' => 'PHP 8 en production', 'fin' => '2019-05-24 10:00:00'],
            101 => ['titre' => 'Les fibers expliquées', 'fin' => '2019-05-24 17:00:00'],
            102 => ['titre' => 'Sécuriser une API', 'fin' => '2019-05-24 11:00:00'],
            103 => ['titre' => 'Domain-Driven Design au quotidien', 'fin' => '2019-05-24 15:00:00'],
            104 => ['titre' => 'Le plus ancien talk en vidéo', 'fin' => '2019-05-23 10:00:00'],
            105 => ['titre' => 'Une clinique filmée', 'fin' => '2019-05-24 18:00:00', 'genre' => 6],
            106 => ['titre' => 'Un talk sans vidéo', 'fin' => '2019-05-24 18:00:00', 'youtube_id' => ''],
            107 => ['titre' => 'Un talk non planifié', 'fin' => '2019-05-24 18:00:00', 'plannifie' => 0],
            108 => ['titre' => 'Un talk au créneau pas terminé', 'fin' => $notEndedYet],
        ];

        $sessions = [];
        $speakers = [];
        $plannings = [];
        foreach ($talks as $sessionId => $talk) {
            $sessions[] = [
                'session_id' => $sessionId,
                'id_forum' => self::ID_EVENT,
                'date_soumission' => '2019-01-15',
                'titre' => $talk['titre'],
                'abstract' => 'Résumé du talk « ' . $talk['titre'] . ' ».',
                'genre' => $talk['genre'] ?? 1,
                'skill' => 0,
                'plannifie' => $talk['plannifie'] ?? 1,
                'youtube_id' => $talk['youtube_id'] ?? 'video' . $sessionId,
                'language_code' => 'fr',
            ];
            $speakers[] = [
                'session_id' => $sessionId,
                'conferencier_id' => Conferenciers::ID_CONFERENCIER,
            ];
            $end = new \DateTime($talk['fin']);
            $plannings[] = [
                'id' => $sessionId,
                'id_session' => $sessionId,
                'debut' => (clone $end)->modify('-40 minutes')->format('U'),
                'fin' => $end->format('U'),
                'id_forum' => self::ID_EVENT,
                'keynote' => 0,
            ];
        }

        $this->table('afup_sessions')->insert($sessions)->save();
        $this->table('afup_conferenciers_sessions')->insert($speakers)->save();
        $this->table('afup_forum_planning')->insert($plannings)->save();
    }
}
