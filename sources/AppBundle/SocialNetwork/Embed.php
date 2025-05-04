<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork;

/**
 * @readonly
 */
final class Embed
{
    public function __construct(
        public string $url,
        public string $title,
        public string $abstract,
        public ?string $imageUrl,
    ) {
    }
}
