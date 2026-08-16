<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity;

use AppBundle\Accounting\Entity\Repository\InvoicingPeriodRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoicingPeriodRepository::class)]
#[ORM\Table(name: 'compta_periode')]
class InvoicingPeriod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(name: 'date_debut', nullable: true)]
    public ?\DateTime $dateDebut = null;

    #[ORM\Column(name: 'date_fin', nullable: true)]
    public ?\DateTime $dateFin = null;

    #[ORM\Column(name: 'verouiller', nullable: true)]
    public ?bool $verrouille = false;
}
