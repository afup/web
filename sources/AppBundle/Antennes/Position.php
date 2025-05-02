<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

final readonly class Position
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
    }
}
