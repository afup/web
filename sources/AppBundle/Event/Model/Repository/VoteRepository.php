<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Vote;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class VoteRepository extends Repository implements MetadataInitializer
{
    public function getNumberOfVotesByEvent(Event $event)
    {
        $query = $this->getQuery('SELECT COUNT(id) AS votes
FROM afup_sessions_vote_github asvg
LEFT JOIN afup_sessions s ON s.session_id = asvg.session_id
WHERE s.id_forum = :event');
        $query->setParams(['event' => $event->getId()]);
        return $query->query($this->getCollection(new HydratorArray()))->first();
    }

    /**
     * @param int $eventId
     * @return CollectionInterface
     */
    public function getVotesByEvent($eventId)
    {
        $query = $this
            ->getPreparedQuery('
            SELECT asvg.id, asvg.session_id, submitted_on, asvg.comment, asvg.user, vote,
            sessions.titre, sessions.abstract, aug.login, aug.avatar_url
            FROM afup_sessions_vote_github asvg
            LEFT JOIN afup_sessions sessions ON sessions.session_id = asvg.session_id
            LEFT JOIN afup_user_github aug ON aug.id = asvg.user
            WHERE sessions.id_forum = :eventId
            ORDER BY asvg.session_id, asvg.submitted_on
            ');
        $query->setParams(['eventId' => (int) $eventId]);

        $hydrator = new HydratorSingleObject();
        $hydrator
            ->mapObjectTo('sessions', 'asvg', 'setTalk')
            ->mapObjectTo('aug', 'asvg', 'setGithubUser')
        ;
        return $query->query($this->getCollection($hydrator));
    }

    public function getVotesByTalkWithUser($talkId)
    {
        $query = $this->getPreparedQuery('
            SELECT asvg.id, asvg.session_id, asvg.submitted_on, asvg.comment, asvg.user, asvg.vote, aug.afup_crew
            FROM afup_sessions_vote_github asvg
            LEFT JOIN afup_user_github aug ON aug.id = asvg.user
            WHERE asvg.session_id = :talkId
            ORDER BY asvg.submitted_on DESC
        ');

        $query->setParams(['talkId' => (int) $talkId]);

        $hydrator = new HydratorSingleObject();
        $hydrator
            ->mapObjectTo('aug', 'asvg', 'setGithubUser')
        ;
        return $query->query($this->getCollection($hydrator));
    }

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
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'session_id',
                'fieldName' => 'sessionId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'submitted_on',
                'fieldName' => 'submittedOn',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'comment',
                'fieldName' => 'comment',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'user',
                'fieldName' => 'user',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'vote',
                'fieldName' => 'vote',
                'type' => 'int',
            ])
        ;

        return $metadata;
    }

    public function upsert(Vote $vote): void
    {
        /**
         * @var Vote|null $previousVote
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
