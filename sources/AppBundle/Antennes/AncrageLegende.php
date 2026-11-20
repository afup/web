<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

/**
 * Côté de la ville auquel la légende est rattachée sur la carte des antennes.
 * Les valeurs restent en anglais : elles sont lues telles quelles par le JavaScript de la carte.
 */
enum AncrageLegende: string
{
    case Haut = 'top';
    case Bas = 'bottom';
    case Gauche = 'left';
    case Droite = 'right';
}
