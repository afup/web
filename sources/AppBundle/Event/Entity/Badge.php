<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\BadgeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BadgeRepository::class)]
#[ORM\Table(name: 'afup_badge')]
class Badge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    public string $label;

    #[ORM\Column(length: 255, nullable: false)]
    public string $url;
}
