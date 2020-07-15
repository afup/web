<?php

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
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'id_forum',
                'fieldName' => 'idEvent',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'texte',
                'fieldName' => 'text',
                'type' => 'string'
            ]);

        return $metadata;
    }

    public function changeCouponForEvent(array $coupons, Event $event)
    {
        if ($event->getId() === null) {
            throw new \Exception("Impossible d'ajouter des coupons a un événement sans ID");
        }
        $sql = 'DELETE FROM afup_forum_coupon WHERE id_forum = :id';
        $query = $this->getQuery($sql);
        $query->setParams(['id' => $event->getId()]);
        $query->execute();

        foreach ($coupons as $coupon) {
            if (is_string($coupon) === false || empty($coupon) === true) {
                continue;
            }
            $this->save(EventCoupon::initForEventAndCoupon($event, $coupon));
        }
    }

    public function couponsListForEvent(Event $event)
    {
        if ($event->getId() === null) {
            throw new \Exception("Impossible de lire les coupons d'un événement sans ID");
        }
        $sql = 'SELECT * FROM afup_forum_coupon WHERE id_forum = :id';
        $query = $this->getQuery($sql);
        $query->setParams(['id' => $event->getId()]);
        return $query->query($this->getCollection(new HydratorSingleObject()));
    }
}
