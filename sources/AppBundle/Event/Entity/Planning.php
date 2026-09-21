<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Doctrine\Type\UnixTimestampType;
use AppBundle\Event\Entity\Repository\PlanningRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlanningRepository::class)]
#[ORM\Table(name: 'afup_forum_planning')]
class Planning
{
    /**
     * Les horaires sont stockés en base sous forme de timestamp, donc absolus.
     * Les événements de l'AFUP se déroulant en France, ils sont saisis et affichés
     * dans cette timezone, quelle que soit celle du serveur ou du navigateur.
     */
    public const string TIMEZONE = 'Europe/Paris';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    #[ORM\Column(name: 'id_session', nullable: true)]
    public ?int $talkId = null;

    #[ORM\Column(name: 'debut', type: UnixTimestampType::NAME, nullable: true)]
    public ?\DateTime $start = null;

    #[ORM\Column(name: 'fin', type: UnixTimestampType::NAME, nullable: true)]
    public ?\DateTime $end = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    #[ORM\Column(name: 'id_forum', nullable: true)]
    public ?int $eventId = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    #[ORM\Column(name: 'id_salle', nullable: true)]
    public ?int $roomId = null;

    #[ORM\Column(name: 'keynote', options: ['default' => 0])]
    public bool $isKeynote = false;
}
