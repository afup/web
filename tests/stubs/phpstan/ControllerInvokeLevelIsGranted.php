<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan;

use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ControllerInvokeLevelIsGranted
{
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function __invoke(): void {}
}
