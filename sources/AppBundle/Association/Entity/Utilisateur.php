<?php

declare(strict_types=1);

namespace AppBundle\Association\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_personnes_physiques')]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $email = null;

    #[ORM\Column(name: 'prenom', nullable: false)]
    public string $firstname;

    #[ORM\Column(name: 'nom', nullable: false)]
    public string $lastname;
}
