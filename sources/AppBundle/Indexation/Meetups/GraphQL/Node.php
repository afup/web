<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

use DateTime;

final readonly class Node
{
    /**
     * @param array<Venue> $venues
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public DateTime $dateTime,
        public array $venues,
    ) {}
}
