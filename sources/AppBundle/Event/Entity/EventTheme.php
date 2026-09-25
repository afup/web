<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\EventThemeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventThemeRepository::class)]
#[ORM\Table(name: 'afup_conference_theme')]
class EventTheme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(name: 'id_forum', nullable: false)]
    public int $idForum;

    #[ORM\Column(nullable: false)]
    public string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    public int $priority = 0;
}
