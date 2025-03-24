<?php

declare(strict_types=1);

namespace AppBundle\Clock;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

/**
 * À remplacer par `symfony/clock` dès que possible.
 *
 * La version la plus ancienne a besoin de PHP 8.1 minimum :
 *
 * https://packagist.org/packages/symfony/clock#v6.2.0
 */
final class NativeClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
