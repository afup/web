<?php

namespace App\RendezVous\Infrastructure;

use App\RendezVous\RendezVous;
use App\RendezVous\RendezVousSlide;
use App\RendezVous\RendezVousSlideRepository;
use App\Ting\TingHelper;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @method RendezVousSlide|null get($primariesKeyValue, $forceMaster = false)
 */
class TingRendezVousSlideRepository extends Repository implements RendezVousSlideRepository, MetadataInitializer
{
    public function find($id)
    {
        return $this->get($id);
    }

    /** @return CollectionInterface&RendezVousSlide[] */
    public function findByRendezVous(RendezVous $rendezVous)
    {
        return TingHelper::getResult($this, $this->getQuery('SELECT * FROM afup_rendezvous_slides WHERE id_rendezvous = :id')
            ->setParams(['id' => $rendezVous->getId()]));
    }

    public function deleteByRendezVous(RendezVous $rendezVous)
    {
        $this->getQuery('DELETE FROM afup_rendezvous_slides WHERE id_rendezvous = :id')
            ->setParams(['id' => $rendezVous->getId()])
            ->execute();
    }

    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(RendezVousSlide::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_rendezvous_slides');
        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary' => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'fichier',
                'fieldName' => 'file',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'url',
                'fieldName' => 'url',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'id_rendezvous',
                'fieldName' => 'rendezVousId',
                'type' => 'int',
            ]);

        return $metadata;
    }
}
