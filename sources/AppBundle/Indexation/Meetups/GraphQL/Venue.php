<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

/**
 * @readonly
 */
final class Venue
{
    public function __construct(public string $name)
    {
    }
}
