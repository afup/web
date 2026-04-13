<?php

declare(strict_types=1);

namespace AppBundle\Site\Entity;

use AppBundle\Doctrine\Type\UnixTimestampType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_site_feuille')]
class Feuille
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(nullable: true)]
    public ?int $idParent = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $lien = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $alt = null;

    #[ORM\Column(nullable: true)]
    public ?int $position = null;

    #[ORM\Column(name: 'date', type: UnixTimestampType::NAME, nullable: true)]
    public ?\DateTime $dateCreation = null;

    #[ORM\Column(type: UnixTimestampType::NAME, nullable: true)]
    public ?\DateTime $dateDebutPublication = null;

    #[ORM\Column(type: UnixTimestampType::NAME, nullable: true)]
    public ?\DateTime $dateFinPublication = null;

    #[ORM\Column(nullable: true)]
    public ?int $etat = null; // todo enum

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $imageAlt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $patterns = null;
}
