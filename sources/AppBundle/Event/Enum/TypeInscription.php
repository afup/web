<?php

declare(strict_types=1);

namespace AppBundle\Event\Enum;

/**
 * Types de tarif (afup_forum_tarif.id) ayant un traitement particulier.
 * La colonne afup_inscription_forum.type_inscription accepte n'importe quel tarif : elle reste un entier.
 */
enum TypeInscription: int
{
    case Organisation = 9;
    case Sponsor = 10;
    case Presse = 11;
    case Conferencier = 12;
    case PrixSpecial = 108;
}
