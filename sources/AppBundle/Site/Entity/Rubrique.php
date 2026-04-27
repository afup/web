<?php

declare(strict_types=1);

namespace AppBundle\Site\Entity;

use AppBundle\Doctrine\Type\UnixTimestampType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_site_rubrique')]
class Rubrique
{
    public const ID_RUBRIQUE_ACTUALITES = 9;
    public const ID_RUBRIQUE_ASSOCIATION = 85;
    public const ID_RUBRIQUE_ANTENNES = 84;
    public const ID_RUBRIQUE_INFORMATIONS_PRATIQUES = 86;
    public const ID_RUBRIQUE_NOS_ACTIONS = 88;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(nullable: true)]
    public ?int $idParent = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $raccourci = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $contenu = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $descriptif = null;

    #[ORM\Column(nullable: true)]
    public ?int $position = null;

    #[ORM\Column(type: UnixTimestampType::NAME, nullable: true)]
    public ?\DateTime $date = null;

    #[ORM\Column(nullable: true)]
    public ?int $etat = null;

    #[ORM\Column(nullable: true)]
    public ?int $idPersonnePhysique = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $icone = null;

    #[ORM\Column(nullable: false)]
    public int $pagination = 0;

    #[ORM\Column(nullable: true)]
    public ?int $feuilleAssociee = null;
}
