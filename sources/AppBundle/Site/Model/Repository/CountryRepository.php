<?php

declare(strict_types=1);

namespace AppBundle\Site\Model\Repository;

use AppBundle\Site\Model\Country;
use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Country>
 */
class CountryRepository extends Repository implements MetadataInitializer
{
    /**
     * @return CollectionInterface<Country>
     */
    public function getAllCountries(): CollectionInterface
    {
        /** @var SelectInterface $builder */
        $builder = $this->getQueryBuilder(self::QUERY_SELECT);
        $builder->cols(['*'])
            ->from('afup_pays')
            ->orderBy(['nom asc']);

        return $this->getQuery($builder->getStatement())->query($this->getCollection(new HydratorSingleObject()));
    }


    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = []): Metadata
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(Country::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_pays');
        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary' => true,
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'nom',
                'fieldName' => 'name',
                'type' => 'string',
            ]);

        return $metadata;
    }
}
