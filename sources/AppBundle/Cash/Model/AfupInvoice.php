<?php

declare(strict_types=1);

namespace AppBundle\Cash\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class AfupInvoice implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?int $id = null;
}
