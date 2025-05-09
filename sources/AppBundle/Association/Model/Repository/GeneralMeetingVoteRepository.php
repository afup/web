<?php

declare(strict_types=1);

namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\GeneralMeetingVote;
use CCMBenchmark\Ting\Repository\HydratorArray;
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
     * @return array<string, int>
     */
    public function getResultsForQuestionId(int $questionId): array
    {
        $results = [
            GeneralMeetingVote::VALUE_YES => 0,
            GeneralMeetingVote::VALUE_NO => 0,
            GeneralMeetingVote::VALUE_ABSTENTION => 0,
        ];

        $sql = <<<SQL
SELECT `value`,
       SUM(afup_vote_assemblee_generale.weight) as weight_sum
FROM afup_vote_assemblee_generale
WHERE afup_vote_assemblee_generale.afup_assemblee_generale_question_id = :question_id
GROUP BY `value`
SQL;

        $preparedQuery = $this->getPreparedQuery($sql)->setParams(['question_id' => $questionId]);
        foreach ($preparedQuery->query($this->getCollection(new HydratorArray())) as $row) {
            $results[$row['value']] = $row['weight_sum'];
        }

        return $results;
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
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'afup_personnes_physiques_id',
                'fieldName' => 'userId',
                'type' => 'int',
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
