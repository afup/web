<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Antennes\Antenne;
use AppBundle\Event\Model\Meetup;
use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use Symfony\Component\Clock\ClockAwareTrait;

/**
 * @extends Repository<Meetup>
 */
class MeetupRepository extends Repository implements MetadataInitializer
{
    use ClockAwareTrait;

    public function findNextForAntenne(Antenne $antenne): ?Meetup
    {
        /** @var SelectInterface $qb */
        $qb = $this->getQueryBuilder(self::QUERY_SELECT);
        $qb
            ->from('afup_meetup m')
            ->cols(['m.*'])
            ->where('m.antenne_name = :name')
            ->where('m.date > :after')
            ->orderBy(['m.date desc'])
            ->limit(1)
        ;

        return $this->getPreparedQuery($qb->getStatement())
            ->setParams([
                'name' => $antenne->code,
                'after' => $this->now()->modify('midnight')->format(DATE_RFC3339),
            ])
            ->query($this->getCollection(new HydratorSingleObject()))
            ->first();
    }

    /**
     *
     * @return Metadata
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Meetup::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_meetup');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary' => true,
                'autoincrement' => false,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'date',
                'fieldName' => 'date',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'title',
                'fieldName' => 'title',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'location',
                'fieldName' => 'location',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'description',
                'fieldName' => 'description',
                'type' => 'text',
                'nullable' => true,
            ])
            ->addField([
                'columnName' => 'antenne_name',
                'fieldName' => 'antenneName',
                'type' => 'string',
            ]);

        return $metadata;
    }
}
