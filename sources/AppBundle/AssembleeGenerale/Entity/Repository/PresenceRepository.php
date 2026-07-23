<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Entity\Repository;

use AppBundle\AssembleeGenerale\Dto\Attendee;
use AppBundle\AssembleeGenerale\Entity\Presence;
use AppBundle\Association\Model\User;
use AppBundle\Doctrine\EntityRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Presence>
 */
class PresenceRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presence::class);
    }

    /**
     * @return Presence[]
     */
    public function getByUser(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.utilisateur = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @return DateTimeInterface[]
     */
    public function getAllDates(): array
    {
        $result = $this->getEntityManager()->getConnection()->executeQuery(<<<'SQL'
SELECT DISTINCT apag.date
FROM afup_presences_assemblee_generale apag
ORDER BY apag.date DESC
SQL
        );

        return array_map(
            static fn(array $row): DateTimeImmutable => new DateTimeImmutable('@' . $row['date']),
            $result->fetchAllAssociative(),
        );
    }

    public function getLatestAttendanceDate(): ?DateTimeImmutable
    {
        $maxDate = $this->getEntityManager()->getConnection()
            ->executeQuery('SELECT MAX(date) maxDate FROM afup_presences_assemblee_generale LIMIT 1')
            ->fetchOne();

        return null !== $maxDate ? new DateTimeImmutable('@' . $maxDate) : null;
    }

    public function countAttendeesAndPowers(DateTimeInterface $date): int
    {
        $query = $this->getEntityManager()->getConnection()->prepare(<<<'SQL'
SELECT COUNT(*) c
FROM afup_presences_assemblee_generale apag
WHERE apag.date = :date
AND (apag.presence = '1' OR apag.id_personne_avec_pouvoir > 0)
SQL
        );
        $query->bindValue('date', $date->getTimestamp());

        return (int) $query->executeQuery()->fetchOne();
    }

    public function countAttendees(DateTimeInterface $date): int
    {
        $query = $this->getEntityManager()->getConnection()->prepare(<<<'SQL'
SELECT COUNT(*) c
FROM afup_presences_assemblee_generale apag
WHERE apag.date = :date
AND apag.presence = '1'
SQL
        );
        $query->bindValue('date', $date->getTimestamp());

        return (int) $query->executeQuery()->fetchOne();
    }

    /**
     * @return Attendee[]
     */
    public function getAttendees(DateTimeInterface $date, string $order = 'nom', string $direction = 'asc', ?int $idPersonneAvecPouvoir = null): array
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->from('afup_personnes_physiques', 'app')
            ->select(
                'app.id',
                'app.email',
                'app.login',
                'app.nom',
                'app.prenom',
                'app.nearest_office',
                'apag.date_consultation',
                'apag.presence',
                'app2.id AS power_id',
                'app2.nom AS power_lastname',
                'app2.prenom AS power_firstname',
            )
            ->join('app', 'afup_presences_assemblee_generale', 'apag', 'app.id = apag.id_personne_physique')
            ->leftJoin('app', 'afup_personnes_physiques', 'app2', 'app2.id = apag.id_personne_avec_pouvoir')
            ->where('apag.date = :date')
            ->orderBy($order, $direction)
            ->setParameter('date', $date->getTimestamp());

        if (null !== $idPersonneAvecPouvoir) {
            $query->andWhere('id_personne_avec_pouvoir = :pouvoir')
                ->setParameter('pouvoir', $idPersonneAvecPouvoir);
        }

        return array_map(
            static fn(array $row): Attendee => new Attendee(
                (int) $row['id'],
                $row['email'],
                $row['login'],
                $row['nom'],
                $row['prenom'],
                $row['nearest_office'],
                $row['date_consultation'] ? new DateTimeImmutable('@' . $row['date_consultation']) : null,
                (int) $row['presence'],
                $row['power_id'] ? (int) $row['power_id'] : null,
                $row['power_lastname'],
                $row['power_firstname'],
            ),
            $query->executeQuery()->fetchAllAssociative(),
        );
    }

    public function getAttendee(string $login, DateTimeInterface $date): ?Attendee
    {
        $query = $this->getEntityManager()->getConnection()->prepare(<<<'SQL'
SELECT
    app.id,
    app.email,
    app.login,
    app.nom,
    app.prenom,
    app.nearest_office,
    apag.date_consultation,
    apag.presence,
    app2.id as power_id,
    app2.nom as power_lastname,
    app2.prenom as power_firstname
FROM afup_personnes_physiques app
JOIN afup_presences_assemblee_generale apag ON app.id = apag.id_personne_physique
LEFT JOIN afup_personnes_physiques app2 ON app2.id = apag.id_personne_avec_pouvoir
WHERE app.login = :login AND apag.date = :date
LIMIT 1
SQL
        );
        $query->bindValue('login', $login);
        $query->bindValue('date', $date->getTimestamp());

        $row = $query->executeQuery()->fetchAssociative();

        return is_array($row) ? new Attendee(
            (int) $row['id'],
            $row['email'],
            $row['login'],
            $row['nom'],
            $row['prenom'],
            $row['nearest_office'],
            $row['date_consultation'] ? new DateTimeImmutable('@' . $row['date_consultation']) : null,
            (int) $row['presence'],
            $row['power_id'] ? (int) $row['power_id'] : null,
            $row['power_lastname'],
            $row['power_firstname'],
        ) : null;
    }

    public function addAttendee(int $personId, DateTimeInterface $date, int $presence, int $powerId): bool
    {
        $query = $this->getEntityManager()->getConnection()->prepare(<<<'SQL'
INSERT INTO afup_presences_assemblee_generale
    (id_personne_physique, `date`, presence, id_personne_avec_pouvoir, date_modification)
VALUES (:personId, :date, :presence, :powerId, :modificationDate)
SQL
        );
        $query->bindValue('personId', $personId);
        $query->bindValue('date', $date->getTimestamp());
        $query->bindValue('presence', $presence);
        $query->bindValue('powerId', $powerId);
        $query->bindValue('modificationDate', (new DateTimeImmutable())->getTimestamp());

        return $query->executeStatement() > 0;
    }

    public function editAttendee(string $login, DateTimeInterface $date, int $presence, int $powerId): bool
    {
        $query = $this->getEntityManager()->getConnection()->prepare(<<<'SQL'
UPDATE afup_presences_assemblee_generale apag, afup_personnes_physiques app
SET apag.presence = :presence,
    apag.id_personne_avec_pouvoir = :powerId,
    apag.date_modification = :modificationDate
WHERE apag.id_personne_physique = app.id
    AND (app.login = :login OR app.email = :login)
    AND apag.date = :date
SQL
        );
        $query->bindValue('login', $login);
        $query->bindValue('date', $date->getTimestamp());
        $query->bindValue('presence', $presence);
        $query->bindValue('powerId', $powerId);
        $query->bindValue('modificationDate', (new DateTimeImmutable())->getTimestamp());

        return $query->executeStatement() > 0;
    }

    public function obtenirEcartQuorum(DateTimeInterface $date, int $nombrePersonnesAJourDeCotisation): int
    {
        return $this->countAttendeesAndPowers($date) - (int) ceil($nombrePersonnesAJourDeCotisation / 4);
    }

    public function getValidAttendeeIds(DateTimeInterface $date): array
    {
        $query = $this->getEntityManager()->getConnection()->prepare(<<<'SQL'
SELECT app.id
FROM afup_cotisations ac
INNER JOIN afup_personnes_physiques app ON app.id = ac.id_personne
WHERE date_fin >= :date
AND type_personne = 0
AND etat = 1
UNION SELECT app.id
FROM afup_cotisations ac
INNER JOIN afup_personnes_physiques app ON app.id_personne_morale = ac.id_personne
WHERE date_fin >= :date
AND type_personne = 1
AND etat = 1
SQL
        );
        $query->bindValue('date', $date->getTimestamp() - 14 * 86400);

        return array_map(static fn(array $row): int => (int) $row['id'], $query->executeQuery()->fetchAllAssociative());
    }

    public function hasUserRspvedToLastGeneralMeeting(User $user): bool
    {
        $latestDate = $this->getLatestAttendanceDate();
        if (null === $latestDate) {
            return false;
        }

        $query = $this->getEntityManager()->getConnection()->prepare(<<<'SQL'
SELECT apag.date_modification
FROM afup_presences_assemblee_generale apag
JOIN afup_personnes_physiques app ON apag.id_personne_physique = app.id
WHERE app.login = :login AND apag.date = :date
LIMIT 1
SQL
        );
        $query->bindValue('login', $user->getUsername());
        $query->bindValue('date', $latestDate->getTimestamp());

        $row = $query->executeQuery()->fetchAssociative();

        return is_array($row) && null !== $row['date_modification'];
    }

    /**
     * @return array<int, string>
     */
    public function getPowerSelectionList(DateTimeInterface $date, ?string $excludeLogin): array
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->from('afup_personnes_physiques', 'app')
            ->join('app', 'afup_presences_assemblee_generale', 'apag', 'app.id = apag.id_personne_physique')
            ->select('app.id', 'app.nom', 'app.prenom')
            ->where('apag.date = :date')
            ->andWhere("apag.presence = '1'")
            ->groupBy('app.id')
            ->orderBy('app.nom')
            ->addOrderBy('app.prenom')
            ->setParameter('date', $date->getTimestamp());

        if (null !== $excludeLogin) {
            $query->andWhere('app.login <> :login')
                ->setParameter('login', $excludeLogin);
        }

        $list = [];
        foreach ($query->executeQuery()->fetchAllAssociative() as $row) {
            $list[$row['id']] = $row['nom'] . ' ' . $row['prenom'];
        }

        return $list;
    }
}
