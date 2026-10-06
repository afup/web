<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\TicketEventType;
use AppBundle\Event\Entity\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Event;
use Doctrine\DBAL\Connection;

final class TicketEventTypeRepositoryTest extends IntegrationTestCase
{
    public function testGetTicketsByEventRetourneLesTarifsDEvenement(): void
    {
        $repository = self::getContainer()->get(TicketEventTypeRepository::class);
        $this->insertTicketEventTypes();

        $ticketEventTypes = $repository->getTicketsByEvent($this->buildEvent(999));

        self::assertCount(2, $ticketEventTypes);
        self::assertSame(9001, $ticketEventTypes[0]->ticketTypeId);
        self::assertSame(150.0, $ticketEventTypes[0]->price);
        self::assertSame(2, $ticketEventTypes[0]->maxTickets);
        self::assertSame('Tarif public', $ticketEventTypes[0]->ticketType->getPrettyName());
        self::assertSame(9002, $ticketEventTypes[1]->ticketTypeId);
        self::assertTrue($ticketEventTypes[1]->ticketType->getIsRestrictedToMembers());
    }

    public function testGetTicketsByEventAvecFiltrePublic(): void
    {
        $repository = self::getContainer()->get(TicketEventTypeRepository::class);
        $this->insertTicketEventTypes();

        $ticketEventTypes = $repository->getTicketsByEvent($this->buildEvent(999), false);

        self::assertCount(3, $ticketEventTypes);
        self::assertSame(9003, $ticketEventTypes[2]->ticketTypeId);
    }

    public function testGetTicketsByEventAvecFiltresDeDates(): void
    {
        $repository = self::getContainer()->get(TicketEventTypeRepository::class);
        $this->insertTicketEventTypes();

        // seul le tarif passe (date_end deja passee) est exclu quand on retire les tarifs passes
        $sansPasses = $repository->getTicketsByEvent($this->buildEvent(999), false, TicketEventTypeRepository::REMOVE_PAST_TICKETS);
        self::assertCount(2, $sansPasses);
        self::assertSame(9002, $sansPasses[0]->ticketTypeId);

        // seul le tarif futur est exclu quand on retire les tarifs futurs
        $sansFuturs = $repository->getTicketsByEvent($this->buildEvent(999), false, TicketEventTypeRepository::REMOVE_FUTURE_TICKETS);
        self::assertCount(2, $sansFuturs);
        self::assertNotContains(9003, array_map(static fn(TicketEventType $t): int => $t->ticketTypeId, $sansFuturs));

        // la combinaison des deux garde uniquement les tarifs en cours de vente
        $actuels = $repository->getTicketsByEvent($this->buildEvent(999), false, TicketEventTypeRepository::ACTUAL_TICKETS_ONLY);
        self::assertCount(1, $actuels);
        self::assertSame(9002, $actuels[0]->ticketTypeId);
    }

    public function testDoesEventHasRestrictedToMembersTickets(): void
    {
        $repository = self::getContainer()->get(TicketEventTypeRepository::class);
        $this->insertTicketEventTypes();

        self::assertTrue($repository->doesEventHasRestrictedToMembersTickets($this->buildEvent(999)));
        self::assertFalse($repository->doesEventHasRestrictedToMembersTickets($this->buildEvent(998)));
    }

    public function testSaveInsereEtMetAJourAvecClePrimaireComposee(): void
    {
        $repository = self::getContainer()->get(TicketEventTypeRepository::class);

        $ticketEventType = new TicketEventType();
        $ticketEventType->ticketTypeId = 9001;
        $ticketEventType->eventId = 777;
        $ticketEventType->price = 99.0;
        $ticketEventType->dateStart = new \DateTime('2020-01-01 10:00:00');
        $ticketEventType->dateEnd = new \DateTime('2030-01-01 18:00:00');
        $ticketEventType->description = 'Nouveau tarif';
        $ticketEventType->maxTickets = 5;
        $repository->save($ticketEventType);

        $saved = $repository->find(['ticketTypeId' => 9001, 'eventId' => 777]);
        self::assertNotNull($saved);
        self::assertSame(99.0, $saved->price);
        self::assertSame(5, $saved->maxTickets);
        self::assertSame('Nouveau tarif', $saved->description);

        $ticketEventType->price = 100.0;
        $ticketEventType->maxTickets = null;
        $repository->save($ticketEventType);

        $updated = $repository->find(['ticketTypeId' => 9001, 'eventId' => 777]);
        self::assertNotNull($updated);
        self::assertSame(100.0, $updated->price);
        self::assertNull($updated->maxTickets);
    }

    private function buildEvent(int $eventId): Event
    {
        $event = new Event();
        $event->setId($eventId);

        return $event;
    }

    private function insertTicketEventTypes(): void
    {
        $connection = self::getContainer()->get(Connection::class);

        $tarifs = [
            ['id' => 9001, 'technical_name' => 'T_PUBLIC', 'pretty_name' => 'Tarif public', 'public' => 1, 'members_only' => 0, 'cfp_submitter_only' => 0, 'default_price' => 150.0, 'active' => 1, 'day' => 'one'],
            ['id' => 9002, 'technical_name' => 'T_MEMBERS', 'pretty_name' => 'Tarif membres', 'public' => 1, 'members_only' => 1, 'cfp_submitter_only' => 0, 'default_price' => 100.0, 'active' => 1, 'day' => 'two'],
            ['id' => 9003, 'technical_name' => 'T_CACHE', 'pretty_name' => 'Tarif cache', 'public' => 0, 'members_only' => 0, 'cfp_submitter_only' => 0, 'default_price' => 50.0, 'active' => 1, 'day' => 'one'],
        ];
        foreach ($tarifs as $tarif) {
            $connection->insert('afup_forum_tarif', $tarif);
        }

        $ticketEventTypes = [
            ['id_tarif' => 9001, 'id_event' => 999, 'price' => 150.0, 'date_start' => '2009-01-01 10:00:00', 'date_end' => '2020-01-01 12:00:00', 'description' => 'Tarif passe', 'max_tickets' => 2],
            ['id_tarif' => 9002, 'id_event' => 999, 'price' => 100.0, 'date_start' => '2010-01-01 10:00:00', 'date_end' => '2099-12-31 12:00:00', 'description' => 'Tarif en cours', 'max_tickets' => 10],
            ['id_tarif' => 9003, 'id_event' => 999, 'price' => 50.0, 'date_start' => '2099-01-01 10:00:00', 'date_end' => '2099-12-31 12:00:00', 'description' => 'Tarif futur', 'max_tickets' => 20],
        ];
        foreach ($ticketEventTypes as $ticketEventType) {
            $connection->insert('afup_forum_tarif_event', $ticketEventType);
        }
    }
}
