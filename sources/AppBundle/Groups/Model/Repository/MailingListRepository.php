<?php

namespace AppBundle\Groups\Model\Repository;

use AppBundle\Groups\Model\MailingList;
use Aura\SqlQuery\Mysql\Select;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class MailingListRepository extends Repository implements MetadataInitializer
{
    /**
     * @param bool $membersOnly
     * @param null $category
     * @return \CCMBenchmark\Ting\Repository\CollectionInterface|MailingList[]
     * @throws \CCMBenchmark\Ting\Exception
     * @throws \CCMBenchmark\Ting\Query\QueryException
     */
    public function getAllMailingLists($membersOnly = false, $category = null)
    {
        /**
         * @var $query Select
         */
        $query = $this->getQueryBuilder(self::QUERY_SELECT);
        $query
            ->cols(['id', 'email', 'name', 'description', 'members_only', 'category'])
            ->from('afup_mailing_lists')
            ->orderBy(['category', 'name'])
        ;
        $params = [];
        if ($membersOnly === true) {
            $query->where('members_only = 1');
        }
        if ($category !== null) {
            $query->where('category = :category');
            $params['category'] = $category;
        }

        return $this
            ->getQuery($query->getStatement())
            ->setParams($params)
            ->query($this->getCollection(new HydratorSingleObject()))
        ;
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(MailingList::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_mailing_lists');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'name',
                'fieldName' => 'name',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'description',
                'fieldName' => 'description',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'members_only',
                'fieldName' => 'membersOnly',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'category',
                'fieldName' => 'category',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'auto_registration',
                'fieldName' => 'autoRegistration',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
        ;

        return $metadata;
    }
}
