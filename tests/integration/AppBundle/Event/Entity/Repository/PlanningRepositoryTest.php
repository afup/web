<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\Planning;
use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Entity\Repository\RoomRepository;
use AppBundle\Event\Entity\Room;
use AppBundle\Event\Entity\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\TalkAggregate;
use Doctrine\DBAL\Connection;

final class PlanningRepositoryTest extends IntegrationTestCase
{
    public function testGetByTalkEtCrud(): void
    {
        $planningRepository = self::getContainer()->get(PlanningRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertPlanning($connection, '1007', '1451606400', '1451608800', '1', '10', '0');

        $talk = new Talk()->setId(1007);

        $planning = $planningRepository->getByTalk($talk);
        self::assertInstanceOf(Planning::class, $planning);
        self::assertSame(1007, $planning->talkId);
        self::assertSame(10, $planning->eventId);
        self::assertSame(1, $planning->roomId);
        self::assertFalse($planning->isKeynote);
        // Les horaires sont stockés en timestamp : la valeur convertie doit être équivalente
        self::assertSame(1451606400, $planning->start->getTimestamp());
        self::assertSame(1451608800, $planning->end->getTimestamp());

        self::assertNull($planningRepository->getByTalk(new Talk()->setId(9999)));

        // CRUD via EntityRepository
        $planning = new Planning();
        $planning->talkId = 1007;
        $planning->eventId = 10;
        $planning->start = new \DateTime('@1451606400');
        $planning->end = new \DateTime('@1451608800');
        $planning->roomId = 1;
        $planningRepository->save($planning);
        self::assertNotNull($planning->id);

        $saved = $planningRepository->find($planning->id);
        self::assertInstanceOf(Planning::class, $saved);
        self::assertSame(1007, $saved->talkId);

        $id = $planning->id;
        $planningRepository->delete($planning);
        self::assertNull($planningRepository->find($id));
    }

    public function testFindNonKeynotesBetween(): void
    {
        $planningRepository = self::getContainer()->get(PlanningRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertPlanning($connection, '1001', '1451606401', '1451608800', '1', '10', '0');
        // En dessous de la borne inférieure
        $this->insertPlanning($connection, '1002', '1451606399', '1451608800', '1', '10', '0');
        // Au dessus de la borne supérieure (exclusive)
        $this->insertPlanning($connection, '1003', '1451692800', '1451695200', '1', '10', '0');
        // Keynote : exclue même dans la plage
        $this->insertPlanning($connection, '1004', '1451606401', '1451608800', '1', '10', '1');

        $since = new \DateTime()->setTimestamp(1451606400);
        $until = new \DateTime()->setTimestamp(1451692800);

        $plannings = $planningRepository->findNonKeynotesBetween($since, $until);

        self::assertCount(1, $plannings);
        $planning = $plannings[0];
        self::assertInstanceOf(Planning::class, $planning);
        self::assertSame(1001, $planning->talkId);
        self::assertFalse($planning->isKeynote);
    }

    public function testEnrichTalkAggregatesChargeLePlanningEtLaSalle(): void
    {
        $planningRepository = self::getContainer()->get(PlanningRepository::class);
        $roomRepository = self::getContainer()->get(RoomRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $room = new Room();
        $room->name = 'Salle Rasmus';
        $room->eventId = 10;
        $roomRepository->save($room);

        $this->insertPlanning($connection, '1011', '1451606400', '1451608800', (string) $room->id, '10', '0');
        // Planning dont la salle n'existe plus
        $this->insertPlanning($connection, '1012', '1451606400', '1451608800', '9999', '10', '0');
        $connection->insert('afup_conferenciers', ['conferencier_id' => 1, 'id_forum' => 10, 'prenom' => 'Hélène', 'nom' => 'Schapira']);
        $connection->insert('afup_conferenciers_sessions', ['session_id' => 1011, 'conferencier_id' => 1]);

        $aggregates = $planningRepository->enrichTalkAggregates([
            new TalkAggregate(new Talk()->setId(1011), [], null, null),
            new TalkAggregate(new Talk()->setId(1012), [], null, null),
            // Session non planifiée
            new TalkAggregate(new Talk()->setId(1013), [], null, null),
        ]);

        self::assertCount(3, $aggregates);
        self::assertSame(1011, $aggregates[0]->planning?->talkId);
        self::assertSame($room->id, $aggregates[0]->room?->id);
        self::assertSame('Salle Rasmus', $aggregates[0]->room->name);
        self::assertSame(['Hélène SCHAPIRA'], array_map(static fn(Speaker $speaker): string => $speaker->label, $aggregates[0]->speakers));
        self::assertSame([], $aggregates[1]->speakers);

        self::assertSame(1012, $aggregates[1]->planning?->talkId);
        self::assertNull($aggregates[1]->room);

        self::assertNull($aggregates[2]->planning);
        self::assertNull($aggregates[2]->room);
    }

    /**
     * @return void
     */
    private function insertPlanning(Connection $connection, string $talkId, string $debut, string $fin, string $salle, string $forum, string $keynote): void
    {
        $connection->insert('afup_forum_planning', [
            'id_session' => $talkId,
            'debut' => $debut,
            'fin' => $fin,
            'id_salle' => $salle,
            'id_forum' => $forum,
            'keynote' => $keynote,
        ]);
    }
}
