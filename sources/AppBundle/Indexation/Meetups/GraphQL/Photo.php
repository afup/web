<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

final readonly class Photo
{
    public function __construct(public string $standardUrl) {}
}
