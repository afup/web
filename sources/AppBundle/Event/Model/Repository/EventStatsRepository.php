<?php

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\EventStats;
use AppBundle\Event\Model\EventStats\DailyStats;
use Assert\Assertion;
use Datetime;
use Doctrine\DBAL\Connection;

class EventStatsRepository
{
    const DAY_ONE = 'one';
    const DAY_TWO = 'two';
    const DAYS = [self::DAY_ONE, self::DAY_TWO];
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $eventId
     *
     * @return EventStats
     */
    public function getStats($eventId, Datetime $from = null)
    {
        $stats = new EventStats();
        $stats->firstDay = $this->getStatsForDay($eventId, self::DAY_ONE, $from);
        $stats->secondDay = $this->getStatsForDay($eventId, self::DAY_TWO, $from);
        // Nombre de personnes validées par type d'inscription
        $baseQueryBuilder = $this->connection->createQueryBuilder()
            ->select('type_inscription', 'COUNT(*) AS c')
            ->from('afup_inscription_forum', 'aif')
            ->where('id_forum = :eventId')
            ->groupBy('type_inscription')
            ->setParameter('eventId', $eventId);
        if (null !== $from) {
            $baseQueryBuilder->andWhere('aif.date > :from')
                ->setParameter('from', $from->getTimestamp());
        }
        $queryBuilder = clone $baseQueryBuilder;
        $statement = $queryBuilder->andWhere('etat IN(:states)')
            ->setParameter('states', [AFUP_FORUM_ETAT_REGLE, AFUP_FORUM_ETAT_ATTENTE_REGLEMENT, AFUP_FORUM_ETAT_INVITE], Connection::PARAM_INT_ARRAY)
            ->execute();
        foreach ($statement->fetchAll() as $row) {
            $stats->ticketType->confirmed[$row['type_inscription']] = $row['c'];
        }
        $queryBuilder = clone $baseQueryBuilder;
        $statement = $queryBuilder->andWhere('etat IN(:states)')
            ->setParameter('states', [AFUP_FORUM_ETAT_REGLE, AFUP_FORUM_ETAT_ATTENTE_REGLEMENT], Connection::PARAM_INT_ARRAY)
            ->execute();
        foreach ($statement->fetchAll() as $row) {
            $stats->ticketType->paying[$row['type_inscription']] = $row['c'];
        }
        $queryBuilder = clone $baseQueryBuilder;
        $statement = $queryBuilder->andWhere('etat NOT IN(:states)')
            ->setParameter('states', [AFUP_FORUM_ETAT_ANNULE, AFUP_FORUM_ETAT_ERREUR, AFUP_FORUM_ETAT_REFUSE], Connection::PARAM_INT_ARRAY)
            ->execute();
        foreach ($statement->fetchAll() as $row) {
            $stats->ticketType->registered[$row['type_inscription']] = $row['c'];
        }

        return $stats;
    }

    /**
     * @param int           $eventId
     * @param string        $day
     * @param Datetime|null $from
     *
     * @return DailyStats
     */
    private function getStatsForDay($eventId, $day, Datetime $from = null)
    {
        Assertion::inArray($day, self::DAYS);
        $baseQueryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(*) AS c')
            ->from('afup_inscription_forum', 'aif')
            ->innerJoin('aif', 'afup_forum_tarif', 'aft', 'aif.type_inscription = aft.id')
            ->where('id_forum = :eventId AND FIND_IN_SET(:day, aft.day)')
            ->setParameter('day', $day)
            ->setParameter('eventId', $eventId);
        if (null !== $from) {
            $baseQueryBuilder->andWhere('aif.date > :from')
                ->setParameter('from', $from->getTimestamp());
        }
        $dailyStats = new DailyStats();
        $queryBuilder = clone $baseQueryBuilder;
        $dailyStats->registered = $queryBuilder->andWhere('etat NOT IN(:states)')
            ->setParameter('states', [AFUP_FORUM_ETAT_ANNULE, AFUP_FORUM_ETAT_ERREUR, AFUP_FORUM_ETAT_REFUSE], Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetch()['c'];
        $queryBuilder = clone $baseQueryBuilder;
        $dailyStats->confirmed = $queryBuilder->andWhere('etat IN(:states)')
            ->setParameter('states', [AFUP_FORUM_ETAT_REGLE, AFUP_FORUM_ETAT_INVITE, AFUP_FORUM_ETAT_CONFIRME], Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetch()['c'];
        $queryBuilder = clone $baseQueryBuilder;
        $dailyStats->pending = $queryBuilder->andWhere('etat = :state')
            ->setParameter('state', AFUP_FORUM_ETAT_ATTENTE_REGLEMENT)
            ->execute()
            ->fetch()['c'];

        return $dailyStats;
    }
}
