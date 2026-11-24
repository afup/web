<?php

declare(strict_types=1);

namespace AppBundle\Event\Enum;

/**
 * État d'une inscription (colonne afup_inscription_forum.etat).
 * Les mêmes valeurs servent pour l'état des factures d'évènement (afup_facturation_forum.etat).
 */
enum InscriptionEtat: int
{
    case Creee = 0;
    case Annulee = 1;
    case ErreurPaiement = 2;
    case PaiementRefuse = 3;
    case Reglee = 4;
    case Invitation = 5;
    case AttenteReglement = 6;
    case Confirmee = 7;
    case APosteriori = 8;

    public function label(): string
    {
        return match ($this) {
            self::Creee => 'Inscription créée',
            self::Annulee => 'Inscription annulée',
            self::ErreurPaiement => 'Paiement CB erreur',
            self::PaiementRefuse => 'Paiement CB refusé',
            self::Reglee => 'Inscription réglée',
            self::Invitation => 'Invitation',
            self::AttenteReglement => 'Attente règlement',
            self::Confirmee => 'Inscription confirmée',
            self::APosteriori => 'Inscription à posteriori',
        };
    }
}
