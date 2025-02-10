<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class TicketType implements NotifyPropertyInterface
{
    use NotifyProperty;

    const SPECIAL_PRICE_TECHNICAL_NAME = 'SPECIAL_PRICE';
    const EARLY_BIRD_TECHNICAL_NAME = ['EARLY_BIRD', 'EARLY_BIRD_AFUP', 'AFUP_DAY_EARLY'];

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $technicalName;

    /**
     * @var string
     */
    private $prettyName;

    /**
     * @var bool
     */
    private $isPublic;

    /**
     * @var bool
     */
    private $isRestrictedToMembers;

    /**
     * @var bool
     */
    private $isRestrictedToCfpSubmitter;

    /**
     * @var float
     */
    private $defaultPrice;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var string
     */
    private $day;

    public function getLabel(): string
    {
        return sprintf('%s - %s%s - %s',
            $this->getTechnicalName(),
            $this->getPrettyName(),
            $this->getIsRestrictedToMembers() ? ' - (réservé aux membres)' : '',
            $this->getPrettyDays()
        );
    }

    public function isEarly(): bool
    {
        return in_array($this->technicalName, self::EARLY_BIRD_TECHNICAL_NAME);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): self
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTechnicalName()
    {
        return $this->technicalName;
    }

    /**
     * @param string $technicalName
     */
    public function setTechnicalName($technicalName): self
    {
        $this->propertyChanged('technicalName', $this->technicalName, $technicalName);
        $this->technicalName = $technicalName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrettyName()
    {
        return $this->prettyName;
    }

    /**
     * @param string $prettyName
     */
    public function setPrettyName($prettyName): self
    {
        $this->propertyChanged('prettyName', $this->prettyName, $prettyName);
        $this->prettyName = $prettyName;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     */
    public function setIsPublic($isPublic): self
    {
        $this->propertyChanged('isPublic', $this->isPublic, $isPublic);
        $this->isPublic = $isPublic;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsRestrictedToMembers()
    {
        return $this->isRestrictedToMembers;
    }

    /**
     * @param bool $isRestrictedToMembers
     */
    public function setIsRestrictedToMembers($isRestrictedToMembers): self
    {
        $this->propertyChanged('isRestrictedToMembers', $this->isRestrictedToMembers, $isRestrictedToMembers);
        $this->isRestrictedToMembers = $isRestrictedToMembers;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsRestrictedToCfpSubmitter()
    {
        return $this->isRestrictedToCfpSubmitter;
    }

    /**
     * @param bool $isRestrictedToCfpSubmitter
     */
    public function setIsRestrictedToCfpSubmitter($isRestrictedToCfpSubmitter): self
    {
        $this->propertyChanged('isRestrictedToCfpSubmitter', $this->isRestrictedToCfpSubmitter, $isRestrictedToCfpSubmitter);
        $this->isRestrictedToCfpSubmitter = $isRestrictedToCfpSubmitter;
        return $this;
    }

    /**
     * @return float
     */
    public function getDefaultPrice()
    {
        return $this->defaultPrice;
    }

    /**
     * @param float $defaultPrice
     */
    public function setDefaultPrice($defaultPrice): self
    {
        $this->propertyChanged('defaultPrice', $this->defaultPrice, $defaultPrice);
        $this->defaultPrice = $defaultPrice;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive): self
    {
        $this->propertyChanged('isActive', $this->isActive, $isActive);
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param string $day
     */
    public function setDay($day): self
    {
        $this->propertyChanged('day', $this->day, $day);
        $this->day = $day;
        return $this;
    }

    public function getDays()
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
