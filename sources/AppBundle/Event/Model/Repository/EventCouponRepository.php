<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\EventCoupon;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class EventCouponRepository extends Repository implements MetadataInitializer
{
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(EventCoupon::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_forum_coupon');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary' => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'id_forum',
                'fieldName' => 'idEvent',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'texte',
                'fieldName' => 'text',
                'type' => 'string',
            ]);

        return $metadata;
    }

    public function changeCouponForEvent(Event $event, array $coupons): void
    {
        $query = $this->getQuery('DELETE FROM afup_forum_coupon WHERE id_forum = :id');
        $query->setParams([
            'id' => $event->getId(),
        ]);
        $query->execute();

        foreach ($coupons as $coupon) {
            $coupon = trim((string) $coupon);
            if (empty($coupon) === true) {
                continue;
            }
            $this->save(EventCoupon::initForEventAndCoupon($event, $coupon));
        }
    }

    public function couponsListForEvent(Event $event)
    {
        $query = $this->getQuery('SELECT * FROM afup_forum_coupon WHERE id_forum = :id');
        $query->setParams([
            'id' => $event->getId(),
        ]);
        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    public function couponsListForEventImploded(Event $event, string $separator = ', '): string
    {
        $eventCoupons = $this->couponsListForEvent($event);
        $array = array_map(static fn (EventCoupon $coupon) => $coupon->getText(), iterator_to_array($eventCoupons));

        return implode($separator, $array);
    }
}
