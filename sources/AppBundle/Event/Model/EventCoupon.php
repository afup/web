<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class EventCoupon implements NotifyPropertyInterface
{
    use NotifyProperty;
    /**
     * @var null|int
     */
    private $id;

    /**
     * @var null|int
     */
    private $idEvent;

    /**
     * @var null|string
     */
    private $text;

    /**
     * @param Event $event
     * @param string $coupon
     * @return self
     */
    public static function initForEventAndCoupon(Event $event, $coupon)
    {
        if ($event->getId() === null) {
            throw new \Exception('Event has no ID');
        }
        if (empty($coupon) === true || is_string($coupon) === false) {
            throw new \Exception('Coupon argument is not a not empty string');
        }
        $eventCoupon = new self();
        $eventCoupon->setIdEvent($event->getId());
        $eventCoupon->setText($coupon);
        return $eventCoupon;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getIdEvent()
    {
        return $this->idEvent;
    }

    /**
     * @param int|null $idEvent
     */
    public function setIdEvent($idEvent): void
    {
        $this->propertyChanged('idEvent', $this->idEvent, $idEvent);
        $this->idEvent = $idEvent;
    }

    /**
     * @return string|null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     */
    public function setText($text): void
    {
        $this->propertyChanged('text', $this->text, $text);
        $this->text = $text;
    }
}
