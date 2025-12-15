<?php

declare(strict_types=1);

namespace AppBundle\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @template T of object
 * @template-extends ServiceEntityRepository<T>
 */
abstract class EntityRepository extends ServiceEntityRepository
{
    /**
     * @param T $entity
     */
    final public function save(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param T $entity
     */
    final public function delete(object $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }
}
