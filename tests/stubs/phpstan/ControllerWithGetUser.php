<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ControllerWithGetUser extends AbstractController
{
    public function __invoke(): void
    {
        $this->getUser();
    }
}
