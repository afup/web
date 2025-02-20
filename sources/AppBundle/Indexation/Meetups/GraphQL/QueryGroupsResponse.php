<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

/**
 * @readonly
 */
final class QueryGroupsResponse
{
    /** @var array<string, Group> */
    public array $data;

    /**
     * @param array<string, Group> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
