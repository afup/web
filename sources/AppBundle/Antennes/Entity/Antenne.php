<?php

declare(strict_types=1);

namespace AppBundle\Antennes\Entity;

use AppBundle\Antennes\Carte;
use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use AppBundle\Antennes\Meetup;
use AppBundle\Antennes\ReseauxSociaux;
use AppBundle\Doctrine\Type\AntenneCarteType;
use AppBundle\Doctrine\Type\AntenneReseauxSociauxType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * `meetup` est une propriété virtuelle calculée à partir des colonnes `meetup_nom_url` / `meetup_id_externe`.
 * Seul `code` (clé primaire, référencée ailleurs) est immuable.
 */
#[ORM\Entity(repositoryClass: AntenneRepository::class)]
#[ORM\Table(name: 'afup_antenne')]
final class Antenne
{
    public ?Meetup $meetup {
        get => $this->meetupNomUrl !== null && $this->meetupIdExterne !== null
            ? new Meetup($this->meetupNomUrl, $this->meetupIdExterne)
            : null;
        set(?Meetup $value) {
            $this->meetupNomUrl = $value?->nomUrl;
            $this->meetupIdExterne = $value?->id;
        }
    }

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $meetupNomUrl;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $meetupIdExterne;

    /**
     * @param string[]|null $departements
     * @param string[]|null $pays
     */
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(length: 50)]
        public readonly string $code,
        #[ORM\Column(length: 255)]
        public string $libelle,
        ?Meetup $meetup,
        #[ORM\Column(length: 255)]
        public string $urlLogo,
        #[ORM\Column(type: AntenneReseauxSociauxType::NAME)]
        public ReseauxSociaux $reseauxSociaux,
        #[ORM\Column(type: AntenneCarteType::NAME, nullable: true)]
        public ?Carte $carte,
        #[ORM\Column(type: Types::JSON, nullable: true)]
        public ?array $departements = null,
        #[ORM\Column(type: Types::JSON, nullable: true)]
        public ?array $pays = null,
        #[ORM\Column(length: 255, nullable: true)]
        public ?string $region = null,
        #[ORM\Column(options: ['default' => false])]
        public bool $masqueePageAntennes = false,
    ) {
        $this->meetup = $meetup;
    }
}
