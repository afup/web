<?php

declare(strict_types=1);

namespace integration\AppBundle\Doctrine;

use Afup\Tests\Support\Fake\DoctrineConnection\FakeRow;
use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Doctrine\DoctrineConnection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Faker\Factory;

final class DoctrineConnectionTest extends IntegrationTestCase
{
    public function testMapOne(): void
    {
        $faker = Factory::create();

        $tableName = 'test_table_' . $faker->word();

        $connection = self::getContainer()->get(Connection::class);
        $connection->executeStatement("CREATE TABLE $tableName(value TEXT);");

        $doctrineConnection = self::getContainer()->get(DoctrineConnection::class);

        $value = $faker->sentence();

        $doctrineConnection->statement(
            fn (QueryBuilder $qb) => $qb
                ->insert($tableName)
                ->values([
                    'value' => ':value',
                ])
                ->setParameter('value', $value)
        );

        $result = $doctrineConnection->mapOne(
            FakeRow::class,
            fn (QueryBuilder $qb) => $qb
                ->select(['*'])
                ->from($tableName)
                ->setMaxResults(1)
        );

        self::assertInstanceOf(FakeRow::class, $result);
        self::assertEquals($value, $result->value);
    }

    public function testMapMany(): void
    {
        $faker = Factory::create();

        $tableName = 'test_table_' . $faker->word();

        $connection = self::getContainer()->get(Connection::class);
        $connection->executeStatement("CREATE TABLE $tableName(value TEXT);");

        $doctrineConnection = self::getContainer()->get(DoctrineConnection::class);

        $value1 = $faker->sentence();
        $value2 = $faker->sentence();

        $doctrineConnection->statement(
            fn (QueryBuilder $qb) => $qb
                ->insert($tableName)
                ->values([
                    'value' => ':value',
                ])
                ->setParameter('value', $value1)
        );

        $doctrineConnection->statement(
            fn (QueryBuilder $qb) => $qb
                ->insert($tableName)
                ->values([
                    'value' => ':value',
                ])
                ->setParameter('value', $value2)
        );

        $results = $doctrineConnection->mapMany(
            FakeRow::class,
            fn (QueryBuilder $qb) => $qb
                ->select(['*'])
                ->from($tableName)
        );

        self::assertCount(2, $results);

        self::assertInstanceOf(FakeRow::class, $results[0]);
        self::assertInstanceOf(FakeRow::class, $results[1]);

        self::assertEquals($value1, $results[0]->value);
        self::assertEquals($value2, $results[1]->value);
    }
}
