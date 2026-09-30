<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\Repository\RoomRepository;
use AppBundle\Event\Entity\Room;
use AppBundle\Event\Model\Event;

final class RoomRepositoryTest extends IntegrationTestCase
{
    public function testSaveGetByEventAndDelete(): void
    {
        $roomRepository = self::getContainer()->get(RoomRepository::class);
        $event = new Event();
        $event->setId(4242);

        // Deux salles : une pour l'événement testé, l'autre pour un événement voisin
        $room = new Room();
        $room->name = 'La salle de test';
        $room->eventId = 4242;
        $roomRepository->save($room);

        $autre = new Room();
        $autre->name = 'La salle ailleurs';
        $autre->eventId = 99;
        $roomRepository->save($autre);

        // L'auto-incrément remplit l'identifiant à la sauvegarde
        self::assertNotNull($room->id);
        self::assertGreaterThan(0, $room->id);

        $rooms = $roomRepository->getByEvent($event);
        self::assertCount(1, $rooms);
        self::assertSame('La salle de test', $rooms[0]->name);
        self::assertSame($room->id, $rooms[0]->id);

        // Mise à jour puis vidage de l'EntityManager pour vérifier la persistance en base
        $room->name = 'La salle renommée';
        $roomRepository->save($room);
        $roomRepository->getEntityManager()->clear();

        $reloaded = $roomRepository->find($room->id);
        self::assertInstanceOf(Room::class, $reloaded);
        self::assertSame('La salle renommée', $reloaded->name);
        self::assertSame(4242, $reloaded->eventId);

        $roomRepository->delete($reloaded);

        self::assertNull($roomRepository->find($room->id));
        self::assertCount(0, $roomRepository->getByEvent($event));
    }
}
