<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\EventCoupon;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<EventCoupon>
 */
final class EventCouponRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventCoupon::class);
    }

    /**
     * @param list<string> $coupons
     */
    public function changeCouponForEvent(int $eventId, array $coupons): void
    {
        $this->getEntityManager()->wrapInTransaction(function () use ($eventId, $coupons): void {
            ($qb = $this->createQueryBuilder('c'))
                ->delete()
                ->where($qb->expr()->eq('c.eventId', ':eventId'))
                ->setParameter('eventId', $eventId)
                ->getQuery()
                ->execute();

            foreach ($coupons as $coupon) {
                $coupon = trim($coupon);
                if ($coupon === '') {
                    continue;
                }

                $eventCoupon = new EventCoupon();
                $eventCoupon->eventId = $eventId;
                $eventCoupon->text = $coupon;
                $this->getEntityManager()->persist($eventCoupon);
            }

            $this->getEntityManager()->flush();
        });
    }

    /**
     * @return list<EventCoupon>
     */
    public function couponsListForEvent(int $eventId): array
    {
        return ($qb = $this->createQueryBuilder('c'))
            ->where($qb->expr()->eq('c.eventId', ':eventId'))
            ->setParameter('eventId', $eventId)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function couponsListForEventImploded(int $eventId, string $separator = ', '): string
    {
        $texts = array_map(
            static fn(EventCoupon $coupon): string => $coupon->text,
            $this->couponsListForEvent($eventId),
        );

        return implode($separator, $texts);
    }
}
