<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

final readonly class Meetup
{
    public function __construct(
        public string $urlName,
        public string $id,
    ) {
    }
}
