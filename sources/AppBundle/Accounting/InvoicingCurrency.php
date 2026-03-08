<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

enum InvoicingCurrency: string
{
    case Euro = 'EUR';
    case Dollar = 'DOL';
}
