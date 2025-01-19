<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

/**
 * @readonly
 */
final class Point
{
    public int $x;
    public int $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
}
