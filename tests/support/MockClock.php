<?php

declare(strict_types=1);

namespace Afup\Tests\Support;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final class MockClock implements ClockInterface
{
    private \DateTimeImmutable $now;

    public function __construct(DateTimeImmutable $now)
    {
        $this->now = $now;
    }

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }
}
