<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan;

use Symfony\Component\Security\Http\Attribute\IsGranted;

// Tableau de bord commun à toute la console : ROLE_ADMIN y est toléré
#[IsGranted('ROLE_ADMIN')]
final class HomeAction
{
    public function __invoke(): void {}
}
