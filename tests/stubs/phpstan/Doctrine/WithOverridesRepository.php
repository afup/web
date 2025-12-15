<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;

class WithOverridesRepository extends ServiceEntityRepository
{
    public function someMethod(): void
    {
        $this->createQueryBuilder('foo');
        $this->createResultSetMappingBuilder('foo');
        $this->findBy([]);
        $this->findOneBy([]);
        $this->getClassName();
        $this->matching(Criteria::create());
        $this->count();
    }

    public function createQueryBuilder(string $alias, ?string $indexBy = null): QueryBuilder
    {
        return parent::createQueryBuilder($alias, $indexBy);
    }

    public function createResultSetMappingBuilder(string $alias): ResultSetMappingBuilder
    {
        return parent::createResultSetMappingBuilder($alias);
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    public function getClassName(): string
    {
        return parent::getClassName();
    }

    public function matching(Criteria $criteria): AbstractLazyCollection&Selectable
    {
        return parent::matching($criteria);
    }

    public function count(array $criteria = []): int
    {
        return parent::count($criteria);
    }
}
