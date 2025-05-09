<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork;

/**
 * @readonly
 */
final class StatusId
{
    public function __construct(public string $value)
    {
    }
}
