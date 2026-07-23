<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Entity\Repository;

use AppBundle\AssembleeGenerale\Entity\AssembleeGenerale;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<AssembleeGenerale>
 */
class AssembleeGeneraleRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssembleeGenerale::class);
    }

    public function getLatestDate(): ?\DateTimeImmutable
    {
        $ts = $this->getEntityManager()->getConnection()->fetchOne(
            'SELECT MAX(date) FROM afup_assemblee_generale',
        );

        return is_numeric($ts) ? new \DateTimeImmutable('@' . $ts) : null;
    }

    public function hasPlanned(?\DateTimeInterface $currentDate = null): bool
    {
        $currentDate ??= new \DateTime();
        $latestDate = $this->getLatestDate();

        return null !== $latestDate
            && $latestDate->getTimestamp() > strtotime('-1 day', $currentDate->getTimestamp());
    }

    public function findOneByDate(\DateTimeInterface $date): ?AssembleeGenerale
    {
        /** @var AssembleeGenerale|null $assemblee */
        $assemblee = $this->createQueryBuilder('ag')
            ->where('ag.date = :date')
            ->setParameter('date', $date->getTimestamp())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $assemblee;
    }

    public function upsert(\DateTimeInterface $date, string $description): void
    {
        $assemblee = $this->findOneByDate($date) ?? new AssembleeGenerale();
        $assemblee->date = $date->getTimestamp();
        $assemblee->description = $description;
        $this->save($assemblee);
    }

    public function prepare(\DateTimeInterface $date, string $description): bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $members = $conn->executeQuery('SELECT id FROM afup_personnes_physiques WHERE etat = 1')->fetchAllAssociative();
        $insertQuery = $conn->prepare('INSERT INTO afup_presences_assemblee_generale (id_personne_physique, date) VALUES (:id, :date)');
        $insertQuery->bindValue('date', $date->getTimestamp());

        $success = 0;
        foreach ($members as $row) {
            $alreadyExists = $conn->prepare('SELECT id FROM afup_presences_assemblee_generale WHERE id_personne_physique = :id AND date = :date');
            $alreadyExists->bindValue('id', $row['id']);
            $alreadyExists->bindValue('date', $date->getTimestamp());

            if (!is_array($alreadyExists->executeQuery()->fetchAssociative())) {
                $insertQuery->bindValue('id', $row['id']);
                if ($insertQuery->executeStatement()) {
                    $success++;
                }
            }
        }

        if (0 === $success) {
            return false;
        }

        $replaceQuery = $conn->prepare('REPLACE INTO afup_assemblee_generale (`date`, `description`) VALUES (:date, :description)');
        $replaceQuery->bindValue('date', $date->getTimestamp());
        $replaceQuery->bindValue('description', $description);

        return $replaceQuery->executeStatement() > 0;
    }
}
