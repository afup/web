<?php

declare(strict_types=1);

namespace AppBundle\Antennes\Entity\Repository;

use AppBundle\Antennes\Entity\Antenne;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Les antennes sont stockées dans la table afup_antenne (voir la migration 20261113100000_create_antenne_table).
 * L'API publique est restée identique à celle de l'ancien repository « codé en dur ».
 *
 * @extends EntityRepository<Antenne>
 */
final class AntenneRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Antenne::class);
    }

    /**
     * @return array<string, Antenne>
     */
    public function getAll(): array
    {
        $antennes = [];
        foreach ($this->findBy([], ['code' => 'ASC']) as $antenne) {
            $antennes[$antenne->code] = $antenne;
        }

        return $antennes;
    }

    /**
     * @return array<string, Antenne>
     */
    public function getAllSortedByLabels(): array
    {
        $antennes = array_filter(
            $this->getAll(),
            fn(Antenne $antenne): bool => !$antenne->masqueePageAntennes,
        );

        uasort(
            $antennes,
            fn(Antenne $a, Antenne $b): int => strcmp($a->libelle, $b->libelle),
        );

        return $antennes;
    }

    public function findByCode(string $code): Antenne
    {
        $antenne = $this->find($code);

        if (!$antenne instanceof Antenne) {
            throw new \InvalidArgumentException("Antenne introuvable via le code $code");
        }

        return $antenne;
    }

    /**
     * @return array<string, string>
     */
    public function getOrderedLabelsByKey(): array
    {
        $labels = [];
        foreach ($this->getAllSortedByLabels() as $antenne) {
            $labels[$antenne->code] = $antenne->libelle;
        }

        return $labels;
    }
}
