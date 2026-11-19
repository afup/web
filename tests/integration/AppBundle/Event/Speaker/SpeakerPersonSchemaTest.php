<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Speaker;

use Afup\Tests\Support\IntegrationTestCase;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

final class SpeakerPersonSchemaTest extends IntegrationTestCase
{
    public function testASpeakerRowCanBeLinkedToAPersonAndIsUnlinkedWhenThePersonIsDeleted(): void
    {
        $connection = self::getContainer()->get(Connection::class);

        $connection->insert('afup_speaker_person', ['id' => 1, 'slug' => 'helene-schapira']);
        $connection->insert('afup_conferenciers', ['conferencier_id' => 10, 'id_forum' => 1, 'prenom' => 'Hélène', 'nom' => 'Schapira', 'email' => 'h@example.com', 'person_id' => 1]);
        $connection->insert('afup_conferenciers', ['conferencier_id' => 20, 'id_forum' => 2, 'prenom' => 'Hélène', 'nom' => 'Schapira', 'email' => 'h@example.com']);

        self::assertSame([[10, 1], [20, null]], $this->personIds($connection));

        $connection->delete('afup_speaker_person', ['id' => 1]);

        self::assertSame([[10, null], [20, null]], $this->personIds($connection), 'supprimer une personne détache ses lignes sans les supprimer');
    }

    public function testSlugIsUnique(): void
    {
        $connection = self::getContainer()->get(Connection::class);
        $connection->insert('afup_speaker_person', ['slug' => 'jean-martin']);

        $this->expectException(UniqueConstraintViolationException::class);
        $connection->insert('afup_speaker_person', ['slug' => 'jean-martin']);
    }

    /**
     * @return list<array{int, int|null}>
     */
    private function personIds(Connection $connection): array
    {
        return array_map(
            static fn(array $row): array => [(int) $row['conferencier_id'], null !== $row['person_id'] ? (int) $row['person_id'] : null],
            $connection->fetchAllAssociative('SELECT conferencier_id, person_id FROM afup_conferenciers ORDER BY conferencier_id'),
        );
    }
}
