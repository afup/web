<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity;

use AppBundle\Doctrine\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'compta_reglement')]
class Payment extends Entity
{
    #[ORM\Column(name: 'reglement', length: 50, nullable: false)]
    public string $name;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    public ?\DateTimeImmutable $hideInAccountingJournalAt = null;
}
