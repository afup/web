<?php

declare(strict_types=1);


namespace AppBundle\TechLetter\Model\Repository;

use AppBundle\TechLetter\Model\Sending;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class SendingRepository extends Repository implements MetadataInitializer
{
    public function getAllOrderedByDateDesc()
    {
        $sql = <<<SQL
SELECT afup_techletter.*
FROM afup_techletter
ORDER BY afup_techletter.sending_date DESC
SQL;
        $query = $this->getQuery($sql);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }
    public function getAllPastSent()
    {
        $sql = <<<SQL
SELECT afup_techletter.*
FROM afup_techletter
WHERE afup_techletter.sent_to_mailchimp = 1
AND afup_techletter.sending_date < NOW()
ORDER BY afup_techletter.sending_date DESC
SQL;
        $query = $this->getQuery($sql);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Sending::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_techletter');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'sending_date',
                'fieldName' => 'sendingDate',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'techletter',
                'fieldName' => 'techletter',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'sent_to_mailchimp',
                'fieldName' => 'sentToMailchimp',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'archive_url',
                'fieldName' => 'archiveUrl',
                'type' => 'string',
            ])
        ;

        return $metadata;
    }
}
