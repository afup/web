<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\TicketSpecialPriceRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketSpecialPriceRepository::class)]
#[ORM\Table(name: 'afup_forum_special_price')]
class TicketSpecialPrice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(name: 'id_event', nullable: false)]
    public int $eventId;

    #[ORM\Column(nullable: false)]
    public string $token;

    #[ORM\Column(nullable: true)]
    public ?float $price = null;

    #[ORM\Column(name: 'date_start', type: 'datetime_immutable', nullable: false)]
    public DateTimeImmutable $dateStart;

    #[ORM\Column(name: 'date_end', type: 'datetime_immutable', nullable: false)]
    public DateTimeImmutable $dateEnd;

    #[ORM\Column(nullable: false)]
    public string $description;

    #[ORM\Column(name: 'created_on', type: 'datetime_immutable', nullable: false)]
    public DateTimeImmutable $createdOn;

    #[ORM\Column(name: 'creator_id', nullable: false)]
    public int $creatorId;
}
