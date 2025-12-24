<?php

declare(strict_types=1);

namespace PlanetePHP;

use Assert\Assertion;
use Doctrine\DBAL\Connection;

class FeedRepository
{
    public function __construct(private readonly Connection $connection) {}

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
     * @param string      $sort
     * @param string      $direction
     * @param string|null $filter
     *
     * @return Feed[]
     */
    public function find($sort = 'name', $direction = 'asc', $filter = null): array
    {
        $sorts = [
            'name' => 'f.nom',
            'url' => 'f.url',
            'status' => 'f.etat',
        ];
        Assertion::keyExists($sorts, $sort);
        $qb = $this->connection->createQueryBuilder()
            ->select('f.id', 'f.nom', 'f.url', 'f.feed', 'f.etat', 'f.id_personne_physique')
            ->from('afup_planete_flux', 'f')
            ->orderBy($sorts[$sort], $direction);
        if (null !== $filter) {
            $qb->where('nom LIKE :filter')
                ->setParameter('filter', '%' . $filter . '%');
        }

        return $this->hydrateAll($qb->executeQuery()->fetchAllAssociative());
    }

    public function get($id): Feed
    {
        $query = $this->connection->prepare('SELECT id, nom, url, feed, etat, id_personne_physique
            FROM afup_planete_flux f WHERE f.id = :id');
        $query->bindValue('id', $id);

        return $this->hydrate($query->executeQuery()->fetchAssociative());
    }

    public function insert(string $name, string $url, string $feed, FeedStatus $status, ?int $userId = 0)
    {
        $statement = $this->connection->prepare('INSERT INTO afup_planete_flux (nom, url, feed, etat, id_personne_physique) VALUES (:name, :url, :feed, :status, :userId)');

        $statement->bindValue('name', $name);
        $statement->bindValue('url', $url);
        $statement->bindValue('feed', $feed);
        $statement->bindValue('status', $status->value);
        $statement->bindValue('userId', (int) $userId);

        return $statement->executeStatement();
    }

    public function update(int $id, string $name, string $url, string $feed, FeedStatus $status, ?int $userId = 0)
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

        return $statement->executeStatement();
    }

    public function delete(int $id): bool
    {
        return $this->connection->delete('afup_planete_flux', ['id' => $id]) === 1;
    }

    private function hydrateAll(array $rows): array
    {
        return array_map(fn(array $row): Feed => $this->hydrate($row), $rows);
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
