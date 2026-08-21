<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_meetup')]
final class Meetup
{
    #[ORM\Id]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(name: 'date', type: 'datetime_immutable', nullable: false)]
    public \DateTimeImmutable $date;

    #[ORM\Column(name: 'title', length: 255, nullable: true)]
    public ?string $titre = null;

    #[ORM\Column(name: 'location', length: 255, nullable: true)]
    public ?string $lieu = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $description = null;

    #[ORM\Column(name: 'antenne_name', length: 255, nullable: false)]
    public string $codeAntenne;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $photoUrl = null;
}
