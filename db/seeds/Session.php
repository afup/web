<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Session extends AbstractSeed
{
    public const ID_SESSIONS = [
        1,
        2,
    ];

    public function run(): void
    {
        $dateDebut = DateTimeImmutable::createFromFormat('U', (string) strtotime('-5 days 09:00:00'));
        $date = DateTimeImmutable::createFromFormat('U', (string) strtotime('-1 days 20:00:00'));

        $sessions = [
            [
                'session_id' => self::ID_SESSIONS[0],
                'id_forum' => Event::ID_FORUM,
                'date_soumission' => $date->format('Y-m-d'),
                'titre' => 'Jouons tous ensemble à un petit jeu',
                'abstract' => 'Hey ! Tu as participé au quiz du #SuperApéroPHP ? Tu te demandes : mais comment ont-ils fait ? Ne cherche pas plus loin, je te propose de nous retrouver pour un petit jeu et de découvrir ensemble l\'envers du décor ! Au programme : une session interactive avec Laravel / Laravel Echo / Redis et Socket.IO.',
                'staff_notes' => null,
                'journee' => 0,
                'genre' => 1,
                'skill' => 0,
                'plannifie' => 1,
                'needs_mentoring' => 0,
                'youtube_id' => 'MseSkWbhxV8',
                'video_has_fr_subtitles' => 1,
                'video_has_en_subtitles' => 1,
                'slides_url' => 'https://speakerdeck.com/caporaldead/jouons-tous-ensemble-a-un-petit-jeu',
                'blog_post_url' => 'https://mon-blog.com/post/123',
                'interview_url' => 'https://mon-blog.com/interview/456',
                'openfeedback_path' => 'eaJnyMXD3oNfhrrnBYDT/2019-06-27/100',
                'language_code' => 'fr',
                'markdown' => 1,
                'joindin' => 24041,
                'date_publication' => null,
                'has_allowed_to_sharing_with_local_offices' => 1,
                'transcript' => <<<EOF
                1
                00:00:28,440 --> 00:00:29,900
                Merci.
                
                2
                00:00:29,920 --> 00:00:31,660
                Merci.
                
                3
                00:00:31,680 --> 00:00:33,340
                Bonjour, bonjour à toutes et à tous.
                EOF,
            ],
            [
                'session_id' => self::ID_SESSIONS[1],
                'id_forum' => Event::ID_FORUM,
                'date_soumission' => $date->format('Y-m-d'),
                'titre' => 'REST ou GraphQL ? Exemples illustrés avec Symfony et API Platform',
                'abstract' => '<p>GraphQL est une alternative aux architectures REST pour la r&eacute;alisation d&rsquo;API web. Le langage promu par Facebook a des avantages ind&eacute;niab
les : r&eacute;cup&eacute;ration des donn&eacute;es utiles uniquement, limitation du nombre de requ&ecirc;tes, typage fort, syntaxe puissante et bien pens&eacute;e&hellip; Cependant, 
il souffre aussi de d&eacute;fauts souvent sous-estim&eacute;s parmi lesquels l&rsquo;incompatibilit&eacute; avec les m&eacute;canismes de cache, de log, de s&eacute;curit&eacute; ou d&rsquo;auth qui forment la base du stack web d&rsquo;aujourd&rsquo;hui, ou la n&eacute;cessit&eacute; d\'un parser sp&eacute;cifique. De plus, les formats hypermedias modernes s&rsquo;appuyant sur REST disposent de fonctionnalit&eacute;s tr&egrave;s similaires tout en restant compatibles avec les fondements du web. Le framework API Platform, bas&eacute; sur Symfony, permet de cr&eacute;er tr&egrave;s facilement des API REST (JSON-LD, JSON API&hellip;) et GraphQL. Apr&egrave;s avoir &eacute;num&eacute;r&eacute; les avantages et inconv&eacute;nients des diff&eacute;rents formats, nous &eacute;tudierons au travers de diff&eacute;rents cas d&rsquo;usages quand il est pr&eacute;f&eacute;rable d&rsquo;utiliser GraphQL, REST ou les 2 en compl&eacute;ment.</p>',
                'staff_notes' => null,
                'journee' => 0,
                'genre' => 1,
                'skill' => 2,
                'plannifie' => 1,
                'needs_mentoring' => 0,
                'youtube_id' => 'QhAToFl_Omo',
                'video_has_fr_subtitles' => 0,
                'video_has_en_subtitles' => 0,
                'slides_url' => 'https://dunglas.fr/2018/03/symfonylive-paris-slides-rest-vs-graphql-illustrated-examples-with-the-api-platform-framework/',
                'blog_post_url' => '',
                'language_code' => 'fr',
                'markdown' => 0,
                'joindin' => 24138,
                'date_publication' => new \DateTime()->modify('-1 days')->format('Y-m-d H:i:s'),
                'has_allowed_to_sharing_with_local_offices' => 1,
                'bluesky_posts' => 'https://bsky.app/profile/afup.org/post/3mjk3hmqxxe2d',
            ],
            [
                'session_id' => 3,
                'id_forum' => Event::ID_FORUM,
                'date_soumission' => $date->format('Y-m-d'),
                'titre' => 'Révolutionons PHP',
                'abstract' => 'Hey ! Viens changer PHP avec moi !',
                'staff_notes' => null,
                'journee' => 0,
                'genre' => 1,
                'skill' => 0,
                'plannifie' => 1,
                'needs_mentoring' => 0,
                'youtube_id' => 'MseSkWbhxV8',
                'video_has_fr_subtitles' => 0,
                'video_has_en_subtitles' => 0,
                'slides_url' => 'https://speakerdeck.com/caporaldead/jouons-tous-ensemble-a-un-petit-jeu',
                'blog_post_url' => '',
                'language_code' => 'fr',
                'markdown' => 1,
                'joindin' => 24041,
                'date_publication' => new \DateTime()->modify('+5 days')->format('Y-m-d H:i:s'),
                'has_allowed_to_sharing_with_local_offices' => 1,
            ],
            [
                'session_id' => 4,
                'id_forum' => 2,
                'date_soumission' => $date->format('Y-m-d'),
                'titre' => 'Une conf pour voter',
                'abstract' => 'Une conf soumise pour tester le vote du CFP',
                'plannifie' => 0,
                'genre' => 1,
            ],
            [
                'session_id' => 5,
                'id_forum' => 2,
                'date_soumission' => $date->format('Y-m-d'),
                'titre' => 'Une deuxième conf pour voter',
                'abstract' => 'Une deuxième conf soumise pour tester le vote du CFP',
                'plannifie' => 0,
                'genre' => 3,
            ],
            [
                'session_id' => 6,
                'id_forum' => Event::ID_PASSED,
                'date_soumission' => '2020-05-01',
                'titre' => 'Révolutionons PHP',
                'abstract' => 'Hey ! Viens changer PHP avec moi !',
                'staff_notes' => null,
                'journee' => 0,
                'genre' => 1,
                'skill' => 0,
                'plannifie' => 1,
            ],
        ];

        $forcedPlannings = [
            6 => [
                'debut' => new \DateTime('2020-09-15 10:00:00')->format('U'),
                'fin' => new \DateTime('2020-09-15 10:40:00')->format('U'),
            ],
        ];

        // Une édition passée avec un planning complet (7 talks publiés, une vidéo sur
        // deux, des horaires dans un autre ordre que les identifiants) et un talk dont
        // la publication est à venir, pour les liens entre talks de la page talk.
        $passedEventTalks = [
            7 => ['titre' => 'Clôture : le futur de PHP', 'youtube_id' => 'videoTalk07', 'debut' => '16:00'],
            8 => ['titre' => 'Keynote d\'ouverture', 'youtube_id' => null, 'debut' => '09:00'],
            9 => ['titre' => 'Tester sans se ruiner', 'youtube_id' => 'videoTalk09', 'debut' => '11:00'],
            10 => ['titre' => 'Migrer une application legacy', 'youtube_id' => null, 'debut' => '12:00'],
            11 => ['titre' => 'Observabilité avec OpenTelemetry', 'youtube_id' => 'videoTalk11', 'debut' => '14:00'],
            12 => ['titre' => 'Le typage statique en pratique', 'youtube_id' => null, 'debut' => '15:00'],
            13 => ['titre' => 'Un talk pas encore publié', 'youtube_id' => 'videoTalk13', 'debut' => '17:00', 'date_publication' => new \DateTime()->modify('+5 days')->format('Y-m-d H:i:s')],
        ];
        foreach ($passedEventTalks as $sessionId => $passedEventTalk) {
            $sessions[] = [
                'session_id' => $sessionId,
                'id_forum' => Event::ID_PASSED,
                'date_soumission' => '2020-05-01',
                'titre' => $passedEventTalk['titre'],
                'abstract' => 'Résumé du talk « ' . $passedEventTalk['titre'] . ' ».',
                'genre' => 1,
                'skill' => 0,
                'plannifie' => 1,
                'youtube_id' => $passedEventTalk['youtube_id'],
                'language_code' => 'fr',
                'date_publication' => $passedEventTalk['date_publication'] ?? null,
            ];
            $forcedPlannings[$sessionId] = [
                'debut' => new \DateTime('2020-09-15 ' . $passedEventTalk['debut'])->format('U'),
                'fin' => new \DateTime('2020-09-15 ' . $passedEventTalk['debut'])->modify('+40 minutes')->format('U'),
            ];
        }

        $sessionsByIds = [];
        foreach ($sessions as $session) {
            $sessionsByIds[$session['session_id']] = $session;
        }

        $table = $this->table('afup_sessions');
        $table->truncate();

        $table
            ->insert($sessions)
            ->save()
        ;

        $conferenciers = [];
        foreach ($sessions as $session) {
            $conferenciers[] = [
                'session_id' => $session['session_id'],
                'conferencier_id' => Conferenciers::ID_CONFERENCIER,
            ];
        }

        $conferenciers[2]['conferencier_id'] = 2;
        foreach ($conferenciers as $index => $conferencier) {
            if (isset($passedEventTalks[$conferencier['session_id']])) {
                $conferenciers[$index]['conferencier_id'] = 3;
            }
        }

        $table = $this->table('afup_conferenciers_sessions');
        $table->truncate();

        $table
            ->insert($conferenciers)
            ->save()
        ;

        $table = $this->table('afup_forum_salle');
        $table->truncate();

        $table
            ->insert([
                'id' => 1,
                'nom' => 'La salle T',
                'id_forum' => Event::ID_FORUM,
            ])
            ->save();


        $i = 1;
        $plannings = [];
        foreach ($sessions as $session) {
            $sessionId = $session['session_id'];
            $plannings[] = [
                'id' => $i,
                'id_session' => $sessionId,
                'debut' => $forcedPlannings[$sessionId]['debut'] ?? $dateDebut->format('U'),
                'fin' => $forcedPlannings[$sessionId]['fin'] ??  $date->format('U'),
                'id_salle' => 1,
                'id_forum' => $sessionsByIds[$sessionId]['id_forum'],
                'keynote' => 0,
            ];
            ++$i;
        }

        $table = $this->table('afup_forum_planning');
        $table->truncate();

        $table
            ->insert($plannings)
            ->save()
        ;
    }
}
