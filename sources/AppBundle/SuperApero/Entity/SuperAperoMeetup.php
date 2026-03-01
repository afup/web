<?php

declare(strict_types=1);

namespace AppBundle\SuperApero\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'super_apero_meetup')]
class SuperAperoMeetup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: SuperApero::class, inversedBy: 'meetups')]
    #[ORM\JoinColumn(nullable: false)]
    public SuperApero $superApero;

    #[ORM\Column(length: 255, nullable: false)]
    public string $antenne;

    #[ORM\Column(nullable: true)]
    public ?int $meetupId = null;

    #[ORM\Column(nullable: true)]
    public ?string $description = null;
}
