<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan\Doctrine;

class NotRepository
{
    public function createQueryBuilder(): void {}
    public function createResultSetMappingBuilder(): void {}
    public function findBy(): void {}
    public function findOneBy(): void {}
    public function getClassName(): void {}
    public function matching(): void {}
    public function count(): void {}

    public function someMethod(): void
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
