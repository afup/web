<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

/**
 * @readonly
 */
final class Events
{
    /** @var list<Edge> */
    public array $edges;

    /**
     * @param list<Edge> $edges
     */
    public function __construct(array $edges)
    {
        $this->edges = $edges;
    }
}
