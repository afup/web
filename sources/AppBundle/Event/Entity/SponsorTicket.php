<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\SponsorTicketRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SponsorTicketRepository::class)]
#[ORM\Table(name: 'afup_forum_sponsors_tickets')]
class SponsorTicket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(name: 'company', nullable: false)]
    public string $societe;

    #[ORM\Column(length: 64, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 30, max: 64)]
    public string $token;

    #[ORM\Column(name: 'contact_email', nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $contactEmail;

    #[ORM\Column(name: 'max_invitations', nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 20)]
    public int $maxInvitations = 0;

    #[ORM\Column(name: 'used_invitations', nullable: false)]
    public int $usedInvitations = 0;

    #[ORM\Column(name: 'id_forum', nullable: false)]
    public int $eventId;

    #[ORM\Column(name: 'created_on', type: 'datetime_immutable', nullable: false)]
    public DateTimeImmutable $createdOn;

    #[ORM\Column(name: 'edited_on', type: 'datetime_immutable', nullable: false)]
    public DateTimeImmutable $editedOn;

    #[ORM\Column(name: 'creator_id', nullable: false)]
    public int $creatorId;

    #[ORM\Column(name: 'qr_codes_scanner_available', nullable: false)]
    public bool $qrCodesScannerAvailable = false;

    /**
     * Nombre d'invitations restantes pour ce sponsor.
     */
    public function getPendingInvitations(): int
    {
        return $this->maxInvitations - $this->usedInvitations;
    }
}
