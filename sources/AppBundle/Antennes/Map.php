<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

final readonly class Map
{
    public function __construct(
        public bool $useSecondColor,
        public LegendAttachment $legendAttachment,
        public City $firstCity,
        public ?City $secondCity = null,
    ) {
    }
}
