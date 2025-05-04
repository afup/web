<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork;

/**
 * @readonly
 */
final class Status
{
    public function __construct(
        public string $text,
        public ?Embed $embed = null,
    ) {
    }
}
