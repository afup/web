<?php

declare(strict_types=1);

namespace Afup\Tests\Support;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final readonly class MockClock implements ClockInterface
{
    public function __construct(private \DateTimeImmutable $now)
    {
    }

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }
}
