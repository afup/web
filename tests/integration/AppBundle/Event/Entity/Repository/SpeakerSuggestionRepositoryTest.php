<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\Repository\SpeakerSuggestionRepository;
use AppBundle\Event\Entity\SpeakerSuggestion;
use Doctrine\DBAL\Connection;

final class SpeakerSuggestionRepositoryTest extends IntegrationTestCase
{
    public function testSavePersistsTheSuggestionAndGeneratesItsId(): void
    {
        $repository = self::getContainer()->get(SpeakerSuggestionRepository::class);

        $suggestion = $this->buildSuggestion();
        $repository->save($suggestion);

        self::assertNotNull($suggestion->id);
    }

    public function testSavePersistsEveryFieldOfTheSuggestion(): void
    {
        $repository = self::getContainer()->get(SpeakerSuggestionRepository::class);

        $suggestion = $this->buildSuggestion();
        $repository->save($suggestion);

        $fromDatabase = $repository->find($suggestion->id);

        self::assertInstanceOf(SpeakerSuggestion::class, $fromDatabase);
        self::assertSame(42, $fromDatabase->eventId);
        self::assertSame('suggere@example.com', $fromDatabase->suggesterEmail);
        self::assertSame('Camille Soumetteur', $fromDatabase->suggesterName);
        self::assertSame('Doe Speaker', $fromDatabase->speakerName);
        self::assertSame('Parle très bien des sabres laser', $fromDatabase->comment);
        self::assertEquals(new \DateTimeImmutable('2026-01-10 09:00:00'), $fromDatabase->createdAt);
        self::assertNull($repository->find($suggestion->id + 1));
    }

    public function testSaveSupportsANullComment(): void
    {
        $repository = self::getContainer()->get(SpeakerSuggestionRepository::class);

        $suggestion = $this->buildSuggestion();
        $suggestion->comment = null;
        $repository->save($suggestion);

        $fromDatabase = $repository->find($suggestion->id);

        self::assertInstanceOf(SpeakerSuggestion::class, $fromDatabase);
        self::assertNull($fromDatabase->comment);
    }

    public function testDeleteRemovesTheSuggestion(): void
    {
        $repository = self::getContainer()->get(SpeakerSuggestionRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $suggestion = $this->buildSuggestion();
        $repository->save($suggestion);
        $id = $suggestion->id;

        $repository->delete($suggestion);

        // Doctrine remet l'identifiant à null après la suppression
        self::assertNull($suggestion->id);
        self::assertNull($repository->find($id));
        self::assertSame(
            0,
            $connection->fetchOne('SELECT COUNT(*) FROM afup_speaker_suggestion'),
        );
    }

    private function buildSuggestion(): SpeakerSuggestion
    {
        $suggestion = new SpeakerSuggestion();
        $suggestion->eventId = 42;
        $suggestion->suggesterEmail = 'suggere@example.com';
        $suggestion->suggesterName = 'Camille Soumetteur';
        $suggestion->speakerName = 'Doe Speaker';
        $suggestion->comment = 'Parle très bien des sabres laser';
        $suggestion->createdAt = new \DateTimeImmutable('2026-01-10 09:00:00');

        return $suggestion;
    }
}
