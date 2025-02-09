<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

use DateTime;

/**
 * @readonly
 */
final class Node
{
    public string $id;
    public string $title;
    public string $description;
    public DateTime $dateTime;
    public ?Venue $venue;

    public function __construct(string $id, string $title, string $description, DateTime $dateTime, ?Venue $venue)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->dateTime = $dateTime;
        $this->venue = $venue;
    }
}
