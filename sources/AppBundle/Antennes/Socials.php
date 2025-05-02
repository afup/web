<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

final readonly class Socials
{
    public function __construct(
        public ?string $youtube,
        public ?string $blog,
        public ?string $twitter,
        public ?string $linkedin,
        public ?string $bluesky,
    ) {
    }
}
