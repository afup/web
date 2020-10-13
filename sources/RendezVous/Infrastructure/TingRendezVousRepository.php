<?php

namespace App\RendezVous\Infrastructure;

use App\RendezVous\RendezVous;
use App\RendezVous\RendezVousRepository;
use App\Ting\TingHelper;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TingRendezVousRepository extends Repository implements RendezVousRepository, MetadataInitializer
{
    /**
     * @param int $id
     *
     * @return RendezVous|null
     */
    public function find($id)
    {
        return $this->get($id);
    }

    public function findAll()
    {
        return $this->getAll();
    }

    /** @return RendezVous|null */
    public function findNext()
    {
        return TingHelper::getOneOrNullResult($this, $this->getQuery('SELECT * FROM afup_rendezvous WHERE debut > UNIX_TIMESTAMP() ORDER BY debut LIMIT 0, 1'));
    }

    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(RendezVous::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_rendezvous');
        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary' => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'titre',
                'fieldName' => 'title',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'accroche',
                'fieldName' => 'pitch',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'theme',
                'fieldName' => 'theme',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'debut',
                'fieldName' => 'start',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'fin',
                'fieldName' => 'end',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'lieu',
                'fieldName' => 'place',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'url',
                'fieldName' => 'url',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'plan',
                'fieldName' => 'plan',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'adresse',
                'fieldName' => 'address',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'capacite',
                'fieldName' => 'capacity',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'id_antenne',
                'fieldName' => 'officeId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'inscription',
                'fieldName' => 'registration',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'url_externe',
                'fieldName' => 'externalUrl',
                'type' => 'string',
            ]);

        return $metadata;
    }
}
