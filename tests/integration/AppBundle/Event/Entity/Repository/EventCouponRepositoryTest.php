<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\Repository\EventCouponRepository;
use Doctrine\DBAL\Connection;

final class EventCouponRepositoryTest extends IntegrationTestCase
{
    public function testChangeCouponForEventReplacesPreviousCoupons(): void
    {
        $repository = self::getContainer()->get(EventCouponRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $repository->changeCouponForEvent(42, ['FREE_FORUM', '  SUPER_FORUM  ', '', '   ']);

        self::assertSame(['FREE_FORUM', 'SUPER_FORUM'], $this->fetchCouponTexts($connection, 42));

        $repository->changeCouponForEvent(42, ['OTHER_COUPON']);

        self::assertSame(['OTHER_COUPON'], $this->fetchCouponTexts($connection, 42));
    }

    public function testChangeCouponForEventWithOnlyEmptyCouponsEmptiesTheTable(): void
    {
        $repository = self::getContainer()->get(EventCouponRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $repository->changeCouponForEvent(42, ['FREE_FORUM']);
        $repository->changeCouponForEvent(42, ['', '  ']);

        self::assertSame([], $this->fetchCouponTexts($connection, 42));
    }

    public function testCouponsListForEventReturnsCouponsOfTheEventOnly(): void
    {
        $repository = self::getContainer()->get(EventCouponRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $repository->changeCouponForEvent(42, ['COUPON_42']);
        $repository->changeCouponForEvent(43, ['COUPON_43']);

        self::assertSame(['COUPON_42'], $this->fetchCouponTexts($connection, 42));
        self::assertSame(['COUPON_43'], $this->fetchCouponTexts($connection, 43));
    }

    public function testCouponsListForEventImplodedJoinsCouponTexts(): void
    {
        $repository = self::getContainer()->get(EventCouponRepository::class);

        $repository->changeCouponForEvent(42, ['COUPON_A', 'COUPON_B']);

        self::assertSame('COUPON_A, COUPON_B', $repository->couponsListForEventImploded(42));
        self::assertSame('COUPON_A|COUPON_B', $repository->couponsListForEventImploded(42, '|'));
        self::assertSame('', $repository->couponsListForEventImploded(999));
    }

    /**
     * @return list<string>
     */
    private function fetchCouponTexts(Connection $connection, int $eventId): array
    {
        return array_column(
            $connection->fetchAllAssociative(
                'SELECT texte FROM afup_forum_coupon WHERE id_forum = :id ORDER BY id',
                ['id' => $eventId],
            ),
            'texte',
        );
    }
}
