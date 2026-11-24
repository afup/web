<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\TicketEventTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketEventTypeRepository::class)]
#[ORM\Table(name: 'afup_forum_tarif_event')]
class TicketEventType
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_tarif', nullable: false)]
    public int $ticketTypeId;

    #[ORM\Id]
    #[ORM\Column(name: 'id_event', nullable: false)]
    public int $eventId;

    #[ORM\Column(nullable: true)]
    public ?float $price = null;

    #[ORM\Column(name: 'date_start')]
    public \DateTime $dateStart;

    #[ORM\Column(name: 'date_end')]
    public \DateTime $dateEnd;

    #[ORM\Column(nullable: true)]
    public ?string $description = null;

    #[ORM\Column(name: 'max_tickets', nullable: true)]
    public ?int $maxTickets = null;

    // Type de tarif associé : champ non mappé, renseigné par TicketTypeRepository::attachTo()
    public ?TicketType $ticketType = null;
}
