<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

enum OperationType: int
{
    case Debit = 1;
    case Credit = 2;
}
