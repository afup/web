<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\JoinHydrator;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Query\QueryException;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TalkRepository extends Repository implements MetadataInitializer
{
    public function getNumberOfTalksByEvent(Event $event, \DateTime $since = null)
    {
        return $this->getNumberOfTalksByEventAndLanguage($event, null, $since);
    }

    public function getNumberOfTalksByEventAndLanguage(Event $event, $languageCode = null, \DateTime $since = null)
    {
        $sql = 'SELECT COUNT(session_id) AS talks FROM afup_sessions WHERE id_forum = :event';
        $params = ['event' => $event->getId()];
        if ($since instanceof \DateTime) {
            $sql .= ' AND date_soumission >= :since ';
            $params['since'] = $since->format('Y-m-d');
        }
        if (null !== $languageCode) {
            $sql .= ' AND language_code = :language ';
            $params['language'] = $languageCode;
        }
        $query = $this->getQuery($sql);
        $query->setParams($params);

        return $query->query($this->getCollection(new HydratorArray()))->first();
    }

    /**
     * @return CollectionInterface&iterable<Talk>
     */
    public function getTalksBySpeaker(Event $event, Speaker $speaker)
    {
        $query = $this->getPreparedQuery(
            'SELECT sessions.session_id, titre, abstract, id_forum, sessions.plannifie, skill, genre
            FROM afup_sessions sessions
            LEFT JOIN afup_conferenciers_sessions cs ON cs.session_id = sessions.session_id
            WHERE id_forum = :event AND cs.conferencier_id = :speaker
            ORDER BY titre
            LIMIT 0, 20
            '
        )->setParams(['event' => $event->getId(), 'speaker' => $speaker->getId()]);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * @return CollectionInterface
     */
    public function getPreviousTalksBySpeaker(Event $event, Speaker $speaker)
    {
        $query = $this->getPreparedQuery(
            'SELECT s.session_id, s.titre, s.abstract, s.id_forum, s.plannifie, s.skill, s.genre
            FROM afup_sessions s
            JOIN afup_conferenciers_sessions cs ON cs.session_id = s.session_id
            JOIN afup_conferenciers c ON cs.conferencier_id = c.conferencier_id
            WHERE s.id_forum != :event AND c.user_github IN (SELECT user_github FROM afup_conferenciers WHERE conferencier_id = :speaker) 
            ORDER BY s.titre ASC
            LIMIT 0, 50
            '
        )->setParams(['event' => $event->getId(), 'speaker' => $speaker->getId()]);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * Retrieve the list of talks to rate.
     * It retrieve $limit + 1 row. So if `count($results) <= $limit` there is no more result.
     * Otherwise you should add a "next" item on your paginator
     *
     * @param int $randomSeed used to create a consistent random
     * @param int $page starting from 1
     * @param int $limit
     * @return CollectionInterface
     */
    public function getAllTalksAndRatingsForUser(Event $event, GithubUser $user, $randomSeed, $page = 1, $limit = 10)
    {
        $query = $this->getPreparedQuery(
            'SELECT sessions.session_id, titre, abstract, skill, genre, id_forum, asvg.id, asvg.comment, asvg.vote
            FROM afup_sessions sessions
            LEFT JOIN afup_sessions_vote_github asvg ON (asvg.session_id = sessions.session_id AND asvg.user = :user)
            WHERE plannifie = 0 AND id_forum = :event
            ORDER BY RAND(:randomSeed)
            LIMIT ' . ((int) $page - 1)*$limit . ', ' . ((int) $limit + 1)
        )->setParams(['event' => $event->getId(), 'user' => $user->getId(), 'randomSeed' => $randomSeed]);

        return $query->query();
    }

    public function getTalkOfTheDay(\DateTime $currentDate)
    {
        $query = $this
            ->getPreparedQuery(
            'SELECT afup_sessions.*
            FROM afup_sessions
            WHERE plannifie = 1 and LENGTH(youtube_id) > 0  AND (afup_sessions.date_publication < NOW() OR afup_sessions.date_publication IS NULL)
            AND id_forum IN (
              SELECT id
              FROM afup_forum
              WHERE date_debut > DATE_SUB(NOW(), INTERVAL 2 YEAR)
            )
            ORDER BY RAND(:randomSeed)
            LIMIT 1
            ')
            ->setParams(['randomSeed' => md5($currentDate->format('Y-m-d'))])
        ;

        return $query->query($this->getCollection(new HydratorSingleObject()))->first();
    }

    /**
     * Retrieve all talks with ratings from current user if applicable
     * It retrieve $limit + 1 row. So if `count($results) <= $limit` there is no more result.
     * Otherwise you should add a "next" item on your paginator
     *
     * @param int $randomSeed used to create a consistent random
     * @param int $page starting from 1
     * @param int $limit
     * @return CollectionInterface
     */
    public function getNewTalksToRate(Event $event, GithubUser $user, $randomSeed, $page = 1, $limit = 10)
    {
        $query = $this->getPreparedQuery(
            'SELECT sessions.session_id, titre, skill, genre, abstract, id_forum
            FROM afup_sessions sessions
            LEFT JOIN afup_sessions_vote_github asvg ON (asvg.session_id = sessions.session_id AND asvg.user = :user)
            WHERE plannifie = 0 AND id_forum = :event
            AND asvg.id IS NULL AND sessions.session_id NOT IN (
                SELECT sessions.session_id
                FROM afup_sessions sessions
                LEFT JOIN afup_conferenciers_sessions cs ON cs.session_id = sessions.session_id
                WHERE id_forum = :excluded_event AND cs.conferencier_id = :excluded_user
            )
            ORDER BY RAND(:randomSeed)
            LIMIT ' . ((int) $page - 1)*$limit . ', ' . ((int) $limit + 1)
        )->setParams([
            'event' => $event->getId(),
            'user' => $user->getId(),
            'excluded_event' => $event->getId(),
            'excluded_user' => $user->getId(),
            'randomSeed' => $randomSeed
        ]);

        return $query->query();
    }

    /**
     * @return CollectionInterface
     */
    public function getByTalkWithSpeakers(Talk $talk)
    {
        $hydrator = new JoinHydrator();
        $hydrator->aggregateOn('talk', 'speaker', 'getId');

        $query = $this->getPreparedQuery(
            'SELECT talk.session_id, talk.titre, skill, genre, abstract, talk.plannifie,
            speaker.conferencier_id, speaker.nom, speaker.prenom, speaker.id_forum, speaker.photo, speaker.societe, speaker.biographie,
            planning.debut, planning.fin, room.id, room.nom, event.date_annonce_planning
            FROM afup_sessions AS talk
            LEFT JOIN afup_conferenciers_sessions acs ON acs.session_id = talk.session_id
            LEFT JOIN afup_conferenciers speaker ON speaker.conferencier_id = acs.conferencier_id
            LEFT JOIN afup_forum_planning planning ON planning.id_session = talk.session_id
            LEFT JOIN afup_forum_salle room ON planning.id_salle = room.id
            LEFT JOIN afup_forum event ON talk.id_forum = event.id
            WHERE talk.session_id = :talk AND plannifie = 1 AND (talk.date_publication < NOW() OR talk.date_publication IS NULL)
            ORDER BY planning.debut ASC, room.id ASC, talk.session_id ASC '
        )->setParams(['talk' => $talk->getId()]);

        return $query->query($this->getCollection($hydrator));
    }

    /**
     * @param bool $applyPublicationdateFilters
     *
     * @return CollectionInterface&iterable<array{talk: Talk, speaker: Speaker, room: mixed, planning: mixed, ".aggregation": array<string, mixed>}>
     * @throws QueryException
     */
    public function getByEventWithSpeakers(Event $event, $applyPublicationdateFilters = true)
    {
        return $this->getByEventsWithSpeakers([$event], $applyPublicationdateFilters);
    }

    /**
     * @param list<Event> $events
     * @param bool $applyPublicationdateFilters
     *
     * @return CollectionInterface&iterable<array{talk: Talk, speaker: Speaker, room: mixed, planning: mixed, ".aggregation": array<string, mixed>}>
     * @throws QueryException
     */
    public function getByEventsWithSpeakers(array $events, $applyPublicationdateFilters = true)
    {
        $hydrator = new JoinHydrator();
        $hydrator->aggregateOn('talk', 'speaker', 'getId');

        $publicationdateFilters = '';
        if ($applyPublicationdateFilters) {
            $publicationdateFilters = 'AND (talk.date_publication < NOW() OR talk.date_publication IS NULL)';
        }

        $params = [];

        $inEventsKeys = [];
        $cpt = 0;
        foreach ($events as $event) {
            $cpt++;
            $key = 'event_id_' . $cpt;
            $inEventsKeys[] = ':' . $key;
            $params[$key] = $event->getId();
        }

        $inEvents = implode(',', $inEventsKeys);

        $query = $this->getPreparedQuery(
            sprintf('SELECT talk.id_forum, talk.session_id, titre, skill, genre, abstract, talk.plannifie, talk.language_code,
            talk.joindin,
            speaker.conferencier_id, speaker.nom, speaker.prenom, speaker.id_forum, speaker.photo, speaker.societe,
            planning.debut, planning.fin, room.id, room.nom
            FROM afup_sessions AS talk
            LEFT JOIN afup_conferenciers_sessions acs ON acs.session_id = talk.session_id
            LEFT JOIN afup_conferenciers speaker ON speaker.conferencier_id = acs.conferencier_id
            LEFT JOIN afup_forum_planning planning ON planning.id_session = talk.session_id
            LEFT JOIN afup_forum_salle room ON planning.id_salle = room.id
            WHERE talk.id_forum IN(%s) AND plannifie = 1 %s
            ORDER BY planning.debut ASC, room.id ASC, talk.session_id ASC ', $inEvents, $publicationdateFilters)
        )->setParams($params);

        return $query->query($this->getCollection($hydrator));
    }


    /**
     * @return CollectionInterface
     * @throws QueryException
     */
    public function getAllByEventWithSpeakers(Event $event)
    {
        $hydrator = new JoinHydrator();
        $hydrator->aggregateOn('talk', 'speaker', 'getId');

        $query = $this->getPreparedQuery(
            'SELECT talk.session_id, titre, skill, genre, abstract, talk.plannifie, talk.language_code, talk.needs_mentoring, talk.staff_notes, talk.youtube_id,
            speaker.conferencier_id, speaker.nom, speaker.prenom, speaker.id_forum, speaker.photo, speaker.ville, speaker.societe, speaker.email, speaker.conferencier_id
            FROM afup_sessions AS talk
            LEFT JOIN afup_conferenciers_sessions acs ON acs.session_id = talk.session_id
            LEFT JOIN afup_conferenciers speaker ON speaker.conferencier_id = acs.conferencier_id
            WHERE talk.id_forum = :event
            ORDER BY talk.session_id ASC '
        )->setParams(['event' => $event->getId()]);

        return $query->query($this->getCollection($hydrator));
    }

    public function getAllPastTalks(\DateTime $dateTime)
    {
        $query = $this->getPreparedQuery(
            '
            SELECT talk.*
            FROM afup_sessions AS talk
            LEFT JOIN afup_forum_planning afp ON talk.session_id = afp.id_session
            WHERE afp.fin <= :date_fin'
        )->setParams(['date_fin' => $dateTime->format('U')]);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * @param array<int> $talkIds
     * @return CollectionInterface<Talk>
     */
    public function findList(array $talkIds): CollectionInterface
    {
        /** @var SelectInterface $qb */
        $qb = $this->getQueryBuilder(self::QUERY_SELECT);

        $placeholders = [];
        $parameters = [];
        foreach ($talkIds as $index => $id) {
            $placeholders[] = ":id{$index}";
            $parameters["id{$index}"] = $id;
        }


        $qb->from('afup_sessions')
            ->cols(['*'])
            ->where("session_id IN (" . implode(', ', $placeholders) . ")");

        foreach ($parameters as $placeholder => $value) {
            $qb->bindValue($placeholder, $value);
        }

        return $this
            ->getQuery($qb->getStatement())
            ->setParams($qb->getBindValues())
            ->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
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
                'columnName' => 'staff_notes',
                'fieldName' => 'staffNotes',
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
                'columnName' => 'with_workshop',
                'fieldName' => 'withWorkshop',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'workshop_abstract',
                'fieldName' => 'workshopAbstract',
                'type' => 'string'
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
            ->addField([
                'columnName' => 'youtube_id',
                'fieldName' => 'youtubeId',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'video_has_fr_subtitles',
                'fieldName' => 'videoHasFrSubtitles',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'video_has_en_subtitles',
                'fieldName' => 'videoHasEnSubtitles',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'slides_url',
                'fieldName' => 'slidesUrl',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'blog_post_url',
                'fieldName' => 'blogPostUrl',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'interview_url',
                'fieldName' => 'interviewUrl',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'joindin',
                'fieldName' => 'joindinId',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'openfeedback_path',
                'fieldName' => 'openfeedbackPath',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'language_code',
                'fieldName' => 'languageCode',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'tweets',
                'fieldName' => 'tweets',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'transcript',
                'fieldName' => 'transcript',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'verbatim',
                'fieldName' => 'verbatim',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'markdown',
                'fieldName' => 'useMarkdown',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'has_allowed_to_sharing_with_local_offices',
                'fieldName' => 'hasAllowedToSharingWithLocalOffices',
                'type' => 'bool',
                'serializer' => Boolean::class
            ]);

        return $metadata;
    }
}
