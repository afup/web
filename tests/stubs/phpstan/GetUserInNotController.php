<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan;

abstract class SomeParentClass
{
    protected function getUser(): void {}
}

final class GetUserInNotController extends SomeParentClass
{
    public function __invoke(): void
    {
        $this->getUser();
    }
}
