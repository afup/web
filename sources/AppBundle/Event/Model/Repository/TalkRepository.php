<?php

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TalkRepository extends Repository implements MetadataInitializer
{
    public function getNumberOfTalksByEvent(Event $event)
    {
        $query = $this->getQuery('SELECT COUNT(session_id) AS talks FROM afup_sessions WHERE id_forum = :event');
        $query->setParams(['event' => $event->getId()]);
        return $query->query($this->getCollection(new HydratorArray()))->first();
    }

    /**
     * @param Event $event
     * @param Speaker $speaker
     * @return \CCMBenchmark\Ting\Repository\CollectionInterface
     */
    public function getTalksBySpeaker(Event $event, Speaker $speaker)
    {
        $query = $this->getPreparedQuery(
            'SELECT sessions.session_id, titre, abstract, id_forum
            FROM afup_sessions sessions
            LEFT JOIN afup_conferenciers_sessions cs ON cs.session_id = sessions.session_id
            WHERE id_forum = :event AND cs.conferencier_id = :speaker
            ORDER BY titre
            LIMIT 0, 10
            '
        )->setParams(['event' => $event->getId(), 'speaker' => $speaker->getId()]);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * Retrieve the list of talks to rate.
     * It retrieve $limit + 1 row. So if `count($results) <= $limit` there is no more result.
     * Otherwise you should add a "next" item on your paginator
     *
     * @param Event $event
     * @param GithubUser $user
     * @param int $randomSeed used to create a consistent random
     * @param int $page starting from 1
     * @param int $limit
     * @return \CCMBenchmark\Ting\Repository\CollectionInterface
     */
    public function getAllTalksAndRatingsForUser(Event $event, GithubUser $user, $randomSeed, $page = 1, $limit = 10)
    {
        $query = $this->getPreparedQuery(
            'SELECT sessions.session_id, titre, abstract, skill, genre, id_forum, asvg.id, asvg.comment, asvg.vote
            FROM afup_sessions sessions
            LEFT JOIN afup_sessions_vote_github asvg ON (asvg.session_id = sessions.session_id AND asvg.user = :user)
            WHERE plannifie = 0 AND id_forum = :event
            ORDER BY RAND(:randomSeed)
            LIMIT '. ((int)$page - 1)*$limit . ', '. ((int)$limit + 1)
        )->setParams(['event' => $event->getId(), 'user' => $user->getId(), 'randomSeed' => $randomSeed]);

        return $query->query();
    }

    /**
     * Retrieve all talks with ratings from current user if applicable
     * It retrieve $limit + 1 row. So if `count($results) <= $limit` there is no more result.
     * Otherwise you should add a "next" item on your paginator
     *
     * @param Event $event
     * @param GithubUser $user
     * @param int $randomSeed used to create a consistent random
     * @param int $page starting from 1
     * @param int $limit
     * @return \CCMBenchmark\Ting\Repository\CollectionInterface
     */
    public function getNewTalksToRate(Event $event, GithubUser $user, $randomSeed, $page = 1, $limit = 10)
    {
        $query = $this->getPreparedQuery(
            'SELECT sessions.session_id, titre, skill, genre, abstract, id_forum
            FROM afup_sessions sessions
            LEFT JOIN afup_sessions_vote_github asvg ON (asvg.session_id = sessions.session_id AND asvg.user = :user)
            WHERE plannifie = 0 AND id_forum = :event
            AND asvg.id IS NULL
            ORDER BY RAND(:randomSeed)
            LIMIT '. ((int)$page - 1)*$limit . ', '. ((int)$limit + 1)
        )->setParams(['event' => $event->getId(), 'user' => $user->getId(), 'randomSeed' => $randomSeed]);

        return $query->query();
    }

    /**
     * @param SerializerFactoryInterface $serializerFactory
     * @param array $options
     * @return Metadata
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(Talk::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_sessions');

        $metadata
            ->addField([
                'columnName' => 'session_id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'id_forum',
                'fieldName' => 'forumId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'date_soumission',
                'fieldName' => 'submittedOn',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => false]
                ]
            ])
            ->addField([
                'columnName' => 'titre',
                'fieldName' => 'title',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'abstract',
                'fieldName' => 'abstract',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'genre',
                'fieldName' => 'type',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'skill',
                'fieldName' => 'skill',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'plannifie',
                'fieldName' => 'scheduled',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'needs_mentoring',
                'fieldName' => 'needsMentoring',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
        ;

        return $metadata;
    }

}
