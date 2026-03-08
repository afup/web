<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;

final class WithoutOverridesRepository extends ServiceEntityRepository
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
}
