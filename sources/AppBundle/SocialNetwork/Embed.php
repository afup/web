<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork;

final readonly class Embed
{
    public function __construct(
        public string $url,
        public string $title,
        public string $abstract,
        public ?string $imageUrl,
    ) {}
}
