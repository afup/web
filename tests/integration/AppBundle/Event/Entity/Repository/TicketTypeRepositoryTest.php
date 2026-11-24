<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\Repository\TicketTypeRepository;
use AppBundle\Event\Entity\TicketEventType;
use AppBundle\Event\Entity\TicketType;
use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Model\TicketAggregate;
use Doctrine\DBAL\Connection;

final class TicketTypeRepositoryTest extends IntegrationTestCase
{
    public function testFindAllOrderedByIdReturnsTypesSortedById(): void
    {
        $repository = self::getContainer()->get(TicketTypeRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $idA = $this->insertTicketType($connection, ['technical_name' => 'TEST_A', 'pretty_name' => 'Tarif A']);
        $idB = $this->insertTicketType($connection, ['technical_name' => 'TEST_B', 'pretty_name' => 'Tarif B']);

        $ticketTypes = $repository->findAllOrderedById();

        self::assertCount(2, $ticketTypes);
        self::assertSame($idA, $ticketTypes[0]->id);
        self::assertSame($idB, $ticketTypes[1]->id);
    }

    public function testFindReturnsHydratedEntity(): void
    {
        $repository = self::getContainer()->get(TicketTypeRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $id = $this->insertTicketType($connection, [
            'technical_name' => 'TEST_FULL',
            'pretty_name' => 'Tarif complet',
            'public' => 1,
            'members_only' => 0,
            'default_price' => 120.5,
            'active' => 1,
            'day' => 'one,two',
            'cfp_submitter_only' => 1,
        ]);

        $ticketType = $repository->find($id);

        self::assertInstanceOf(TicketType::class, $ticketType);
        self::assertSame($id, $ticketType->id);
        self::assertSame('TEST_FULL', $ticketType->technicalName);
        self::assertSame('Tarif complet', $ticketType->prettyName);
        self::assertTrue($ticketType->isPublic);
        self::assertFalse($ticketType->isRestrictedToMembers);
        self::assertTrue($ticketType->isRestrictedToCfpSubmitter);
        self::assertSame(120.5, $ticketType->defaultPrice);
        self::assertTrue($ticketType->isActive);
        self::assertSame(['one', 'two'], $ticketType->getDays());
        self::assertSame('JOUR 1, JOUR 2', $ticketType->getPrettyDays());
        self::assertSame('TEST_FULL - Tarif complet - JOUR 1, JOUR 2', $ticketType->getLabel());
    }

    public function testIsEarlyDetectsEarlyBirdTechnicalNames(): void
    {
        $repository = self::getContainer()->get(TicketTypeRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $earlyId = $this->insertTicketType($connection, ['technical_name' => 'EARLY_BIRD']);
        $earlyAfupId = $this->insertTicketType($connection, ['technical_name' => 'AFUP_DAY_EARLY']);
        $classicId = $this->insertTicketType($connection, ['technical_name' => 'COMITE']);

        self::assertTrue($repository->find($earlyId)->isEarly());
        self::assertTrue($repository->find($earlyAfupId)->isEarly());
        self::assertFalse($repository->find($classicId)->isEarly());
    }

    public function testFindReturnsNullForUnknownId(): void
    {
        $repository = self::getContainer()->get(TicketTypeRepository::class);

        self::assertNull($repository->find(999999));
    }

    public function testAttachToRenseigneLeTypeDeTarif(): void
    {
        $repository = self::getContainer()->get(TicketTypeRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $id = $this->insertTicketType($connection, ['pretty_name' => 'Tarif rattaché']);

        $connu = new TicketEventType();
        $connu->ticketTypeId = $id;
        // Tarif d'évènement dont le type n'existe plus
        $inconnu = new TicketEventType();
        $inconnu->ticketTypeId = 999999;

        $repository->attachTo([$connu, $inconnu]);

        self::assertSame('Tarif rattaché', $connu->ticketType?->prettyName);
        self::assertNull($inconnu->ticketType);
    }

    public function testEnrichTicketAggregatesChargeLeTypeDeTarif(): void
    {
        $repository = self::getContainer()->get(TicketTypeRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $id = $this->insertTicketType($connection, ['pretty_name' => 'Tarif inscrit']);

        $connue = new Inscription();
        $connue->ticketTypeId = $id;
        $inconnue = new Inscription();
        $inconnue->ticketTypeId = 999999;

        $aggregates = $repository->enrichTicketAggregates([
            new TicketAggregate($connue, null, null, null),
            new TicketAggregate($inconnue, null, null, null),
        ]);

        self::assertCount(2, $aggregates);
        self::assertSame('Tarif inscrit', $aggregates[0]->ticketType?->prettyName);
        self::assertNull($aggregates[1]->ticketType);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function insertTicketType(Connection $connection, array $data = []): int
    {
        $connection->insert('afup_forum_tarif', $data + [
            'technical_name' => 'TEST',
            'pretty_name' => 'Tarif',
            'public' => 1,
            'members_only' => 0,
            'default_price' => 10.0,
            'active' => 1,
            'day' => 'one',
            'cfp_submitter_only' => 0,
        ]);

        return (int) $connection->lastInsertId();
    }
}
