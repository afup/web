<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

final readonly class City
{
    public function __construct(
        public Point $firstPoint,
        public Point $secondPoint,
        public Point $thirdPoint,
        public Position $position,
    ) {
    }
}
