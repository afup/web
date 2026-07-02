<?php

declare(strict_types=1);

namespace AppBundle\Veille\Entity;

use AppBundle\Veille\Entity\Repository\EnvoiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnvoiRepository::class)]
#[ORM\Table(name: 'afup_techletter')]
class Envoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(name: 'sending_date', type: 'datetime')]
    public \DateTimeInterface $dateEnvoi;

    public function __construct()
    {
        $this->dateEnvoi = new \DateTime();
    }

    #[ORM\Column(name: 'sent_to_mailchimp', type: 'boolean')]
    public bool $envoyeMailchimp = false;

    #[ORM\Column(name: 'techletter', type: 'text', nullable: true)]
    public ?string $contenu = null;

    #[ORM\Column(name: 'archive_url', nullable: true)]
    public ?string $urlArchive = null;
}
