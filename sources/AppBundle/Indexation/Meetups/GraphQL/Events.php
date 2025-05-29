<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

final readonly class Events
{
    /**
     * @param list<Edge> $edges
     */
    public function __construct(public array $edges) {}
}
