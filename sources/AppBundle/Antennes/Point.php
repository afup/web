<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

final readonly class Point
{
    public function __construct(
        public int $x,
        public int $y,
    ) {
    }
}
