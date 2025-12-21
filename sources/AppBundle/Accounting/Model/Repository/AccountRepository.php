<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model\Repository;

use AppBundle\Accounting\Model\Account;
use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Repository\Collection;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Account>
 */
class AccountRepository extends Repository implements MetadataInitializer
{
    /**
     * @return Collection<Account>
     */
    public function getActiveAccounts(): CollectionInterface
    {
        /** @var SelectInterface $builder */
        $builder = $this->getQueryBuilder(self::QUERY_SELECT);
        $builder->cols(['id', 'nom_compte'])
            ->from('compta_compte')
            ->where('archived_at IS NULL')
            ->orderBy(['nom_compte asc']);

        return $this->getQuery($builder->getStatement())->query($this->getCollection(new HydratorSingleObject()));
    }

    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Account::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('compta_compte');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'nom_compte',
                'fieldName' => 'name',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'archived_at',
                'fieldName' => 'archivedAt',
                'type' => 'datetime',
            ])
        ;

        return $metadata;
    }
}
