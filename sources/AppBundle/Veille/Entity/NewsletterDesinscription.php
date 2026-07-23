<?php

declare(strict_types=1);

namespace AppBundle\Veille\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_techletter_unsubscriptions')]
class NewsletterDesinscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $email = null;

    #[ORM\Column(name: 'unsubscription_date', type: 'datetime', nullable: true)]
    public ?\DateTime $dateDesinscription = null;

    #[ORM\Column(name: 'reason', length: 255, nullable: true)]
    public ?string $raison = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $mailchimpId = null;
}
