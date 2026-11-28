<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Speaker;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\Speaker;
use AppBundle\Event\Speaker\PersonneConferencierLinker;
use Doctrine\DBAL\Connection;

final class PersonneConferencierLinkerTest extends IntegrationTestCase
{
    public function testEnsurePersonneReusesThePersonneOfTheMostRecentRowOfTheGithubAccount(): void
    {
        $connection = self::getContainer()->get(Connection::class);
        $connection->insert('afup_forum', ['id' => 1, 'titre' => 'Forum 2020', 'date_debut' => '2020-11-01']);
        $connection->insert('afup_forum', ['id' => 2, 'titre' => 'Forum 2024', 'date_debut' => '2024-11-01']);
        $connection->insert('afup_conferenciers_personnes', ['id' => 1, 'slug' => 'ancienne-personne']);
        $connection->insert('afup_conferenciers_personnes', ['id' => 2, 'slug' => 'marie-curie']);
        $this->insertSpeaker($connection, 10, 1, 7, 1);
        $this->insertSpeaker($connection, 11, 2, 7, 2);

        $speaker = $this->buildSpeaker(7);
        self::getContainer()->get(PersonneConferencierLinker::class)->ensurePersonne($speaker);

        self::assertSame(2, $speaker->personneId);
        self::assertSame(2, (int) $connection->fetchOne('SELECT COUNT(*) FROM afup_conferenciers_personnes'), 'aucune personne créée');
    }

    public function testEnsurePersonneCreatesAPersonneWhenTheGithubAccountHasNone(): void
    {
        $connection = self::getContainer()->get(Connection::class);

        $speaker = $this->buildSpeaker(7);
        self::getContainer()->get(PersonneConferencierLinker::class)->ensurePersonne($speaker);

        self::assertNotNull($speaker->personneId);
        self::assertSame('marie-curie', $connection->fetchOne('SELECT slug FROM afup_conferenciers_personnes WHERE id = ?', [$speaker->personneId]));
    }

    public function testEnsurePersonneSuffixesTheSlugWhenItIsAlreadyTaken(): void
    {
        $connection = self::getContainer()->get(Connection::class);
        $connection->insert('afup_conferenciers_personnes', ['slug' => 'marie-curie']);
        $connection->insert('afup_conferenciers_personnes', ['slug' => 'marie-curie-2']);

        $speaker = $this->buildSpeaker(null);
        self::getContainer()->get(PersonneConferencierLinker::class)->ensurePersonne($speaker);

        self::assertSame('marie-curie-3', $connection->fetchOne('SELECT slug FROM afup_conferenciers_personnes WHERE id = ?', [$speaker->personneId]));
    }

    public function testEnsurePersonneKeepsAnExistingPersonne(): void
    {
        $speaker = $this->buildSpeaker(7);
        $speaker->personneId = 42;
        self::getContainer()->get(PersonneConferencierLinker::class)->ensurePersonne($speaker);

        self::assertSame(42, $speaker->personneId);
    }

    private function buildSpeaker(?int $githubUserId): Speaker
    {
        $speaker = new Speaker();
        $speaker->firstname = 'Marie';
        $speaker->lastname = 'Curie';
        $speaker->githubUserId = $githubUserId;

        return $speaker;
    }

    private function insertSpeaker(Connection $connection, int $id, int $eventId, ?int $githubUserId, ?int $personneId): void
    {
        $connection->insert('afup_conferenciers', [
            'conferencier_id' => $id,
            'id_forum' => $eventId,
            'prenom' => 'Marie',
            'nom' => 'Curie',
            'email' => 'marie@example.com',
            'user_github' => $githubUserId,
            'personne_id' => $personneId,
        ]);
    }
}
