<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\SponsorshipSettingsRepository;
use AppBundle\Event\Enum\BecomeSponsorMode;
use Doctrine\ORM\Mapping as ORM;

/**
 * Réglages de la page /become-sponsor, stockés sur une ligne unique.
 */
#[ORM\Entity(repositoryClass: SponsorshipSettingsRepository::class)]
#[ORM\Table(name: 'afup_sponsorship_settings')]
class SponsorshipSettings
{
    public const int ID = 1;

    #[ORM\Id]
    #[ORM\Column]
    public int $id = self::ID;

    #[ORM\Column(length: 20, nullable: false, enumType: BecomeSponsorMode::class)]
    public BecomeSponsorMode $mode = BecomeSponsorMode::Generic;

    #[ORM\Column(name: 'text_fr', type: 'text', nullable: false)]
    public string $textFr = '';

    #[ORM\Column(name: 'text_en', type: 'text', nullable: false)]
    public string $textEn = '';

    public function getText(string $language): string
    {
        return $language === 'en' ? $this->textEn : $this->textFr;
    }
}
