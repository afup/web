<?php

declare(strict_types=1);

namespace PlanetePHP;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedRepository::class)]
#[ORM\Table(name: 'afup_planete_flux')]
class Feed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(name: 'nom', length: 255, nullable: false)]
    public string $name;

    #[ORM\Column(length: 255, nullable: false)]
    public string $url;

    #[ORM\Column(length: 255, nullable: false)]
    public string $feed;

    #[ORM\Column(name: 'etat', nullable: false, enumType: FeedStatus::class)]
    public FeedStatus $status;

    #[ORM\Column(name: 'id_personne_physique', nullable: true)]
    public ?int $userId = null;
}
