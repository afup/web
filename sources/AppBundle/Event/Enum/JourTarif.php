<?php

declare(strict_types=1);

namespace AppBundle\Event\Enum;

/**
 * Jour couvert par un tarif (valeurs du SET afup_forum_tarif.day).
 */
enum JourTarif: string
{
    case Un = 'one';
    case Deux = 'two';
}
