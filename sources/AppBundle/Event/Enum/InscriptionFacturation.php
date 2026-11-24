<?php

declare(strict_types=1);

namespace AppBundle\Event\Enum;

/**
 * État de facturation d'une inscription (colonne afup_inscription_forum.facturation).
 */
enum InscriptionFacturation: int
{
    case AEnvoyer = 0;
    case Envoyee = 1;
    case Recue = 2;

    public function label(): string
    {
        return match ($this) {
            self::AEnvoyer => 'Facture à envoyer',
            self::Envoyee => 'Facture envoyée',
            self::Recue => 'Facture reçue',
        };
    }
}
