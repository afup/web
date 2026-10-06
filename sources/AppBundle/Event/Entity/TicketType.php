<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\TicketTypeRepository;
use AppBundle\Event\Model\Ticket;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketTypeRepository::class)]
#[ORM\Table(name: 'afup_forum_tarif')]
class TicketType
{
    /**
     * Nom technique du tarif utilisé pour les prix sanctuarisés (tokens spéciaux et billetteries privées).
     */
    public const string SPECIAL_PRICE_TECHNICAL_NAME = 'SPECIAL_PRICE';

    /**
     * Noms techniques des tarifs « early bird ».
     */
    public const array EARLY_BIRD_TECHNICAL_NAME = ['EARLY_BIRD', 'EARLY_BIRD_AFUP', 'AFUP_DAY_EARLY'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(name: 'technical_name', length: 64, nullable: false)]
    public string $technicalName;

    #[ORM\Column(name: 'pretty_name', length: 255, nullable: false)]
    public string $prettyName;

    #[ORM\Column(name: 'public', nullable: false)]
    public bool $isPublic;

    #[ORM\Column(name: 'members_only', nullable: false)]
    public bool $isRestrictedToMembers;

    #[ORM\Column(name: 'cfp_submitter_only', nullable: false)]
    public bool $isRestrictedToCfpSubmitter;

    #[ORM\Column(name: 'default_price', nullable: false)]
    public float $defaultPrice;

    #[ORM\Column(name: 'active', nullable: false)]
    public bool $isActive;

    #[ORM\Column(nullable: false)]
    public string $day;

    public function getLabel(): string
    {
        return sprintf('%s - %s%s - %s',
            $this->technicalName,
            $this->prettyName,
            $this->isRestrictedToMembers ? ' - (réservé aux membres)' : '',
            $this->getPrettyDays(),
        );
    }

    public function isEarly(): bool
    {
        return in_array($this->technicalName, self::EARLY_BIRD_TECHNICAL_NAME);
    }

    /**
     * @return list<string>
     */
    public function getDays(): array
    {
        return explode(',', $this->day);
    }

    public function getPrettyDays(): string
    {
        $days = [];
        foreach ($this->getDays() as $day) {
            switch ($day) {
                case Ticket::DAY_ONE:
                    $days[] = 'JOUR 1';
                    break;
                case Ticket::DAY_TWO:
                    $days[] = 'JOUR 2';
                    break;
                default:
                    break;
            }
        }

        return implode(', ', $days);
    }
}
