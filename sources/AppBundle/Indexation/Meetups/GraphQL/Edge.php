<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

/**
 * @readonly
 */
final class Edge
{
    public function __construct(public Node $node)
    {
    }
}
