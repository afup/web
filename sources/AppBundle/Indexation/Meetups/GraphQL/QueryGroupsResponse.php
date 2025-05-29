<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

final readonly class QueryGroupsResponse
{
    /**
     * @param array<string, Group> $data
     */
    public function __construct(public array $data) {}
}
