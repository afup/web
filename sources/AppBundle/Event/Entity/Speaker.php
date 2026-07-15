<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\SpeakerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpeakerRepository::class)]
#[ORM\Table(name: 'afup_conferenciers')]
class Speaker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'conferencier_id')]
    public int $id;

    #[ORM\Column(name: 'prenom', nullable: false)]
    public string $firstname;

    #[ORM\Column(name: 'nom', nullable: false)]
    public string $lastname;

    public string $label {
        get => $this->firstname . " " . ($this->lastname ? mb_strtoupper($this->lastname) : null);
    }
}
