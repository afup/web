<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Speaker;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\Repository\SpeakerPersonRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Speaker\SpeakerSearchResult;
use Doctrine\DBAL\Connection;

/**
 * Requêtes de la reprise et du rattachement manuel : recherche tous événements confondus, suggestions, fusion.
 */
final class SpeakerPersonRowsTest extends IntegrationTestCase
{
    private Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = self::getContainer()->get(Connection::class);
        $this->connection->insert('afup_forum', ['id' => 1, 'titre' => 'Forum 2020', 'date_debut' => '2020-11-01']);
        $this->connection->insert('afup_forum', ['id' => 2, 'titre' => 'AFUP Day 2022', 'date_debut' => '2022-05-01']);
        $this->connection->insert('afup_forum', ['id' => 3, 'titre' => 'Forum 2024', 'date_debut' => '2024-11-01']);
        $this->connection->insert('afup_speaker_person', ['id' => 1, 'slug' => 'helene-martin']);
        $this->connection->insert('afup_speaker_person', ['id' => 2, 'slug' => 'helene-martin-2']);
    }

    public function testSearchAcrossEventsReturnsOnePersonOrOneUnlinkedRowPointingToTheLatestRow(): void
    {
        $this->insertSpeaker(10, 1, 'Hélène', 'Martin', 'Acme', 1);
        $this->insertSpeaker(11, 3, 'Hélène', 'Martin', 'Nouvelle société', 1);
        $this->insertSpeaker(12, 2, 'Helene', 'Martin', 'Autre', null);
        $this->insertSpeaker(13, 2, 'Paul', 'Durand', 'Acme', null);

        $results = self::getContainer()->get(SpeakerRepository::class)->searchAcrossEvents('helene');

        self::assertSame(
            [[11, 1, 'helene-martin', 'Nouvelle société', 'Forum 2024', 2], [12, null, null, 'Autre', 'AFUP Day 2022', 1]],
            array_map(static fn(SpeakerSearchResult $r): array => [$r->speakerId, $r->personId, $r->personSlug, $r->company, $r->lastEventTitle, $r->eventCount], $results),
            'recherche insensible aux accents, une ligne par personne, la plus récente',
        );
    }

    public function testSearchAcrossEventsMatchesCompanyAndEmailAndCountsEveryRowOfAMatchingPerson(): void
    {
        $this->insertSpeaker(10, 1, 'Hélène', 'Martin', 'Ancienne boîte', 1);
        $this->insertSpeaker(11, 3, 'Hélène', 'Martin', 'Acme', 1);

        $results = self::getContainer()->get(SpeakerRepository::class)->searchAcrossEvents('acme');

        self::assertCount(1, $results);
        self::assertSame(2, $results[0]->eventCount);
        self::assertCount(1, self::getContainer()->get(SpeakerRepository::class)->searchAcrossEvents('h.martin@example'));
        self::assertSame([], self::getContainer()->get(SpeakerRepository::class)->searchAcrossEvents('  '));
    }

    public function testLinkSuggestionsAreUnlinkedRowsWithTheSameGithubAccountOrTheSameName(): void
    {
        $this->insertSpeaker(10, 1, 'Hélène', 'Martin', null, 1, 7);
        $this->insertSpeaker(11, 2, 'HELENE', 'MARTIN', null, null);
        $this->insertSpeaker(12, 3, 'H.', 'Martin', null, null, 7);
        $this->insertSpeaker(13, 3, 'Paul', 'Martin', null, null);
        $this->insertSpeaker(14, 3, 'Hélène', 'Martin', null, 2);

        $suggestions = self::getContainer()->get(SpeakerRepository::class)->findLinkSuggestions(1);

        self::assertSame([12, 11], array_map(static fn($speaker): ?int => $speaker->getId(), $suggestions));
    }

    public function testMergeMovesTheRowsThenDeletesTheMergedPerson(): void
    {
        $this->insertSpeaker(10, 1, 'Hélène', 'Martin', null, 1);
        $this->insertSpeaker(11, 3, 'Hélène', 'Martin', null, 2);
        $this->insertSpeaker(12, 2, 'Paul', 'Durand', null, null);

        $repository = self::getContainer()->get(SpeakerPersonRepository::class);
        $repository->merge($repository->find(1), $repository->find(2));

        self::assertSame(
            [[10, 1], [11, 1], [12, null]],
            array_map(
                static fn(array $row): array => [(int) $row['conferencier_id'], $row['person_id'] === null ? null : (int) $row['person_id']],
                $this->connection->fetchAllAssociative('SELECT conferencier_id, person_id FROM afup_conferenciers ORDER BY conferencier_id'),
            ),
        );
        self::assertSame(['helene-martin'], $this->connection->fetchFirstColumn('SELECT slug FROM afup_speaker_person'));
    }

    private function insertSpeaker(int $id, int $eventId, string $firstname, string $lastname, ?string $company, ?int $personId, ?int $githubUserId = null): void
    {
        $this->connection->insert('afup_conferenciers', [
            'conferencier_id' => $id,
            'id_forum' => $eventId,
            'prenom' => $firstname,
            'nom' => $lastname,
            'email' => 'h.martin@example.com',
            'societe' => $company,
            'person_id' => $personId,
            'user_github' => $githubUserId,
        ]);
    }
}
