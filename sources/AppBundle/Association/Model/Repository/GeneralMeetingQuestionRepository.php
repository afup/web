<?php

declare(strict_types=1);

namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\GeneralMeetingQuestion;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class GeneralMeetingQuestionRepository extends Repository implements MetadataInitializer
{
    public function loadNextOpenedQuestion(\DateTimeInterface $generalMeetingDate)
    {
        $sql = <<<SQL
SELECT
  afup_assemblee_generale_question.id,
  afup_assemblee_generale_question.date,
  afup_assemblee_generale_question.label,
  afup_assemblee_generale_question.opened_at,
  afup_assemblee_generale_question.closed_at,
  afup_assemblee_generale_question.created_at
FROM afup_assemblee_generale_question
WHERE
  afup_assemblee_generale_question.opened_at IS NOT NULL
  AND afup_assemblee_generale_question.closed_at IS NULL
  AND afup_assemblee_generale_question.date = :general_meeting_date
ORDER BY afup_assemblee_generale_question.opened_at ASC
LIMIT 1
SQL;

        $params = [
            'general_meeting_date' => $generalMeetingDate->format('U'),
        ];

        $query = $this->getPreparedQuery($sql)->setParams($params);

        $collection = $query->query($this->getCollection(new HydratorSingleObject()));

        if ($collection->count() === 0) {
            return null;
        }

        return $collection->first();
    }

    /**
     * @return iterable<GeneralMeetingQuestion>
     */
    public function loadClosedQuestions(\DateTimeInterface $generalMeetingDate)
    {
        $sql = <<<SQL
SELECT
  afup_assemblee_generale_question.*
FROM afup_assemblee_generale_question
WHERE
  afup_assemblee_generale_question.closed_at IS NOT NULL
  AND afup_assemblee_generale_question.date = :general_meeting_date
ORDER BY afup_assemblee_generale_question.opened_at ASC
SQL;

        $params = [
            'general_meeting_date' => $generalMeetingDate->format('U'),
        ];

        /** @var iterable<GeneralMeetingQuestion> */
        return $this->getPreparedQuery($sql)->setParams($params)->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * @return iterable<GeneralMeetingQuestion>
     */
    public function loadByDate(\DateTimeInterface $generalMeetingDate)
    {
        /** @var iterable<GeneralMeetingQuestion> */
        return $this->getBy([
            'date' => $generalMeetingDate->format('U'),
        ]);
    }

    public function open(GeneralMeetingQuestion $question): void
    {
        $question->setOpenedAt(new \DateTime());
        $this->save($question);
    }

    public function close(GeneralMeetingQuestion $question): void
    {
        $question->setClosedAt(new \DateTime());
        $this->save($question);
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(GeneralMeetingQuestion::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_assemblee_generale_question');

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
                    'serialize' => ['format' => 'U'],
                ],
            ])
            ->addField([
                'columnName' => 'label',
                'fieldName' => 'label',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'opened_at',
                'fieldName' => 'openedAt',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'closed_at',
                'fieldName' => 'closedAt',
                'type' => 'datetime',
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
