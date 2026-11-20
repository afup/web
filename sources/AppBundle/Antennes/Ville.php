<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

final readonly class Ville
{
    public function __construct(
        public Point $premierPoint,
        public Point $deuxiemePoint,
        public Point $troisiemePoint,
        public Position $position,
    ) {}
}
