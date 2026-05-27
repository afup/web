<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

final readonly class Socials
{
    public function __construct(
        public ?string $youtube = null,
        public ?string $blog = null,
        public ?string $twitter = null,
        public ?string $linkedin = null,
        public ?string $bluesky = null,
        public ?string $cfp = null,
        public ?string $github = null,
        public ?string $discord = null,
    ) {}
}
