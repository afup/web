<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity;

use AppBundle\Doctrine\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'compta_operation')]
class Operation extends Entity
{
    #[ORM\Column(name: 'operation', length: 255, nullable: false)]
    public string $name;
}
