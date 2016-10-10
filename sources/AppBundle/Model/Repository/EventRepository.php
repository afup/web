<?php


namespace AppBundle\Model\Repository;


use AppBundle\Model\Event;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class EventRepository extends Repository implements MetadataInitializer
{
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(Event::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_forum');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'titre',
                'fieldName' => 'title',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'nb_places',
                'fieldName' => 'seats',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'date_debut',
                'fieldName' => 'dateStart',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => false]
                ]

            ])
            ->addField([
                'columnName' => 'date_fin',
                'fieldName' => 'dateEnd',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => false]
                ]
            ])
            ->addField([
                'columnName' => 'date_fin_appel_projet',
                'fieldName' => 'dateEndCallForProjects',
                'type' => 'datetime'
            ])
            ->addField([
                'columnName' => 'date_fin_appel_conferencier',
                'fieldName' => 'dateEndCallForPapers',
                'type' => 'datetime'
            ])
            ->addField([
                'columnName' => 'date_fin_prevente',
                'fieldName' => 'dateEndPreSales',
                'type' => 'datetime'
            ])
            ->addField([
                'columnName' => 'date_fin_vente',
                'fieldName' => 'dateEndSales',
                'type' => 'datetime'
            ])
            ->addField([
                'columnName' => 'path',
                'fieldName' => 'path',
                'type' => 'string'
            ])
        ;

        return $metadata;
    }
}
