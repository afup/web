<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

/**
 * @readonly
 */
final class Meetup
{
    public string $urlName;
    public string $id;

    public function __construct(string $urlName, string $id)
    {
        $this->urlName = $urlName;
        $this->id = $id;
    }
}
