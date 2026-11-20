<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\PersonneConferencierRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Personne derrière les lignes afup_conferenciers (une par édition) d'un même conférencier.
 * Le slug ne change plus une fois créé, pour que les URL publiques restent stables.
 */
#[ORM\Entity(repositoryClass: PersonneConferencierRepository::class)]
#[ORM\Table(name: 'afup_conferenciers_personnes')]
class PersonneConferencier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    public int $id;

    #[ORM\Column(name: 'created_at')]
    public \DateTimeImmutable $createdAt;

    public function __construct(
        #[ORM\Column(length: 255, unique: true)]
        public string $slug,
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }
}
