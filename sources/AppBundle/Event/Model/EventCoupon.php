<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class EventCoupon implements NotifyPropertyInterface
{
    use NotifyProperty;

    private int $id;

    private int $idEvent;

    private string $text;

    public static function initForEventAndCoupon(Event $event, string $coupon): self
    {
        if ($event->tryGetId() === null) {
            throw new \Exception('Event has no ID');
        }

        if (strlen($coupon) === 0) {
            throw new \Exception('Coupon argument is not a not empty string');
        }

        $eventCoupon = new self();
        $eventCoupon->setIdEvent($event->getId());
        $eventCoupon->setText($coupon);

        return $eventCoupon;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getIdEvent(): ?int
    {
        return $this->idEvent;
    }

    public function setIdEvent(int $idEvent): void
    {
        $this->propertyChanged('idEvent', $this->idEvent ?? '', $idEvent);
        $this->idEvent = $idEvent;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->propertyChanged('text', $this->text ?? '', $text);
        $this->text = $text;
    }
}
