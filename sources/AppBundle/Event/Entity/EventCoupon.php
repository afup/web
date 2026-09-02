<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\EventCouponRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventCouponRepository::class)]
#[ORM\Table(name: 'afup_forum_coupon')]
class EventCoupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(name: 'id_forum', nullable: false)]
    public int $eventId;

    #[ORM\Column(name: 'texte', length: 45, nullable: false)]
    public string $text;
}
