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
 * Seul `code` (clé primaire, référencée ailleurs) est immuable ; le reste est modifiable via `modifier()`.
 */
#[ORM\Entity(repositoryClass: AntenneRepository::class)]
#[ORM\Table(name: 'afup_antenne')]
final class Antenne
{
    public ?Meetup $meetup {
        get => $this->meetupNomUrl !== null && $this->meetupIdExterne !== null
            ? new Meetup($this->meetupNomUrl, $this->meetupIdExterne)
            : null;
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
        public private(set) string $libelle,
        ?Meetup $meetup,
        #[ORM\Column(length: 255)]
        public private(set) string $urlLogo,
        #[ORM\Column(type: AntenneReseauxSociauxType::NAME)]
        public private(set) ReseauxSociaux $reseauxSociaux,
        #[ORM\Column(type: AntenneCarteType::NAME, nullable: true)]
        public private(set) ?Carte $carte,
        #[ORM\Column(type: Types::JSON, nullable: true)]
        public private(set) ?array $departements = null,
        #[ORM\Column(type: Types::JSON, nullable: true)]
        public private(set) ?array $pays = null,
        #[ORM\Column(length: 255, nullable: true)]
        public private(set) ?string $region = null,
        #[ORM\Column(options: ['default' => false])]
        public private(set) bool $masqueePageAntennes = false,
    ) {
        $this->meetupNomUrl = $meetup?->nomUrl;
        $this->meetupIdExterne = $meetup?->id;
    }

    /**
     * @param string[]|null $departements
     * @param string[]|null $pays
     */
    public function modifier(
        string $libelle,
        ?Meetup $meetup,
        string $urlLogo,
        ReseauxSociaux $reseauxSociaux,
        ?Carte $carte,
        ?array $departements,
        ?array $pays,
        ?string $region,
        bool $masqueePageAntennes,
    ): void {
        $this->libelle = $libelle;
        $this->meetupNomUrl = $meetup?->nomUrl;
        $this->meetupIdExterne = $meetup?->id;
        $this->urlLogo = $urlLogo;
        $this->reseauxSociaux = $reseauxSociaux;
        $this->carte = $carte;
        $this->departements = $departements;
        $this->pays = $pays;
        $this->region = $region;
        $this->masqueePageAntennes = $masqueePageAntennes;
    }
}
