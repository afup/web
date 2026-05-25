<?php

declare(strict_types=1);

namespace PlanetePHP;

use Doctrine\DBAL\Connection;

final readonly class FeedRepository
{
    public function __construct(private Connection $connection) {}

    /**
     * @return Feed[]
     */
    public function findActive(): array
    {
        $query = $this->connection->prepare('SELECT id, nom, url, feed, etat, id_personne_physique
            FROM afup_planete_flux f WHERE f.etat = :status ORDER BY f.nom');
        $query->bindValue('status', FeedStatus::Active->value);

        return $this->hydrateAll($query->executeQuery()->fetchAllAssociative());
    }

    /**
     * @return Feed[]
     */
    public function find(): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('f.id', 'f.nom', 'f.url', 'f.feed', 'f.etat', 'f.id_personne_physique')
            ->from('afup_planete_flux', 'f')
            ->orderBy('f.nom', 'asc');

        return $this->hydrateAll($qb->executeQuery()->fetchAllAssociative());
    }

    public function get(int $id): Feed
    {
        $query = $this->connection->prepare('SELECT id, nom, url, feed, etat, id_personne_physique
            FROM afup_planete_flux f WHERE f.id = :id');
        $query->bindValue('id', $id);

        return $this->hydrate($query->executeQuery()->fetchAssociative());
    }

    public function insert(string $name, string $url, string $feed, FeedStatus $status, ?int $userId = 0): void
    {
        $statement = $this->connection->prepare('INSERT INTO afup_planete_flux (nom, url, feed, etat, id_personne_physique) VALUES (:name, :url, :feed, :status, :userId)');

        $statement->bindValue('name', $name);
        $statement->bindValue('url', $url);
        $statement->bindValue('feed', $feed);
        $statement->bindValue('status', $status->value);
        $statement->bindValue('userId', (int) $userId);

        $statement->executeStatement();
    }

    public function update(int $id, string $name, string $url, string $feed, FeedStatus $status, ?int $userId = 0): void
    {
        $statement = $this->connection->prepare('UPDATE afup_planete_flux
            SET nom = :name, url = :url, feed = :feed, etat = :status, id_personne_physique = :userId
            WHERE id = :id');

        $statement->bindValue('name', $name);
        $statement->bindValue('url', $url);
        $statement->bindValue('feed', $feed);
        $statement->bindValue('status', $status->value);
        $statement->bindValue('userId', (int) $userId);
        $statement->bindValue('id', $id);

        $statement->executeStatement();
    }

    public function delete(int $id): void
    {
        $this->connection->delete('afup_planete_flux', ['id' => $id]);
    }

    private function hydrateAll(array $rows): array
    {
        return array_map($this->hydrate(...), $rows);
    }

    private function hydrate(array $row): Feed
    {
        return new Feed(
            $row['id'],
            $row['nom'],
            $row['url'],
            $row['feed'],
            FeedStatus::from($row['etat']),
            $row['id_personne_physique'],
        );
    }
}
