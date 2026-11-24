<?php

declare(strict_types=1);

namespace AppBundle\Association\Enum;

enum PersonneMoraleEtat: int
{
    case EnAttente = -1;
    case Inactive = 0;
    case Active = 1;
}
