<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\Speaker;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Speaker>
 */
class SpeakerRepository extends EntityRepository
{
    private const array SORTS = [
        'name' => 's.lastname',
        'company' => 's.company',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Speaker::class);
    }

    /**
     * @return list<Speaker>
     */
    public function getSpeakersByTalk(int $talkId): array
    {
        return $this->findIndexedByTalkIds([$talkId])[$talkId] ?? [];
    }

    /**
     * Les conférenciers de chacune des sessions demandées, indexés par id de session.
     *
     * @param list<int> $talkIds
     *
     * @return array<int, list<Speaker>>
     */
    public function findIndexedByTalkIds(array $talkIds): array
    {
        if ($talkIds === []) {
            return [];
        }

        $rows = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('cs.session_id', 'cs.conferencier_id')
            ->from('afup_conferenciers_sessions', 'cs')
            ->where('cs.session_id IN (:talkIds)')
            ->orderBy('cs.session_id', 'ASC')
            ->addOrderBy('cs.conferencier_id', 'ASC')
            ->setParameter('talkIds', array_values(array_unique($talkIds)), ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchAllAssociative();

        if ($rows === []) {
            return [];
        }

        $speakers = [];
        foreach ($this->findBy(['id' => array_values(array_unique(array_map(static fn(array $row): int => self::toInt($row['conferencier_id']), $rows)))]) as $speaker) {
            $speakers[$speaker->id] = $speaker;
        }

        $speakersByTalk = [];
        foreach ($rows as $row) {
            $speaker = $speakers[self::toInt($row['conferencier_id'])] ?? null;
            if ($speaker !== null) {
                $speakersByTalk[self::toInt($row['session_id'])][] = $speaker;
            }
        }

        return $speakersByTalk;
    }

    /**
     * Les conférenciers ayant au moins une session planifiée (et publiée, sauf si $includeUnpublished)
     * pour l'événement, triés par prénom puis nom, avec les ids de ces sessions.
     *
     * @return array<int, list<int>> ids de session, par id de conférencier
     */
    public function getScheduledSpeakerTalkIds(int $eventId, bool $includeUnpublished = false): array
    {
        $queryBuilder = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('c.conferencier_id', 'talk.session_id')
            ->from('afup_conferenciers', 'c')
            ->innerJoin('c', 'afup_conferenciers_sessions', 'cs', 'cs.conferencier_id = c.conferencier_id')
            ->innerJoin('cs', 'afup_sessions', 'talk', 'talk.session_id = cs.session_id')
            ->where('c.id_forum = :eventId')
            ->andWhere('talk.plannifie = 1')
            ->orderBy('c.prenom', 'ASC')
            ->addOrderBy('c.nom', 'ASC')
            ->addOrderBy('c.conferencier_id', 'ASC')
            ->addOrderBy('talk.session_id', 'ASC')
            ->setParameter('eventId', $eventId);

        if (!$includeUnpublished) {
            $queryBuilder->andWhere('talk.date_publication < NOW() OR talk.date_publication IS NULL');
        }

        $talkIds = [];
        foreach ($queryBuilder->executeQuery()->iterateAssociative() as $row) {
            $talkIds[self::toInt($row['conferencier_id'])][] = self::toInt($row['session_id']);
        }

        return $talkIds;
    }

    /**
     * Les conférenciers ayant au moins une session planifiée (et publiée, sauf si $includeUnpublished)
     * pour l'événement, triés par prénom puis nom.
     *
     * @return list<Speaker>
     */
    public function getScheduledSpeakers(int $eventId, bool $includeUnpublished = false): array
    {
        return array_values($this->findIndexedByIds(array_keys($this->getScheduledSpeakerTalkIds($eventId, $includeUnpublished))));
    }

    /**
     * @param list<int> $ids
     *
     * @return array<int, Speaker> dans l'ordre des ids demandés
     */
    public function findIndexedByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $found = [];
        foreach ($this->findBy(['id' => array_values(array_unique($ids))]) as $speaker) {
            $found[$speaker->id] = $speaker;
        }

        $speakers = [];
        foreach ($ids as $id) {
            if (array_key_exists($id, $found)) {
                $speakers[$id] = $found[$id];
            }
        }

        return $speakers;
    }

    /**
     * Les conférenciers ayant une session au planning de l'événement.
     *
     * @return list<Speaker>
     */
    public function getSpeakersByEvent(int $eventId): array
    {
        $speakerIds = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('DISTINCT cs.conferencier_id')
            ->from('afup_conferenciers_sessions', 'cs')
            ->innerJoin('cs', 'afup_sessions', 'talk', 'talk.session_id = cs.session_id')
            ->innerJoin('talk', 'afup_forum_planning', 'planning', 'planning.id_session = talk.session_id')
            ->where('talk.id_forum = :eventId')
            ->setParameter('eventId', $eventId)
            ->executeQuery()
            ->fetchFirstColumn();

        if ($speakerIds === []) {
            return [];
        }

        return array_values($this->findBy(['id' => array_map(self::toInt(...), $speakerIds)], ['id' => 'ASC']));
    }

    /**
     * La fiche de ce compte GitHub sur l'édition la plus récente, hors événement donné.
     */
    public function getFromLastEventAndUserId(int $eventId, int $githubUserId): ?Speaker
    {
        $speakerId = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('c.conferencier_id')
            ->from('afup_conferenciers', 'c')
            ->innerJoin('c', 'afup_forum', 'f', 'f.id = c.id_forum')
            ->where('c.id_forum != :eventId')
            ->andWhere('c.user_github = :githubUserId')
            ->orderBy('f.date_debut', 'DESC')
            ->addOrderBy('c.conferencier_id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('eventId', $eventId)
            ->setParameter('githubUserId', $githubUserId)
            ->executeQuery()
            ->fetchOne();

        return $speakerId !== false ? $this->find(self::toInt($speakerId)) : null;
    }

    /**
     * Retourne `true` si le speaker avec l'email ($email) a soumis au moins 1 CFP pour l'évènement passé en paramètre.
     */
    public function hasCFPSubmitted(int $eventId, string $email): bool
    {
        $count = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('COUNT(c.conferencier_id)')
            ->from('afup_conferenciers', 'c')
            ->innerJoin('c', 'afup_conferenciers_sessions', 'cs', 'cs.conferencier_id = c.conferencier_id')
            ->innerJoin('cs', 'afup_sessions', 'talk', 'talk.session_id = cs.session_id')
            ->where('talk.id_forum = :eventId')
            ->andWhere('c.email = :email')
            ->setParameter('eventId', $eventId)
            ->setParameter('email', $email)
            ->executeQuery()
            ->fetchOne();

        return self::toInt($count) > 0;
    }

    /**
     * @return list<Speaker>
     */
    public function searchSpeakers(int $eventId, string $sort = 'name', string $direction = 'asc', ?string $filter = null): array
    {
        if (!array_key_exists($sort, self::SORTS)) {
            throw new \InvalidArgumentException(sprintf('Tri "%s" inconnu', $sort));
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            throw new \InvalidArgumentException(sprintf('Direction de tri "%s" inconnue', $direction));
        }

        $queryBuilder = $this->createQueryBuilder('s')
            ->where('s.eventId = :eventId')
            ->orderBy(self::SORTS[$sort], $direction)
            ->addOrderBy('s.id', 'ASC')
            ->setParameter('eventId', $eventId);

        if ($filter !== null && $filter !== '') {
            $queryBuilder
                ->andWhere("CONCAT(COALESCE(s.lastname, ''), COALESCE(s.firstname, ''), COALESCE(s.company, '')) LIKE :filter")
                ->setParameter('filter', '%' . $filter . '%');
        }

        /** @var list<Speaker> */
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Nombre de conférenciers distincts (par nom et prénom) de l'événement.
     */
    public function countByEvent(int $eventId): int
    {
        $count = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('COUNT(DISTINCT CONCAT(COALESCE(nom, \'\'), \'|\', COALESCE(prenom, \'\')))')
            ->from('afup_conferenciers')
            ->where('id_forum = :eventId')
            ->setParameter('eventId', $eventId)
            ->executeQuery()
            ->fetchOne();

        return self::toInt($count);
    }

    private static function toInt(mixed $value): int
    {
        if (!is_numeric($value)) {
            throw new \UnexpectedValueException('Le comptage doit renvoyer une valeur numérique');
        }

        return (int) $value;
    }
}
