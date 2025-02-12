<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

/**
 * @readonly
 */
final class Map
{
    public City $firstCity;
    public ?City $secondCity;

    public bool $useSecondColor;
    public string $legendAttachment; // todo enum

    public function __construct(
        bool $useSecondColor,
        string $legendAttachment,
        City $firstCity,
        ?City $secondCity = null
    ) {
        $this->firstCity = $firstCity;
        $this->secondCity = $secondCity;
        $this->useSecondColor = $useSecondColor;
        $this->legendAttachment = $legendAttachment;
    }
}
