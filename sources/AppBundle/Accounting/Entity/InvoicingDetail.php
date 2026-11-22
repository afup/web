<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'afup_compta_facture_details')]
class InvoicingDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Invoicing::class, inversedBy: 'details')]
    #[ORM\JoinColumn(name: 'idafup_compta_facture', referencedColumnName: 'id', nullable: false)]
    public Invoicing $facture;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[ORM\Column(name: 'ref', length: 50, nullable: false)]
    public string $reference = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: false)]
    public string $designation = '';

    #[Assert\NotBlank]
    #[ORM\Column(name: 'quantite', type: 'float', nullable: false)]
    public ?float $quantite = null; // @phpstan-ignore doctrine.columnType (un champ vide donne null, rejeté ensuite par NotBlank)

    #[Assert\NotBlank]
    #[ORM\Column(name: 'pu', type: 'float', nullable: false)]
    public ?float $prixUnitaire = null; // @phpstan-ignore doctrine.columnType (un champ vide donne null, rejeté ensuite par NotBlank)

    #[Assert\NotBlank]
    #[ORM\Column(type: 'float', nullable: true)]
    public ?float $tva = null;
}
