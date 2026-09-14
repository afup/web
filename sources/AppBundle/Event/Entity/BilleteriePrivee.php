<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\BilleteriePriveeRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BilleteriePriveeRepository::class)]
#[ORM\Table(name: 'afup_forum_billeterie_privee')]
class BilleteriePrivee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(name: 'id_forum', nullable: false)]
    public int $eventId;

    #[ORM\Column(name: 'nom', nullable: false)]
    public string $nom;

    #[ORM\Column(nullable: false)]
    public string $token;

    #[ORM\Column(name: 'mot_de_passe', type: 'string', nullable: false)]
    public string $motDePasse;

    #[ORM\Column(name: 'id_tarif', nullable: false)]
    public int $ticketTypeId;

    #[ORM\Column(name: 'prix', nullable: false)]
    public float $prix;

    #[ORM\Column(name: 'max_places', nullable: false)]
    public int $maxPlaces;

    #[ORM\Column(name: 'date_debut', type: 'datetime_immutable', nullable: false)]
    public DateTimeImmutable $dateDebut;

    #[ORM\Column(name: 'date_fin', type: 'datetime_immutable', nullable: false)]
    public DateTimeImmutable $dateFin;

    #[ORM\Column(name: 'created_on', type: 'datetime_immutable', nullable: false)]
    public DateTimeImmutable $createdOn;

    public function getPlacesRestantes(int $placesPrises): int
    {
        return max(0, $this->maxPlaces - $placesPrises);
    }
}
