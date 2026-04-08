<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

enum InvoicingPaymentStatus: int
{
    case Waiting = 0;
    case Payed = 1;
    case Cancelled = 2;

    public function label(): string
    {
        return match ($this) {
            self::Waiting => 'En attente de paiement',
            self::Payed => 'Payé',
            self::Cancelled => 'Annulé',
        };
    }
}
