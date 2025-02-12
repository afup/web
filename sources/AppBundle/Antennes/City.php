<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

/**
 * @readonly
 */
final class City
{
    public Point $firstPoint;
    public Point $secondPoint;
    public Point $thirdPoint;
    public Position $position;

    public function __construct(Point $firstPoint, Point $secondPoint, Point $thirdPoint, Position $point)
    {
        $this->firstPoint = $firstPoint;
        $this->secondPoint = $secondPoint;
        $this->thirdPoint = $thirdPoint;
        $this->position = $point;
    }
}
