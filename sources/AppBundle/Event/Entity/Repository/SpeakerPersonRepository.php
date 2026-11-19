<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\SpeakerPerson;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<SpeakerPerson>
 */
class SpeakerPersonRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpeakerPerson::class);
    }

    public function slugExists(string $slug): bool
    {
        return $this->count(['slug' => $slug]) > 0;
    }

    /**
     * Déplace les lignes conférencier de $source vers $target puis supprime $source (son slug est perdu).
     */
    public function merge(SpeakerPerson $target, SpeakerPerson $source): void
    {
        $this->getEntityManager()->wrapInTransaction(function () use ($target, $source): void {
            $this->getEntityManager()->getConnection()->executeStatement(
                'UPDATE afup_conferenciers SET person_id = :target WHERE person_id = :source',
                ['target' => $target->id, 'source' => $source->id],
            );
            $this->getEntityManager()->remove($source);
        });
    }
}
