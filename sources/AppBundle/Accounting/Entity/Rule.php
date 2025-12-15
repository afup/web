<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'compta_regle')]
class Rule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    public string $label;

    #[ORM\Column(name: '`condition`', length: 255, nullable: false)]
    public string $condition;

    #[ORM\Column(nullable: true)]
    public ?bool $isCredit = null;

    #[ORM\Column(nullable: true)]
    public ?string $vat = null;

    #[ORM\Column(nullable: true)]
    public ?int $categoryId = null;

    #[ORM\Column(nullable: true)]
    public ?int $eventId = null;

    #[ORM\Column(name: 'mode_regl_id', nullable: true)]
    public ?int $paymentTypeId = null;

    #[ORM\Column(nullable: true)]
    public ?bool $attachmentRequired = null;
}
