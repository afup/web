<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\EventStats\CFPStats;
use AppBundle\Event\Model\EventStats\TicketTypeStats;
use AppBundle\Event\Model\EventStats;
use AppBundle\Event\Model\EventStats\DailyStats;
use AppBundle\Event\Model\Ticket;
use Datetime;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Webmozart\Assert\Assert;

class EventStatsRepository
{
    private const DAY_ONE = 'one';
    private const DAY_TWO = 'two';
    private const DAYS = [self::DAY_ONE, self::DAY_TWO];

    public function __construct(
        private readonly Connection $connection,
        private readonly TalkRepository $talkRepository,
        private readonly TalkToSpeakersRepository $talkToSpeakersRepository,
    ) {}

    public function getStats(int $eventId, Datetime $from = null): EventStats
    {
        return new EventStats(
            $this->getStatsForDay($eventId, self::DAY_ONE, $from),
            $this->getStatsForDay($eventId, self::DAY_TWO, $from),
            $this->getStatsForTicketTypes($eventId, $from),
            $this->getCFPStats($eventId),
        );
    }

    public function getStatsForTicketTypes(int $eventId, ?Datetime $from): TicketTypeStats
    {
        // Nombre de personnes validées par type d'inscription
        $baseQueryBuilder = $this->connection->createQueryBuilder()
            ->select('type_inscription', 'COUNT(*) AS c')
            ->from('afup_inscription_forum', 'aif')
            ->where('id_forum = :eventId')
            ->groupBy('type_inscription')
            ->setParameter('eventId', $eventId);

        if ($from instanceof \Datetime) {
            $baseQueryBuilder->andWhere('aif.date > :from')
                ->setParameter('from', $from->getTimestamp());
        }

        $queryBuilder = clone $baseQueryBuilder;
        $statement = $queryBuilder->andWhere('etat IN(:states)')
            ->setParameter('states', [Ticket::STATUS_PAID, Ticket::STATUS_WAITING, Ticket::STATUS_GUEST], ArrayParameterType::INTEGER)
            ->executeQuery();

        $confirmed = [];
        foreach ($statement->fetchAllAssociative() as $row) {
            $confirmed[$row['type_inscription']] = $row['c'];
        }

        $queryBuilder = clone $baseQueryBuilder;
        $statement = $queryBuilder->andWhere('etat IN(:states)')
            ->setParameter('states', [Ticket::STATUS_PAID, Ticket::STATUS_WAITING], ArrayParameterType::INTEGER)
            ->executeQuery();

        $paying = [];
        foreach ($statement->fetchAllAssociative() as $row) {
            $paying[$row['type_inscription']] = $row['c'];
        }

        $queryBuilder = clone $baseQueryBuilder;
        $statement = $queryBuilder->andWhere('etat NOT IN(:states)')
            ->setParameter('states', [Ticket::STATUS_CANCELLED, Ticket::STATUS_ERROR, Ticket::STATUS_DECLINED], ArrayParameterType::INTEGER)
            ->executeQuery();

        $registered = [];
        foreach ($statement->fetchAllAssociative() as $row) {
            $registered[$row['type_inscription']] = $row['c'];
        }

        return new TicketTypeStats($confirmed, $registered, $paying);
    }

    private function getStatsForDay(int $eventId, string $day, ?Datetime $from = null): DailyStats
    {
        Assert::inArray($day, self::DAYS);
        $baseQueryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(*) AS c')
            ->from('afup_inscription_forum', 'aif')
            ->innerJoin('aif', 'afup_forum_tarif', 'aft', 'aif.type_inscription = aft.id')
            ->where('id_forum = :eventId AND FIND_IN_SET(:day, aft.day)')
            ->setParameter('day', $day)
            ->setParameter('eventId', $eventId);

        if ($from instanceof \Datetime) {
            $baseQueryBuilder->andWhere('aif.date > :from')
                ->setParameter('from', $from->getTimestamp());
        }

        $queryBuilder = clone $baseQueryBuilder;
        $registered = $queryBuilder->andWhere('etat NOT IN(:states)')
            ->setParameter('states', [Ticket::STATUS_CANCELLED, Ticket::STATUS_ERROR, Ticket::STATUS_DECLINED], ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchOne();

        $queryBuilder = clone $baseQueryBuilder;
        $confirmed = $queryBuilder->andWhere('etat IN(:states)')
            ->setParameter('states', [Ticket::STATUS_PAID, Ticket::STATUS_GUEST, Ticket::STATUS_CONFIRMED], ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchOne();

        $queryBuilder = clone $baseQueryBuilder;
        $pending = $queryBuilder->andWhere('etat = :state')
            ->setParameter('state', Ticket::STATUS_WAITING)
            ->executeQuery()
            ->fetchOne();

        return new DailyStats($registered, $confirmed, $pending);
    }

    public function getCFPStats(int $eventId): CFPStats
    {
        return new CFPStats(
            $this->talkRepository->getNumberOfTalksByEvent($eventId)['talks'],
            $this->talkToSpeakersRepository->getNumberOfSpeakers($eventId),
        );
    }
}
