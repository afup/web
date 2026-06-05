<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\EventStats\CFPStats;
use AppBundle\Event\Model\EventStats\SalesPilotage;
use AppBundle\Event\Model\EventStats\TicketTypeStats;
use AppBundle\Event\Model\EventStats;
use AppBundle\Event\Model\EventStats\DailyStats;
use AppBundle\Event\Model\Ticket;
use Datetime;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Webmozart\Assert\Assert;
use DateTimeImmutable;

class EventStatsRepository
{
    private const DAY_ONE = 'one';
    private const DAY_TWO = 'two';
    private const DAYS = [self::DAY_ONE, self::DAY_TWO];

    public function __construct(
        private readonly Connection $connection,
        private readonly TalkRepository $talkRepository,
        private readonly TalkToSpeakersRepository $talkToSpeakersRepository,
        private readonly EventRepository $eventRepository,
    ) {}

    public function getStats(int $eventId, ?Datetime $from = null): EventStats
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

        $queryBuilder = clone $baseQueryBuilder;
        $paid = $queryBuilder->andWhere('etat = :state')
            ->setParameter('state', Ticket::STATUS_PAID)
            ->executeQuery()
            ->fetchOne();

        return new DailyStats($registered, $confirmed, $pending, $paid);
    }

    public function getRegistrationTracking(int $eventId, ?int $previousEventId = null): array
    {
        if ($previousEventId === null) {
            $previousEventId = $this->eventRepository->getPreviousForum($eventId) ?? 0;
        }

        $event = $this->eventRepository->get($eventId);

        $now = new \DateTime();
        $dateForum = DateTimeImmutable::createFromInterface($event->getDateEndSales());

        $daysToEndOfSales = 0;
        if ($dateForum >= $now) {
            $daysToEndOfSales = (int) $dateForum->diff($now)->format('%r%a');
        }

        $sql = "
        SELECT SUM(nombre) as nombre, jour, id_forum
        FROM (
            SELECT
              COUNT(*) as nombre,
              DATEDIFF(FROM_UNIXTIME(date, '%Y-%m-%d'), FROM_UNIXTIME(af.date_fin_vente, '%Y-%m-%d')) as jour,
              id_forum
            FROM
              afup_inscription_forum i
            RIGHT JOIN afup_forum_tarif aft ON (aft.id = i.type_inscription AND aft.default_price > 0)
            LEFT JOIN afup_forum af ON af.id = i.id_forum
            WHERE
              i.id_forum IN (:eventId, :previousEventId)
            AND
              etat <> 1
            GROUP BY jour, i.id_forum
            HAVING jour < 0
            UNION ALL
            SELECT
                SUM(max_invitations) as nombre,
                DATEDIFF(created_on, FROM_UNIXTIME(af.date_fin_vente, '%Y-%m-%d')) as jour,
                id_forum
            FROM afup_forum_sponsors_tickets st
            LEFT JOIN afup_forum af ON af.id = st.id_forum
            WHERE
              st.id_forum IN (:eventId, :previousEventId)
            GROUP BY jour, st.id_forum
            HAVING jour < 0
            ORDER BY jour ASC
        ) all_data
        GROUP BY jour, id_forum
        ";

        $nombreParDate = $this->connection->executeQuery(
            $sql,
            ['eventId' => $eventId, 'previousEventId' => $previousEventId],
        )->fetchAllAssociative();

        if ($nombreParDate === []) {
            $nombreParDate = [['jour' => 1]];
        }

        $suivis = [];
        for ($i = $nombreParDate[0]['jour']; $i <= 0; $i++) {
            $infoForum = array_sum(array_map(function (array $info) use ($i, $eventId) {
                if ((int) $info['id_forum'] === $eventId && $info['jour'] <= $i) {
                    return $info['nombre'];
                }
                return 0;
            }, $nombreParDate));
            $infoN1 = array_sum(array_map(function (array $info) use ($i, $previousEventId) {
                if ((int) $info['id_forum'] === $previousEventId && $info['jour'] <= $i) {
                    return $info['nombre'];
                }
                return 0;
            }, $nombreParDate));
            $suivis[$i] = [
                'jour' => $i,
                'n' => $daysToEndOfSales >= $i ? $infoForum : null,
                'n_1' => $infoN1,
            ];
        }

        return [
            'suivi' => $suivis,
            'min' => $nombreParDate[0]['jour'],
            'max' => $i,
            'daysToEndOfSales' => $daysToEndOfSales,
        ];
    }

    /**
     * Instrument de pilotage des ventes : billets payants vendus, jours de vente
     * restants, taux de remplissage et comparaison avec l'édition N-1 prise au
     * même stade de son cycle de vente.
     */
    public function getSalesPilotage(Event $event): SalesPilotage
    {
        $eventId = (int) $event->getId();

        $now = new DateTimeImmutable();
        $endOfSales = DateTimeImmutable::createFromInterface($event->getDateEndSales());
        $daysToEndOfSales = $endOfSales >= $now ? (int) $now->diff($endOfSales)->format('%a') : 0;

        $paidTickets = $this->countPaidTickets($eventId);

        $previousAtSameStage = null;
        $previousTotal = null;
        $previousTitle = null;

        $previousEvent = $this->eventRepository->getLastYearEvent($event);
        if ((int) $previousEvent->getId() !== $eventId) {
            $previousEventId = (int) $previousEvent->getId();
            $previousTitle = $previousEvent->getTitle();
            // On compte les billets de l'édition N-1 vendus alors qu'il lui restait
            // au moins autant de jours de vente qu'aujourd'hui (même stade du cycle).
            $previousAtSameStage = $this->countPaidTickets($previousEventId, -$daysToEndOfSales);
            $previousTotal = $this->countPaidTickets($previousEventId);
        }

        return new SalesPilotage(
            $paidTickets,
            $event->getSeats(),
            $daysToEndOfSales,
            $previousAtSameStage,
            $previousTotal,
            $previousTitle,
        );
    }

    /**
     * Nombre de billets payés (etat = PAID) sur un type de tarif payant
     * (default_price > 0). Si $maxDayOffset est fourni, seuls les billets vendus
     * alors qu'il restait au moins ce nombre de jours avant la fin des ventes sont
     * comptés (offset négatif, relatif à la date de fin de vente de l'événement).
     */
    private function countPaidTickets(int $eventId, ?int $maxDayOffset = null): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('afup_inscription_forum', 'i')
            ->innerJoin('i', 'afup_forum_tarif', 'aft', 'aft.id = i.type_inscription AND aft.default_price > 0')
            ->where('i.id_forum = :eventId')
            ->andWhere('i.etat = :paid')
            ->setParameter('eventId', $eventId)
            ->setParameter('paid', Ticket::STATUS_PAID);

        if ($maxDayOffset !== null) {
            $queryBuilder
                ->innerJoin('i', 'afup_forum', 'af', 'af.id = i.id_forum')
                ->andWhere("DATEDIFF(FROM_UNIXTIME(i.date), FROM_UNIXTIME(af.date_fin_vente)) <= :maxDayOffset")
                ->setParameter('maxDayOffset', $maxDayOffset);
        }

        return (int) $queryBuilder->executeQuery()->fetchOne();
    }

    public function getCFPStats(int $eventId): CFPStats
    {
        return new CFPStats(
            $this->talkRepository->getNumberOfTalksByEvent($eventId)['talks'],
            $this->talkToSpeakersRepository->getNumberOfSpeakers($eventId),
        );
    }
}
