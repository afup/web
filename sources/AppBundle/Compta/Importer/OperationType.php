<?php

declare(strict_types=1);

namespace AppBundle\Compta\Importer;

enum OperationType
{
    case Debit;
    case Credit;
}
