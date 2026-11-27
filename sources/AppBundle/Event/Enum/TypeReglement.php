<?php

declare(strict_types=1);

namespace AppBundle\Event\Enum;

/**
 * Type de règlement d'une facture d'évènement (colonne afup_facturation_forum.type_reglement).
 */
enum TypeReglement: int
{
    case CarteBancaire = 0;
    case Cheque = 1;
    case Virement = 2;
    case Aucun = 3;

    public function label(): string
    {
        return match ($this) {
            self::CarteBancaire => 'Carte bancaire',
            self::Cheque => 'Chèque',
            self::Virement => 'Virement',
            self::Aucun => 'Aucun',
        };
    }

    /**
     * Mention « Payé … » imprimée sur la facture PDF, null quand aucun règlement n'est à mentionner.
     */
    public function invoiceMention(): ?string
    {
        return match ($this) {
            self::CarteBancaire => 'par CB',
            self::Cheque => 'par chèque',
            self::Virement => 'par virement',
            self::Aucun => null,
        };
    }
}
