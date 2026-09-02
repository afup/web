<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\Repository\TalkToSpeakerRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use Doctrine\DBAL\Connection;

final class TalkToSpeakerRepositoryTest extends IntegrationTestCase
{
    public function testGetNumberOfSpeakersCountsDistinctSpeakersByEvent(): void
    {
        $repository = self::getContainer()->get(TalkToSpeakerRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertSession($connection, 1, 10, '2026-01-01');
        $this->insertSession($connection, 2, 10, '2026-01-10');
        $this->insertSession($connection, 3, 20, '2026-01-01');

        $this->insertAssociation($connection, 1, 1);
        $this->insertAssociation($connection, 1, 2);
        // Le même speaker sur une autre session du même event : ne compte qu'une fois (DISTINCT)
        $this->insertAssociation($connection, 2, 1);
        $this->insertAssociation($connection, 3, 3);

        self::assertSame(2, $repository->getNumberOfSpeakers(10));
        self::assertSame(1, $repository->getNumberOfSpeakers(20));
        self::assertSame(0, $repository->getNumberOfSpeakers(99));
    }

    public function testGetNumberOfSpeakersAcceptsAnEventObject(): void
    {
        $repository = self::getContainer()->get(TalkToSpeakerRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertSession($connection, 1, 10, '2026-01-01');
        $this->insertAssociation($connection, 1, 1);

        self::assertSame(1, $repository->getNumberOfSpeakers($this->buildEvent(10)));
    }

    public function testGetNumberOfSpeakersFiltersBySubmissionDateWhenSinceIsProvided(): void
    {
        $repository = self::getContainer()->get(TalkToSpeakerRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertSession($connection, 1, 10, '2026-01-01');
        $this->insertSession($connection, 2, 10, '2026-01-10');

        $this->insertAssociation($connection, 1, 1);
        $this->insertAssociation($connection, 2, 2);

        self::assertSame(1, $repository->getNumberOfSpeakers(10, new \DateTime('2026-01-05')));
        self::assertSame(2, $repository->getNumberOfSpeakers(10, new \DateTime('2025-12-01')));
    }

    public function testReplaceSpeakersReplacesExistingAssociations(): void
    {
        $repository = self::getContainer()->get(TalkToSpeakerRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $talk = $this->buildTalk(1);
        $repository->replaceSpeakers($talk, [$this->buildSpeaker(1), $this->buildSpeaker(2)]);

        self::assertSame([1, 2], $this->fetchSpeakerIds($connection, 1));

        $repository->replaceSpeakers($talk, [$this->buildSpeaker(3), $this->buildSpeaker(4)]);

        self::assertSame([3, 4], $this->fetchSpeakerIds($connection, 1));
    }

    public function testReplaceSpeakersWithNoSpeakerEmptiesAssociations(): void
    {
        $repository = self::getContainer()->get(TalkToSpeakerRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertAssociation($connection, 1, 1);
        $this->insertAssociation($connection, 1, 2);

        $repository->replaceSpeakers($this->buildTalk(1), []);

        self::assertSame([], $this->fetchSpeakerIds($connection, 1));
    }

    public function testReplaceSpeakersKeepsAnAlreadyAssociatedSpeaker(): void
    {
        $repository = self::getContainer()->get(TalkToSpeakerRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertAssociation($connection, 1, 1);

        $repository->replaceSpeakers($this->buildTalk(1), [$this->buildSpeaker(1)]);

        self::assertSame([1], $this->fetchSpeakerIds($connection, 1));
    }

    public function testReplaceSpeakersWithPartialOverlap(): void
    {
        $repository = self::getContainer()->get(TalkToSpeakerRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertAssociation($connection, 1, 1);
        $this->insertAssociation($connection, 1, 2);

        $repository->replaceSpeakers($this->buildTalk(1), [$this->buildSpeaker(2), $this->buildSpeaker(3)]);

        self::assertSame([2, 3], $this->fetchSpeakerIds($connection, 1));
    }

    public function testAddSpeakerToTalkInsertsTheAssociation(): void
    {
        $repository = self::getContainer()->get(TalkToSpeakerRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $repository->addSpeakerToTalk($this->buildTalk(1), $this->buildSpeaker(1));

        self::assertSame([1], $this->fetchSpeakerIds($connection, 1));
    }

    public function testAddSpeakerToTalkIsIdempotent(): void
    {
        $repository = self::getContainer()->get(TalkToSpeakerRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $repository->addSpeakerToTalk($this->buildTalk(1), $this->buildSpeaker(1));
        $repository->addSpeakerToTalk($this->buildTalk(1), $this->buildSpeaker(1));

        self::assertSame([1], $this->fetchSpeakerIds($connection, 1));
    }

    private function insertSession(Connection $connection, int $sessionId, int $eventId, string $submissionDate): void
    {
        $connection->insert('afup_sessions', [
            'session_id' => $sessionId,
            'id_forum' => $eventId,
            'date_soumission' => $submissionDate,
            'abstract' => 'Abstract de test',
            'skill' => 0,
        ]);
    }

    private function insertAssociation(Connection $connection, int $talkId, int $speakerId): void
    {
        $connection->insert('afup_conferenciers_sessions', [
            'session_id' => $talkId,
            'conferencier_id' => $speakerId,
        ]);
    }

    /**
     * @return list<int>
     */
    private function fetchSpeakerIds(Connection $connection, int $talkId): array
    {
        return array_map(
            static fn(array $row): int => (int) $row['conferencier_id'],
            $connection->fetchAllAssociative(
                'SELECT conferencier_id FROM afup_conferenciers_sessions WHERE session_id = :talk ORDER BY conferencier_id',
                ['talk' => $talkId],
            ),
        );
    }

    private function buildTalk(int $id): Talk
    {
        return (new Talk())->setId($id);
    }

    private function buildSpeaker(int $id): Speaker
    {
        return (new Speaker())->setId($id);
    }

    private function buildEvent(int $id): Event
    {
        return (new Event())->setId($id);
    }
}
