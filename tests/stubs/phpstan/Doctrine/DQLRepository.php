<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan\Doctrine;

use AppBundle\Doctrine\EntityRepository;

final class DQLRepository extends EntityRepository
{
    public function someAction(): void
    {
        $this->getEntityManager()->createQuery('');
    }
}
