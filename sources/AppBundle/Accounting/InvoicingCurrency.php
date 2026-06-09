<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

enum InvoicingCurrency: string
{
    case Euro = 'EUR';
    case Dollar = 'DOL';

    public function symbol(): string
    {
        return match ($this) {
            self::Euro => '€',
            self::Dollar => '$',
        };
    }
}
