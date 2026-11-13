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
 * La classe n'est plus `readonly` dans son ensemble : `meetup` est reconstruit à partir des colonnes
 * `meetup_nom_url` / `meetup_id_externe` au chargement (callback PostLoad), Doctrine ne sachant pas
 * hydrater un embeddable nullable. Cette propriété est donc en `private(set)` ; toutes les autres
 * restent `readonly`.
 */
#[ORM\Entity(repositoryClass: AntenneRepository::class)]
#[ORM\Table(name: 'afup_antenne')]
#[ORM\HasLifecycleCallbacks]
final class Antenne
{
    public private(set) ?Meetup $meetup;

    #[ORM\Column(length: 255, nullable: true)]
    private readonly ?string $meetupNomUrl;

    #[ORM\Column(length: 50, nullable: true)]
    private readonly ?string $meetupIdExterne;

    /**
     * @param string[]|null $departements
     * @param string[]|null $pays
     */
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(length: 50)]
        public readonly string $code,
        #[ORM\Column(length: 255)]
        public readonly string $libelle,
        ?Meetup $meetup,
        #[ORM\Column(length: 255)]
        public readonly string $urlLogo,
        #[ORM\Column(type: AntenneReseauxSociauxType::NAME)]
        public readonly ReseauxSociaux $reseauxSociaux,
        #[ORM\Column(type: AntenneCarteType::NAME, nullable: true)]
        public readonly ?Carte $carte,
        #[ORM\Column(type: Types::JSON, nullable: true)]
        public readonly ?array $departements = null,
        #[ORM\Column(type: Types::JSON, nullable: true)]
        public readonly ?array $pays = null,
        #[ORM\Column(length: 255, nullable: true)]
        public readonly ?string $region = null,
        #[ORM\Column(options: ['default' => false])]
        public readonly bool $masqueePageAntennes = false,
    ) {
        $this->meetup = $meetup;
        $this->meetupNomUrl = $meetup?->nomUrl;
        $this->meetupIdExterne = $meetup?->id;
    }

    #[ORM\PostLoad]
    public function initialiserMeetup(): void
    {
        $this->meetup = $this->meetupNomUrl !== null && $this->meetupIdExterne !== null
            ? new Meetup($this->meetupNomUrl, $this->meetupIdExterne)
            : null;
    }
}
