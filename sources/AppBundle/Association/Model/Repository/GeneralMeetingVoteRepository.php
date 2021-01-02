<?php

namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\GeneralMeetingVote;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class GeneralMeetingVoteRepository extends Repository implements MetadataInitializer
{
    public function loadByQuestionIdAndUserId($questionId, $userId)
    {
        return $this->getOneBy([
            'questionId' => $questionId,
            'userId' => $userId,
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(GeneralMeetingVote::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_vote_assemblee_generale');

        $metadata
            ->addField([
                'columnName' => 'afup_assemblee_generale_question_id',
                'fieldName' => 'questionId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'afup_personnes_physiques_id',
                'fieldName' => 'userId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'weight',
                'fieldName' => 'weight',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'value',
                'fieldName' => 'value',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'created_at',
                'fieldName' => 'createdAt',
                'type' => 'datetime',
            ])
        ;

        return $metadata;
    }
}
