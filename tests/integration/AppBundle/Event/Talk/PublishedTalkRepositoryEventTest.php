<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Talk;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Talk\EventTalk;
use AppBundle\Event\Talk\EventTalksSummary;
use AppBundle\Event\Talk\PublishedSpeaker;
use AppBundle\Event\Talk\PublishedTalkRepository;
use Doctrine\DBAL\Connection;

/**
 * Les talks publiés vus depuis les éditions ; les speakers sont dans PublishedTalkRepositoryTest.
 */
final class PublishedTalkRepositoryEventTest extends IntegrationTestCase
{
    private Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = self::getContainer()->get(Connection::class);

        $this->connection->insert('afup_forum', ['id' => 1, 'titre' => 'Forum PHP 2019', 'path' => 'forumphp2019', 'date_debut' => '2019-10-24', 'date_fin' => '2019-10-25', 'nb_places' => 100]);
        $this->connection->insert('afup_forum', ['id' => 2, 'titre' => 'AFUP Day 2021', 'path' => 'afupday2021', 'date_debut' => '2021-05-28', 'date_fin' => '2021-05-28', 'nb_places' => 100]);
        $this->connection->insert('afup_forum', ['id' => 3, 'titre' => 'Forum PHP 2018', 'path' => 'forumphp2018', 'date_debut' => '2018-10-25', 'date_fin' => '2018-10-26', 'nb_places' => 100]);
        $this->connection->insert('afup_forum', ['id' => 4, 'titre' => 'Sans talk publié', 'path' => 'sanstalk', 'date_debut' => '2020-10-25', 'date_fin' => '2020-10-26', 'nb_places' => 100]);

        foreach ([10 => 'Alice', 11 => 'Bob', 12 => 'Chloé'] as $speakerId => $firstname) {
            $this->connection->insert('afup_conferenciers', ['conferencier_id' => $speakerId, 'id_forum' => 1, 'prenom' => $firstname, 'nom' => 'Martin', 'email' => $speakerId . '@example.com']);
        }

