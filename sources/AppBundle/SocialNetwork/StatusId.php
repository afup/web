<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork;

/**
 * @readonly
 */
final class StatusId
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
