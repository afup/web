<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork\Bluesky;

/**
 * @readonly
 */
final class Session
{
    public string $did;
    public string $accessJwt;

    public function __construct(string $did, string $accessJwt)
    {
        $this->did = $did;
        $this->accessJwt = $accessJwt;
    }
}
