<?php

declare(strict_types=1);

namespace AppBundle\GeneralMeeting;

use AppBundle\Association\Model\User;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class GeneralMeetingRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return DateTimeInterface[]
     */
    public function getAllDates(): array
    {
        $query = $this->connection->executeQuery(<<<'SQL'
SELECT DISTINCT apag.date
FROM afup_presences_assemblee_generale apag
ORDER BY apag.date DESC
SQL
        );

        return array_map(static fn (array $row) => new DateTimeImmutable('@' . $row['date']), $query->fetchAll());
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getLatestDate()
    {
        $query = $this->connection->executeQuery('SELECT MAX(date) maxDate FROM afup_presences_assemblee_generale LIMIT 1');
        $row = $query->fetch();

        return null !== $row['maxDate'] ? new DateTimeImmutable('@' . $row['maxDate']) : null;
    }

    public function hasGeneralMeetingPlanned(DateTimeInterface $currentDate = null): bool
    {
        if (!$currentDate instanceof \DateTimeInterface) {
            $currentDate = new DateTime();
        }
        $latestDate = $this->getLatestDate();

        return null !== $latestDate && $latestDate->getTimestamp() > strtotime('-1 day', $currentDate->getTimestamp());
    }

    /**
     * @param string $login
     */
    public function findOneByLoginAndDate($login, DateTimeInterface $date): ?GeneralMeeting
    {
        $query = $this->connection->prepare(<<<'SQL'
SELECT apag.*
FROM afup_presences_assemblee_generale apag
JOIN afup_personnes_physiques app ON apag.id_personne_physique = app.id
WHERE app.login = :login
AND apag.date = :date
LIMIT 1
SQL
        );
        $query->bindValue('login', $login);
        $query->bindValue('date', $date->getTimestamp());
        $query->execute();
        $row = $query->fetch();

        return is_array($row) ? new GeneralMeeting(
            (int) $row['id'],
            (int) $row['id_personne_physique'],
            new DateTimeImmutable('@' . $row['date']),
            (int) $row['presence'],
            (int) $row['id_personne_avec_pouvoir'],
            $row['date_consultation'] ? new \DateTimeImmutable('@' . $row['date_consultation']) : null,
            $row['date_modification'] ? new \DateTimeImmutable('@' . $row['date_modification']) : null
        ) : null;
    }

    /**
     * @throws DBALException
     */
    public function findOneByDate(DateTimeInterface $date): ?array
    {
        $query = $this->connection->prepare(<<<SQL
SELECT * FROM afup_assemblee_generale
WHERE afup_assemblee_generale.date = :date
LIMIT 1
SQL
        );

        $query->bindValue('date', $date->getTimestamp());
        $query->execute();
        $row = $query->fetch();

        return is_array($row) ? [
            'date' => new \DateTimeImmutable('@' . $row['date']),
            'description' => $row['description']
        ] : null;
    }

    public function countAttendeesAndPowers(DateTimeInterface $date): int
    {
        $query = $this->connection->prepare(<<<'SQL'
SELECT COUNT(*) c
FROM afup_presences_assemblee_generale apag
WHERE apag.date = :date
AND (apag.presence = '1' OR apag.id_personne_avec_pouvoir > 0) 
SQL
        );
        $query->bindValue('date', $date->getTimestamp());
        $query->execute();

        return (int) $query->fetch()['c'];
    }

    public function countAttendees(DateTimeInterface $date): int
    {
        $query = $this->connection->prepare(<<<'SQL'
SELECT COUNT(*) c
FROM afup_presences_assemblee_generale apag
WHERE apag.date = :date
AND apag.presence = '1' 
SQL
        );
        $query->bindValue('date', $date->getTimestamp());
        $query->execute();

        return (int) $query->fetch()['c'];
    }

    public function obtenirDescription(DateTimeInterface $date)
    {
        $query = $this->connection->prepare(<<<'SQL'
SELECT description
FROM afup_assemblee_generale aag
WHERE aag.date = :date
SQL
        );
        $query->bindValue('date', $date->getTimestamp());
        $query->execute();
        $row = $query->fetch();

        return is_array($row) ? $row['description'] : null;
    }

    /**
     * @param string   $order
     * @param int|null $idPersonneAvecPouvoir
     *
     * @return Attendee[]
     */
    public function getAttendees(DateTimeInterface $date, $order = 'nom', $direction = 'asc', $idPersonneAvecPouvoir = null): array
    {
        $query = $this->connection->createQueryBuilder()
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
                'app2.prenom AS power_firstname'
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

        return array_map(static fn (array $row): Attendee => new Attendee(
            (int) $row['id'],
            $row['email'],
            $row['login'],
            $row['nom'],
            $row['prenom'],
            $row['nearest_office'],
            $row['date_consultation'] ? new \DateTimeImmutable('@' . $row['date_consultation']) : null,
            (int) $row['presence'],
            $row['power_id'] ? (int) $row['power_id'] : null,
            $row['power_lastname'],
            $row['power_firstname']
        ), $query->execute()->fetchAll());
    }

    /**
     * @param string|null $excludeLogin
     *
     * @return array<int, string>
     */
    public function getPowerSelectionList(DateTimeInterface $date, $excludeLogin): array
    {
        $query = $this->connection->createQueryBuilder()
            ->from('afup_personnes_physiques', 'app')
            ->join('app', 'afup_presences_assemblee_generale', 'apag', 'app.id = apag.id_personne_physique')
            ->select('app.id', 'app.nom', 'app.prenom')
            ->where('apag.date = :date')
            ->andWhere('apag.presence = \'1\'')
            ->groupBy('app.id')
            ->orderBy('app.nom')
            ->orderBy('app.prenom')
            ->setParameter('date', $date->getTimestamp());

        if (null !== $excludeLogin) {
            $query->andWhere('app.login <> :login')
                ->setParameter('login', $excludeLogin);
        }

        $list = [];
        foreach ($query->execute()->fetchAll() as $row) {
            $list[$row['id']] = $row['nom'] . ' ' . $row['prenom'];
        }

        return $list;
    }

    public function getValidAttendeeIds(DateTimeInterface $date): array
    {
        // On autorise un battement de 14 jours
        $timestamp = $date->getTimestamp() - 14 * 86400;
        $query = $this->connection->prepare(<<<'SQL'
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
        $query->bindValue('date', $timestamp);
        $query->execute();

        return array_map(static fn (array $row): int => (int) $row['id'], $query->fetchAll());
    }

    /**
     * @param string $description
     *
     * @return bool
     */
    public function prepare(DateTimeInterface $date, $description)
    {
        $query = $this->connection->executeQuery('SELECT id FROM afup_personnes_physiques WHERE etat = 1');
        $success = 0;
        $insertQuery = $this->connection->prepare('INSERT INTO  afup_presences_assemblee_generale (id_personne_physique, date)
            VALUES (:id, :date)');
        $insertQuery->bindValue('date', $date->getTimestamp());
        foreach ($query->fetchAll() as $row) {
            $query = $this->connection->prepare('SELECT id FROM afup_presences_assemblee_generale 
                WHERE id_personne_physique = :id AND date = :date');
            $query->bindValue('id', $row['id']);
            $query->bindValue('date', $date->getTimestamp());
            $query->execute();
            $preparation = $query->fetch();
            if (!is_array($preparation)) {
                $insertQuery->bindValue('id', $row['id']);
                if ($insertQuery->execute()) {
                    $success++;
                }
            }
        }
        if (0 === $success) {
            return false;
        }

        $query = $this->connection->prepare('REPLACE INTO afup_assemblee_generale (`date`, `description`)
            VALUES (:date, :description)');
        $query->bindValue('date', $date->getTimestamp());
        $query->bindValue('description', $description);

        return $query->execute();
    }

    /**
     * @param string $description
     * @return bool
     * @throws DBALException
     */
    public function save(DateTimeInterface $date, $description)
    {
        $query = $this->connection->prepare('UPDATE afup_assemblee_generale SET `description` = :description
            WHERE `date` = :date');
        $query->bindValue('date', $date->getTimestamp());
        $query->bindValue('description', $description);

        return $query->execute();
    }

    /**
     * @param int $personId
     * @param int $presence
     * @param int $powerId
     *
     * @return bool
     */
    public function addAttendee($personId, DateTimeInterface $date, $presence, $powerId)
    {
        $query = $this->connection->prepare(<<<'SQL'
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

        return $query->execute();
    }

    /**
     * @param string $login
     * @param int    $presence
     * @param int    $powerId
     *
     * @return bool
     */
    public function editAttendee($login, DateTimeInterface $date, $presence, $powerId)
    {
        $query = $this->connection->prepare(<<<'SQL'
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

        return $query->execute();
    }

    /**
     * @param string $login
     */
    public function getAttendee($login, DateTimeInterface $date): ?Attendee
    {
        $query = $this->connection->prepare(<<<'SQL'
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
        $query->execute();
        $row = $query->fetch();

        return is_array($row) ? new Attendee(
            (int) $row['id'],
            $row['email'],
            $row['login'],
            $row['nom'],
            $row['prenom'],
            $row['nearest_office'],
            $row['date_consultation'] ? new \DateTimeImmutable('@' . $row['date_consultation']) : null,
            (int) $row['presence'],
            $row['power_id'] ? (int) $row['power_id'] : null,
            $row['power_lastname'],
            $row['power_firstname']
        ) : null;
    }

    /**
     * @param int $nombrePersonnesAJourDeCotisation
     */
    public function obtenirEcartQuorum(DateTimeInterface $date, $nombrePersonnesAJourDeCotisation): int
    {
        $quorum = (int) ceil($nombrePersonnesAJourDeCotisation / 4);

        return $this->countAttendeesAndPowers($date) - $quorum;
    }

    public function hasUserRspvedToLastGeneralMeeting(User $user): bool
    {
        $generalMeeting = null;
        $latestDate = $this->getLatestDate();
        if (null !== $latestDate) {
            $generalMeeting = $this->findOneByLoginAndDate($user->getUsername(), $latestDate);
        }

        return $generalMeeting instanceof GeneralMeeting && $generalMeeting->getModificationDate() instanceof \DateTimeInterface;
    }
}
