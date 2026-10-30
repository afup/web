<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan;

use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class ControllerWithRoleAdmin
{
    public function __invoke(): void {}
}
