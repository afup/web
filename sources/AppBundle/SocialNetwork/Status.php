<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork;

final readonly class Status
{
    public function __construct(
        public string $text,
        public ?Embed $embed = null,
    ) {}
}
