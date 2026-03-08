<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan\Doctrine;

use Doctrine\Common\Collections\Criteria;

final readonly class SomeExampleClass
{
    public function __construct(
        private WithoutOverridesRepository $withoutOverridesRepository,
        private WithOverridesRepository $withOverridesRepository,
        private NotRepository $notRepository,
        private WithOverridesRepository|WithoutOverridesRepository $withAndWithoutOverridesRepository,
    ) {}

    public function disallowedCalls(): void
    {
        $this->withoutOverridesRepository->createQueryBuilder('foo');
        $this->withoutOverridesRepository->createResultSetMappingBuilder('foo');
        $this->withoutOverridesRepository->findBy([]);
        $this->withoutOverridesRepository->findOneBy([]);
        $this->withoutOverridesRepository->getClassName();
        $this->withoutOverridesRepository->matching(Criteria::create());
        $this->withoutOverridesRepository->count();

        $this->withAndWithoutOverridesRepository->createQueryBuilder('foo');
        $this->withAndWithoutOverridesRepository->createResultSetMappingBuilder('foo');
        $this->withAndWithoutOverridesRepository->findBy([]);
        $this->withAndWithoutOverridesRepository->findOneBy([]);
        $this->withAndWithoutOverridesRepository->getClassName();
        $this->withAndWithoutOverridesRepository->matching(Criteria::create());
        $this->withAndWithoutOverridesRepository->count();
    }

    public function allowedCalls(): void
    {
        $this->withOverridesRepository->createQueryBuilder('foo');
        $this->withOverridesRepository->createResultSetMappingBuilder('foo');
        $this->withOverridesRepository->findBy([]);
        $this->withOverridesRepository->findOneBy([]);
        $this->withOverridesRepository->getClassName();
        $this->withOverridesRepository->matching(Criteria::create());
        $this->withOverridesRepository->count();

        $this->notRepository->createQueryBuilder();
        $this->notRepository->createResultSetMappingBuilder();
        $this->notRepository->findBy();
        $this->notRepository->findOneBy();
        $this->notRepository->getClassName();
        $this->notRepository->matching();
        $this->notRepository->count();
    }
}
