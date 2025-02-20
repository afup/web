<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

/**
 * @readonly
 */
final class Edge
{
    public Node $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }
}
