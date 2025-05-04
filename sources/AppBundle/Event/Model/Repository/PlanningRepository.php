<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Talk;
use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Planning>
 */
class PlanningRepository extends Repository implements MetadataInitializer
{
    /**
     * @return Planning|null
     */
    public function getByTalk(Talk $talk)
    {
        $query = $this
            ->getQuery('SELECT * FROM afup_forum_planning WHERE id_session= :id_session LIMIT 1')
        ;

        $query->setParams(['id_session' => $talk->getId()]);

        $plannings = $query->query($this->getCollection(new HydratorSingleObject()));
        if ($plannings->count() === 0) {
            return null;
        }

        return $plannings->first();
    }

    /**
     * @return CollectionInterface<Planning>
     */
    public function findNonKeynotesBetween(\DateTimeInterface $since, \DateTimeInterface $until): CollectionInterface
    {
        /** @var SelectInterface $qb */
        $qb = $this->getQueryBuilder(self::QUERY_SELECT);

        $qb->from('afup_forum_planning')
            ->cols(['*'])
            ->where('keynote = 0')
            ->where('debut >= :since')
            ->where('debut < :until')
        ;

        return $this
            ->getPreparedQuery($qb->getStatement())
            ->setParams([
                'since' => $since->getTimestamp(),
                'until' => $until->getTimestamp(),
            ])
            ->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * @return Metadata
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(Planning::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_forum_planning');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'id_session',
                'fieldName' => 'talkId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'id_forum',
                'fieldName' => 'eventId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'debut',
                'fieldName' => 'start',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                ],
            ])
            ->addField([
                'columnName' => 'fin',
                'fieldName' => 'end',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                ],
            ])
            ->addField([
                'columnName' => 'keynote',
                'fieldName' => 'isKeynote',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
        ;

        return $metadata;
    }
}