        // Les créneaux sont dans un autre ordre que les identifiants ; deux talks partagent un créneau dans deux salles.
        $this->insertTalk(100, 1, 'Clôture', strtotime('2019-10-25 17:00:00'), room: 1, speakerIds: [10], youtubeId: 'video100');
        $this->insertTalk(101, 1, 'Keynote', strtotime('2019-10-24 09:00:00'), room: 1, speakerIds: [12, 11]);
        $this->insertTalk(102, 1, 'Salle 2', strtotime('2019-10-24 10:00:00'), room: 2, speakerIds: [10], youtubeId: 'video102');
        $this->insertTalk(103, 1, 'Salle 1', strtotime('2019-10-24 10:00:00'), room: 1, speakerIds: []);
        $this->insertTalk(104, 1, 'Clinique', strtotime('2019-10-24 11:00:00'), type: Talk::TYPE_CLINIC, youtubeId: 'video104');
        $this->insertTalk(105, 1, 'Présentation des speakers', strtotime('2019-10-24 11:00:00'), type: Talk::TYPE_SPEAKER_INTRODUCTIONS);
        $this->insertTalk(106, 1, 'Non planifié', strtotime('2019-10-24 11:00:00'), scheduled: false);
        $this->insertTalk(107, 1, 'Publication à venir', strtotime('2019-10-24 11:00:00'), publishedOn: '+1 day', youtubeId: 'video107');
        $this->insertTalk(108, 1, 'Sans créneau', null);
        $this->insertTalk(109, 1, 'Créneau pas encore terminé', time() - 600, slotEnd: time() + 600);
        $this->insertTalk(200, 2, 'AFUP Day', strtotime('2021-05-28 10:00:00'), youtubeId: '');
        $this->insertTalk(300, 3, 'Forum 2018', strtotime('2018-10-25 10:00:00'), youtubeId: 'video300', publishedOn: '-1 day');
        $this->insertTalk(400, 4, 'Non planifié', strtotime('2020-10-25 10:00:00'), scheduled: false);
    }

    public function testEventTalksFollowThePlanning(): void
    {
        $eventTalks = new PublishedTalkRepository($this->connection)->getEventTalks(1);

        self::assertSame(
            [
                [101, 'Keynote', '2019-10-24 09:00', [11, 12], null],
                [103, 'Salle 1', '2019-10-24 10:00', [], null],
                [102, 'Salle 2', '2019-10-24 10:00', [10], 'video102'],
                [100, 'Clôture', '2019-10-25 17:00', [10], 'video100'],
            ],
            array_map(static fn(EventTalk $eventTalk): array => [
                $eventTalk->talk->id,
                $eventTalk->talk->title,
                $eventTalk->startsAt->format('Y-m-d H:i'),
                array_map(static fn(PublishedSpeaker $speaker): int => $speaker->id, $eventTalk->speakers),
                $eventTalk->talk->youtubeId,
            ], $eventTalks),
        );
        self::assertSame([], new PublishedTalkRepository($this->connection)->getEventTalks(4));
    }

    public function testEventSummariesCountPublishedTalksAndVideos(): void
    {
        self::assertSame(
            [
                ['afupday2021', 1, 0, 2021],
                ['forumphp2019', 4, 2, 2019],
                ['forumphp2018', 1, 1, 2018],
            ],
            array_map(static fn(EventTalksSummary $summary): array => [
                $summary->event->path,
                $summary->talkCount,
                $summary->videoCount,
                $summary->getYear(),
            ], new PublishedTalkRepository($this->connection)->getEventSummaries()),
        );
    }

    public function testGetLatestPublishedTalksWithVideoSortsBySlotEnd(): void
    {
        // Le créneau du talk 301 se termine après celui du talk 300, qui a un identifiant plus petit.
        $this->insertTalk(301, 3, 'Forum 2018, fin de journée', strtotime('2018-10-25 17:00:00'), speakerIds: [12, 11], youtubeId: 'video301', slidesUrl: 'https://slides.example/301', blogPostUrl: 'https://blog.example/301');
        $this->insertTalk(110, 1, 'Vidéo, créneau en cours', time() - 600, youtubeId: 'video110', slotEnd: time() + 600);
        $this->insertTalk(111, 1, 'Vidéo, non planifié', strtotime('2019-10-24 11:00:00'), youtubeId: 'video111', scheduled: false);
        $repository = self::getContainer()->get(TalkRepository::class);
        $toRows = static fn(array $latest): array => array_map(static fn(array $row): array => [
            $row['talk']->getId(),
            $row['talk']->getTitle(),
            $row['talk']->getYoutubeId(),
            $row['event']->getPath(),
            array_map(static fn(Speaker $speaker): ?int => $speaker->getId(), $row['speakers']),
            $row['talk']->getSlidesUrl(),
            $row['talk']->getBlogPostUrl(),
        ], $latest);

        // Exclus : sans vidéo ou vidéo vide (101, 103, 200), clinique (104), publication à venir (107), créneau en
        // cours (110), non planifié (111).
        self::assertSame(
            [
                [100, 'Clôture', 'video100', 'forumphp2019', [10], null, null],
                [102, 'Salle 2', 'video102', 'forumphp2019', [10], null, null],
                // Deux speakers : le talk n'apparaît qu'une fois, la limite porte sur les talks.
                [301, 'Forum 2018, fin de journée', 'video301', 'forumphp2018', [11, 12], 'https://slides.example/301', 'https://blog.example/301'],
                [300, 'Forum 2018', 'video300', 'forumphp2018', [], null, null],
            ],
            $toRows($repository->getLatestPublishedTalksWithVideo(12)),
        );
        self::assertSame([100, 102], array_column($toRows($repository->getLatestPublishedTalksWithVideo(2)), 0));
        self::assertSame([100, 102, 301], array_column($toRows($repository->getLatestPublishedTalksWithVideo(3)), 0));
    }

    private function getEvent(string $path): Event
    {
        $event = self::getContainer()->get(EventRepository::class)->getByPath($path);
        self::assertNotNull($event);

        return $event;
    }

    /**
     * @param list<int> $speakerIds
     */
    private function insertTalk(int $id, int $eventId, string $title, ?int $slotStart, int $type = Talk::TYPE_FULL_LONG, int $room = 1, array $speakerIds = [], ?string $youtubeId = null, bool $scheduled = true, ?string $publishedOn = null, ?int $slotEnd = null, ?string $slidesUrl = null, ?string $blogPostUrl = null): void
    {
        $this->connection->insert('afup_sessions', [
            'session_id' => $id,
            'id_forum' => $eventId,
            'date_soumission' => '2019-01-01',
            'titre' => $title,
            'abstract' => '',
            'genre' => $type,
            'plannifie' => (int) $scheduled,
            'youtube_id' => $youtubeId,
            'slides_url' => $slidesUrl,
            'blog_post_url' => $blogPostUrl,
            'date_publication' => null !== $publishedOn ? date('Y-m-d H:i:s', (int) strtotime($publishedOn)) : null,
        ]);

        foreach ($speakerIds as $speakerId) {
            $this->connection->insert('afup_conferenciers_sessions', ['session_id' => $id, 'conferencier_id' => $speakerId]);
        }

        if (null !== $slotStart) {
            $this->connection->insert('afup_forum_planning', ['id_session' => $id, 'debut' => $slotStart, 'fin' => $slotEnd ?? $slotStart + 2400, 'id_forum' => $eventId, 'id_salle' => $room]);
        }
    }
}
