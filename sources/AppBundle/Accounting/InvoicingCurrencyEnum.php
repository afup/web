<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

enum InvoicingCurrencyEnum: string
{
    case EURO = 'EUR';
    case DOLLAR = 'DOL';
}
