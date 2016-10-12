<?php


namespace AppBundle\Model\Repository;


use AppBundle\Model\Vote;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class VoteRepository extends Repository implements MetadataInitializer
{
    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Vote::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_sessions_vote_github');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'session_id',
                'fieldName' => 'sessionId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'submitted_on',
                'fieldName' => 'submittedOn',
                'type' => 'datetime'
            ])
            ->addField([
                'columnName' => 'comment',
                'fieldName' => 'comment',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'user',
                'fieldName' => 'user',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'vote',
                'fieldName' => 'vote',
                'type' => 'int'
            ])
        ;

        return $metadata;
    }

    public function upsert(Vote $vote)
    {
        /**
         * @var $previousVote Vote|null
         */
        $previousVote = $this->getOneBy(['user' => $vote->getUser(), 'sessionId' => $vote->getSessionId()]);
        if ($previousVote !== null) {
            $previousVote
                ->setComment($vote->getComment())
                ->setSubmittedOn($vote->getSubmittedOn())
                ->setVote($vote->getVote())
            ;
            $vote = $previousVote;
        }
        $this->save($vote);
    }

}
