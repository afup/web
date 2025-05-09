<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\TalkInvitation;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TalkInvitationRepository extends Repository implements MetadataInitializer
{
    /**
     * @param $talkId
     * @return CollectionInterface
     */
    public function getPendingInvitationsByTalkId($talkId)
    {
        $query = $this->getPreparedQuery(
            'SELECT asi.id, asi.talk_id, asi.state, asi.submitted_on, asi.submitted_by, asi.token, asi.email
            FROM afup_sessions_invitation asi
            WHERE asi.talk_id = :talkId AND asi.state = :state'
        );
        $query->setParams(['talkId' => $talkId, 'state' => TalkInvitation::STATE_PENDING]);

        return $query->query();
    }

    /**
     * @return Metadata
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(TalkInvitation::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_sessions_invitation');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'talk_id',
                'fieldName' => 'talkId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'state',
                'fieldName' => 'state',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'submitted_on',
                'fieldName' => 'submittedOn',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'submitted_by',
                'fieldName' => 'submittedBy',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'token',
                'fieldName' => 'token',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string',
            ])
        ;

        return $metadata;
    }
}
