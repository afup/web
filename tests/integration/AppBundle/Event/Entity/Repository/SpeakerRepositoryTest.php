<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Association\Genre;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Entity\Speaker;
use AppBundle\Event\Speaker\MicrophoneType;
use Doctrine\DBAL\Connection;

final class SpeakerRepositoryTest extends IntegrationTestCase
{
    private Connection $connection;
    private SpeakerRepository $speakerRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = self::getContainer()->get(Connection::class);
        $this->speakerRepository = self::getContainer()->get(SpeakerRepository::class);
    }

    public function testMapsEveryColumn(): void
    {
        $this->connection->insert('afup_conferenciers', [
            'conferencier_id' => 1,
            'id_forum' => 2,
            'genre' => 'femme',
            'prenom' => 'Hélène',
            'nom' => 'Schapira',
            'email' => 'helene@example.com',
            'societe' => 'Acme',
            'ville' => 'Lyon',
            'phone_number' => '0102030405',
            'biographie' => 'Bio',
            'twitter' => 'helene',
            'mastodon' => 'https://phpc.social/@helene',
            'linkedin' => 'helene',
            'bluesky' => 'helene.bsky.social',
            'user_github' => 7,
            'photo' => '1.jpg',
            'will_attend_speakers_diner' => 1,
            'has_special_diet' => 0,
            'special_diet_description' => null,
            'hotel_nights' => 'before,between',
            'referent_person' => 'Marie',
            'referent_person_email' => 'marie@example.com',
            'has_hosting_sponsor' => 1,
            'travel_refund_needed' => 0,
            'travel_refund_sponsored' => 1,
            'mic_type' => 'headset',
        ]);

        $speaker = $this->speakerRepository->find(1);

        self::assertInstanceOf(Speaker::class, $speaker);
        self::assertSame(2, $speaker->eventId);
        self::assertSame(Genre::Femme, $speaker->genre);
        self::assertSame('Hélène SCHAPIRA', $speaker->label);
        self::assertSame('Lyon', $speaker->locality);
        self::assertSame(7, $speaker->githubUserId);
        self::assertNull($speaker->personneId);
        self::assertTrue($speaker->willAttendSpeakersDiner);
        self::assertFalse($speaker->hasSpecialDiet);
        self::assertSame([Speaker::NIGHT_BEFORE, Speaker::NIGHT_BETWEEN], $speaker->getHotelNightsArray());
        self::assertTrue($speaker->hasHostingSponsor);
        self::assertFalse($speaker->travelRefundNeeded);
        self::assertTrue($speaker->travelRefundSponsored);
        self::assertSame(MicrophoneType::Headset, $speaker->micType);

        $speaker->phoneNumber = '0605040302';
        $speaker->setHotelNightsArray([]);
        $speaker->micType = null;
        $this->speakerRepository->save($speaker);

        self::assertSame(
            ['phone_number' => '0605040302', 'hotel_nights' => '', 'mic_type' => null],
            $this->connection->fetchAssociative('SELECT phone_number, hotel_nights, mic_type FROM afup_conferenciers WHERE conferencier_id = 1'),
        );
    }

    public function testToleratesEmptyNames(): void
    {
        $this->connection->insert('afup_conferenciers', ['conferencier_id' => 1, 'id_forum' => 2, 'prenom' => null, 'nom' => 'ALTERWAY']);

        $speaker = $this->speakerRepository->find(1);

        self::assertInstanceOf(Speaker::class, $speaker);
        self::assertNull($speaker->firstname);
        self::assertSame(' ALTERWAY', $speaker->label);
    }

    public function testFindIndexedByTalkIds(): void
    {
        $this->insertSpeaker(1, 2, 'Hélène', 'Schapira');
        $this->insertSpeaker(2, 2, 'Benjamin', 'Moulès');
        $this->insertTalk(10, 2);
        $this->insertTalk(11, 2);
        $this->insertTalk(12, 2);
        $this->link(10, 2);
        $this->link(10, 1);
        $this->link(11, 1);

        $speakersByTalk = $this->speakerRepository->findIndexedByTalkIds([10, 11, 12]);

        self::assertSame([10, 11], array_keys($speakersByTalk));
        self::assertSame([1, 2], self::ids($speakersByTalk[10]));
        self::assertSame([1], self::ids($speakersByTalk[11]));
        self::assertSame([], $this->speakerRepository->findIndexedByTalkIds([]));
        self::assertSame([1, 2], self::ids($this->speakerRepository->getSpeakersByTalk(10)));
        self::assertSame([], $this->speakerRepository->getSpeakersByTalk(12));
    }

    public function testGetScheduledSpeakerTalkIdsFiltersUnpublishedTalks(): void
    {
        $this->insertSpeaker(1, 2, 'Zoé', 'Martin');
        $this->insertSpeaker(2, 2, 'Anne', 'Durand');
        $this->insertSpeaker(3, 2, 'Paul', 'Non planifié');
        $this->insertSpeaker(4, 3, 'Autre', 'Événement');
        $this->insertTalk(10, 2);
        $this->insertTalk(11, 2, publishedAt: '2999-01-01 00:00:00');
        $this->insertTalk(12, 2, scheduled: false);
        $this->insertTalk(13, 3);
        $this->link(10, 1);
        $this->link(11, 1);
        $this->link(11, 2);
        $this->link(12, 3);
        $this->link(13, 4);

        self::assertSame([1 => [10]], $this->speakerRepository->getScheduledSpeakerTalkIds(2));
        // Triés par prénom puis nom
        self::assertSame([2 => [11], 1 => [10, 11]], $this->speakerRepository->getScheduledSpeakerTalkIds(2, true));
        self::assertSame([2, 1], self::ids($this->speakerRepository->getScheduledSpeakers(2, true)));
    }

    public function testSearchSpeakers(): void
    {
        $this->insertSpeaker(1, 2, 'Hélène', 'Schapira', 'Zeta');
        $this->insertSpeaker(2, 2, 'Benjamin', 'Moulès', null);
        $this->insertSpeaker(3, 2, 'Paul', 'Durand', 'Acme');
        $this->insertSpeaker(4, 3, 'Autre', 'Événement', 'Acme');

        self::assertSame([3, 2, 1], self::ids($this->speakerRepository->searchSpeakers(2)));
        self::assertSame([1, 2, 3], self::ids($this->speakerRepository->searchSpeakers(2, 'name', 'desc')));
        self::assertSame([2, 3, 1], self::ids($this->speakerRepository->searchSpeakers(2, 'company', 'asc')));
        self::assertSame([3], self::ids($this->speakerRepository->searchSpeakers(2, filter: 'Acme')));
        // Une société vide n'empêche pas de trouver le conférencier par son nom
        self::assertSame([2], self::ids($this->speakerRepository->searchSpeakers(2, filter: 'Moul')));
    }

    public function testCountByEventCountsDistinctNames(): void
    {
        $this->insertSpeaker(1, 2, 'Hélène', 'Schapira');
        $this->insertSpeaker(2, 2, 'Hélène', 'Schapira');
        $this->insertSpeaker(3, 2, 'Paul', 'Durand');
        $this->insertSpeaker(4, 3, 'Autre', 'Événement');

        self::assertSame(2, $this->speakerRepository->countByEvent(2));
        self::assertSame(0, $this->speakerRepository->countByEvent(4));
    }

    public function testHasCFPSubmitted(): void
    {
        $this->insertSpeaker(1, 2, 'Hélène', 'Schapira', email: 'helene@example.com');
        $this->insertSpeaker(2, 2, 'Paul', 'Durand', email: 'paul@example.com');
        $this->insertTalk(10, 2, scheduled: false);
        $this->link(10, 1);

        self::assertTrue($this->speakerRepository->hasCFPSubmitted(2, 'helene@example.com'));
        self::assertFalse($this->speakerRepository->hasCFPSubmitted(3, 'helene@example.com'));
        self::assertFalse($this->speakerRepository->hasCFPSubmitted(2, 'paul@example.com'));
    }

    public function testGetFromLastEventAndUserId(): void
    {
        $this->connection->insert('afup_forum', ['id' => 1, 'titre' => 'Forum 2020', 'date_debut' => '2020-11-01']);
        $this->connection->insert('afup_forum', ['id' => 2, 'titre' => 'Forum 2022', 'date_debut' => '2022-11-01']);
        $this->connection->insert('afup_forum', ['id' => 3, 'titre' => 'Forum 2024', 'date_debut' => '2024-11-01']);
        $this->insertSpeaker(1, 1, 'Hélène', 'Ancienne', githubUserId: 7);
        $this->insertSpeaker(2, 2, 'Hélène', 'Récente', githubUserId: 7);
        $this->insertSpeaker(3, 3, 'Hélène', 'Courante', githubUserId: 7);

        self::assertSame(2, $this->speakerRepository->getFromLastEventAndUserId(3, 7)?->id);
        self::assertSame(3, $this->speakerRepository->getFromLastEventAndUserId(2, 7)?->id);
        self::assertNull($this->speakerRepository->getFromLastEventAndUserId(3, 8));
    }

    /**
     * @param iterable<Speaker> $speakers
     *
     * @return list<int>
     */
    private static function ids(iterable $speakers): array
    {
        $ids = [];
        foreach ($speakers as $speaker) {
            $ids[] = $speaker->id;
        }

        return $ids;
    }

    private function insertSpeaker(int $id, int $eventId, string $firstname, string $lastname, ?string $company = null, ?string $email = null, ?int $githubUserId = null): void
    {
        $this->connection->insert('afup_conferenciers', [
            'conferencier_id' => $id,
            'id_forum' => $eventId,
            'prenom' => $firstname,
            'nom' => $lastname,
            'societe' => $company,
            'email' => $email ?? 'speaker' . $id . '@example.com',
            'user_github' => $githubUserId,
        ]);
    }

    private function insertTalk(int $id, int $eventId, bool $scheduled = true, ?string $publishedAt = null): void
    {
        $this->connection->insert('afup_sessions', [
            'session_id' => $id,
            'id_forum' => $eventId,
            'date_soumission' => '2024-01-01',
            'titre' => 'Talk ' . $id,
            'abstract' => 'Abstract de test',
            'skill' => 0,
            'plannifie' => $scheduled ? 1 : 0,
            'date_publication' => $publishedAt,
        ]);
    }

    private function link(int $talkId, int $speakerId): void
    {
        $this->connection->insert('afup_conferenciers_sessions', [
            'session_id' => $talkId,
            'conferencier_id' => $speakerId,
        ]);
    }
}
