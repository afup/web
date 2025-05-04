<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork\Bluesky;

/**
 * @readonly
 */
final class Session
{
    public function __construct(
        public string $did,
        public string $accessJwt,
    ) {
    }
}
