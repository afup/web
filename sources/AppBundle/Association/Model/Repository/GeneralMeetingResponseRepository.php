<?php

declare(strict_types=1);

namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\GeneralMeetingResponse;
use AppBundle\Association\Model\User;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class GeneralMeetingResponseRepository extends Repository implements MetadataInitializer
{
    public function getByUser(User $user)
    {
        return $this->getBy([
            'userId' => $user->getId(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(GeneralMeetingResponse::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_presences_assemblee_generale');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'date',
                'fieldName' => 'date',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                ],
            ])
            ->addField([
                'columnName' => 'id_personne_physique',
                'fieldName' => 'userId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'presence',
                'fieldName' => 'status',
                'type' => 'int',
            ])
        ;

        return $metadata;
    }
}
