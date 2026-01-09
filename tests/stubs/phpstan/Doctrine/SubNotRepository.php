<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan\Doctrine;

final class SubNotRepository extends NotRepository
{
    public function someOtherMethod(): void
    {
        $this->createQueryBuilder();
        $this->createResultSetMappingBuilder();
        $this->findBy();
        $this->findOneBy();
        $this->getClassName();
        $this->matching();
        $this->count();
    }
}
