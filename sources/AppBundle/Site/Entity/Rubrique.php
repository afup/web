<?php

declare(strict_types=1);

namespace AppBundle\Site\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_site_rubrique')]
class Rubrique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(name: 'nom', nullable: true)]
    public ?string $name = null;
}
