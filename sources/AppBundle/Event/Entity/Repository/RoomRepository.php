<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\Room;
use AppBundle\Event\Model\Event;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Room>
 */
final class RoomRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    /**
     * @return list<Room>
     */
    public function getByEvent(Event $event): array
    {
        return array_values($this->findBy(['eventId' => $event->getId()]));
    }

    /**
     * @param list<int> $ids
     *
     * @return array<int, Room>
     */
    public function findIndexedByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $rooms = [];
        foreach ($this->findBy(['id' => array_values(array_unique($ids))]) as $room) {
            $rooms[$room->id] = $room;
        }

        return $rooms;
    }
}
