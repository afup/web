<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'compta_produit')]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    public string $reference;

    #[ORM\Column(length: 255, nullable: false)]
    public string $designation;

    #[ORM\Column(nullable: true)]
    public ?int $quantite = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    public float $prixUnitaireHt;

    #[ORM\Column(nullable: true)]
    public ?float $tauxTva = null;
}
