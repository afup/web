<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

/**
 * @readonly
 */
final class Venue
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
