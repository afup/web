<?php

declare(strict_types=1);

namespace AppBundle\Doctrine;

enum Direction: string
{
    case Desc = 'desc';
    case Asc = 'asc';
}
