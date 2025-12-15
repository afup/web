<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan\Doctrine;

use Doctrine\Common\Collections\Criteria;

final class SubClassRepository extends WithOverridesRepository
{
    public function someOtherMethod(): void
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
