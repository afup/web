<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\SponsorScanRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SponsorScanRepository::class)]
#[ORM\Table(name: 'afup_forum_sponsor_scan')]
class SponsorScan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(name: 'sponsor_ticket_id', nullable: false)]
    public int $sponsorTicketId;

    #[ORM\Column(name: 'ticket_id', nullable: false)]
    public int $ticketId;

    #[ORM\Column(name: 'created_on', type: 'datetime_immutable', nullable: false)]
    public DateTimeImmutable $createdOn;

    #[ORM\Column(name: 'deleted_on', type: 'datetime_immutable', nullable: true)]
    public ?DateTimeImmutable $deletedOn = null;
}
