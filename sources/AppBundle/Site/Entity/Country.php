<?php

declare(strict_types=1);

namespace AppBundle\Site\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_pays')]
class Country
{
    #[ORM\Id]
    #[ORM\Column(length: 255, nullable: false)]
    public string $id;

    #[ORM\Column(name: 'nom', length: 2, nullable: false)]
    public string $name;
}
