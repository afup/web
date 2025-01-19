<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

/**
 * @readonly
 */
final class Position
{
    public float $latitude;
    public float $longitude;

    public function __construct(float $latitude, float $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
}
