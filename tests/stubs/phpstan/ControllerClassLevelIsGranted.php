<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan;

use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_MEMBRES_READER')]
final class ControllerClassLevelIsGrantedStub
{
    public function __invoke(): void {}
}
