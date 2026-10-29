<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\ParametrageSponsoringRepository;
use AppBundle\Event\Enum\ModePageSponsoring;
use Doctrine\ORM\Mapping as ORM;

/**
 * Réglages de la page /become-sponsor, stockés sur une ligne unique.
 */
#[ORM\Entity(repositoryClass: ParametrageSponsoringRepository::class)]
#[ORM\Table(name: 'afup_parametrage_sponsoring')]
class ParametrageSponsoring
{
    public const int ID = 1;

    #[ORM\Id]
    #[ORM\Column]
    public int $id = self::ID;

    #[ORM\Column(length: 20, nullable: false, enumType: ModePageSponsoring::class)]
    public ModePageSponsoring $mode = ModePageSponsoring::Generique;

    #[ORM\Column(name: 'texte_fr', type: 'text', nullable: false)]
    public string $texteFr = '';

    #[ORM\Column(name: 'texte_en', type: 'text', nullable: false)]
    public string $texteEn = '';

    public function getTexte(string $language): string
    {
        return $language === 'en' ? $this->texteEn : $this->texteFr;
    }
}
