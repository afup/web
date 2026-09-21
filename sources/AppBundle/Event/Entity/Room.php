<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\RoomRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[ORM\Table(name: 'afup_forum_salle')]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'smallint')]
    public ?int $id = null;

    #[ORM\Column(name: 'nom', length: 255, nullable: true)]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    #[ORM\Column(name: 'id_forum', nullable: true)]
    public ?int $eventId = null;
}
