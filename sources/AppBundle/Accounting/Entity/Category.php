<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity;

use AppBundle\Doctrine\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'compta_categorie')]
class Category extends Entity
{
    #[ORM\Column(name: 'categorie', length: 255, nullable: false)]
    public string $name;

    #[ORM\Column(name: 'idevenement', nullable: true)]
    public ?int $eventId = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    public ?\DateTimeImmutable $hideInAccountingJournalAt = null;
}
