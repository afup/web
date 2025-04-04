<?php

declare(strict_types=1);

namespace AppBundle\Doctrine;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Cette classe permet d'accéder à la connexion à la base de donnée de façon typée.
 *
 * Les méthodes de lectures ont besoin d'un DTO qui sera hydraté avec le résultat du query builder.
 */
final class DoctrineConnection
{
    private Connection $connection;
    private TreeMapper $mapper;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->mapper = (new MapperBuilder())->mapper();
    }

    /**
     * @param callable(QueryBuilder): QueryBuilder $callback
     */
    public function statement(callable $callback): void
    {
        $queryBuilder = $callback($this->connection->createQueryBuilder());
        $queryBuilder->execute();
    }

    /**
     * Cette méthode permet de mapper une classe sur une seule ligne retournée.
     *
     * @template T
     * @param class-string<T> $type
     * @param callable(QueryBuilder): QueryBuilder $callback
     * @return T
     */
    public function mapOne(string $type, callable $callback)
    {
        $queryBuilder = $callback($this->connection->createQueryBuilder());

        $result = $queryBuilder->execute()->fetchAssociative();

        if ($result === false) {
            return null;
        }

        return $this->mapper->map($type, Source::array($result)->camelCaseKeys());
    }

    /**
     * Cette méthode permet de mapper chaque ligne retournée dans une classe spécifiée.
     *
     * @template T
     * @param class-string<T> $type
     * @param callable(QueryBuilder): QueryBuilder $callback
     * @return list<T>
     */
    public function mapMany(string $type, callable $callback): array
    {
        $queryBuilder = $callback($this->connection->createQueryBuilder());

        $result = $queryBuilder->execute()->fetchAllAssociative();

        if ($result === []) {
            return [];
        }

        return $this->mapper->map('list<' . $type . '>', Source::array($result)->camelCaseKeys());
    }
}
