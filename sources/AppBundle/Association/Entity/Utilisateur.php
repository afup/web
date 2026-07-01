<?php

declare(strict_types=1);

namespace AppBundle\Association\Entity;

use AppBundle\Doctrine\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_personnes_physiques')]
class Utilisateur extends Entity
{
    #[ORM\Column(length: 255, nullable: true)]
    public ?string $email = null;
}
