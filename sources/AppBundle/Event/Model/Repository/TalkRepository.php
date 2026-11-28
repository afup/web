<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\TalkAggregateVote;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Entity\GithubUser;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\TalkAggregate;
use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Query\QueryException;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\Hydrator;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Talk>
 */
class TalkRepository extends Repository implements MetadataInitializer
{
    public function getNumberOfTalksByEvent(Event|int $event, ?\DateTime $since = null)
    {
        return $this->getNumberOfTalksByEventAndLanguage($event, null, $since);
    }

    public function getNumberOfTalksByEventAndLanguage(Event|int $event, $languageCode = null, ?\DateTime $since = null)
    {
        if ($event instanceof Event) {
            $event = $event->getId();
        }

        $sql = 'SELECT COUNT(session_id) AS talks FROM afup_sessions WHERE id_forum = :event';
        $params = ['event' => $event];
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
    public function getTalksBySpeaker(Event $event, int $speakerId)
    {
        $query = $this->getPreparedQuery(
            'SELECT sessions.session_id, titre, abstract, id_forum, sessions.plannifie, skill, genre
            FROM afup_sessions sessions
            LEFT JOIN afup_conferenciers_sessions cs ON cs.session_id = sessions.session_id
            WHERE id_forum = :event AND cs.conferencier_id = :speaker
            ORDER BY titre
            LIMIT 0, 20
            ',
        )->setParams(['event' => $event->getId(), 'speaker' => $speakerId]);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * @return CollectionInterface
     */
    public function getPreviousTalksBySpeaker(Event $event, int $speakerId)
    {
        $query = $this->getPreparedQuery(
            'SELECT s.session_id, s.titre, s.abstract, s.id_forum, s.plannifie, s.skill, s.genre
            FROM afup_sessions s
            JOIN afup_conferenciers_sessions cs ON cs.session_id = s.session_id
            JOIN afup_conferenciers c ON cs.conferencier_id = c.conferencier_id
            WHERE s.id_forum != :event AND c.user_github IN (SELECT user_github FROM afup_conferenciers WHERE conferencier_id = :speaker) 
            ORDER BY s.titre ASC
            LIMIT 0, 50
            ',
        )->setParams(['event' => $event->getId(), 'speaker' => $speakerId]);

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
            'SELECT sessions.session_id, titre, abstract, skill, sessions.genre, id_forum
            FROM afup_sessions sessions
            WHERE plannifie = 0 AND id_forum = :event
            ORDER BY RAND(:randomSeed)
            LIMIT ' . ((int) $page - 1) * $limit . ', ' . ((int) $limit + 1),
        )->setParams(['event' => $event->getId(), 'user' => $user->getId(), 'randomSeed' => $randomSeed]);

        return $query->query();
    }

    public function getTalkOfTheDay(\DateTime $currentDate)
    {
        $query = $this->getPreparedQuery(
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
            ',
        )->setParams(['randomSeed' => md5($currentDate->format('Y-m-d'))]);

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
            'SELECT sessions.session_id, titre, skill, sessions.genre, abstract, id_forum
            FROM afup_sessions sessions
            LEFT JOIN afup_sessions_vote_github asvg ON (asvg.session_id = sessions.session_id AND asvg.user = :user)
            WHERE plannifie = 0 AND id_forum = :event
            AND asvg.id IS NULL AND sessions.session_id NOT IN (
                SELECT sessions.session_id
                FROM afup_sessions sessions
                INNER JOIN afup_conferenciers_sessions cs ON cs.session_id = sessions.session_id
                INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id
                WHERE sessions.id_forum = :excluded_event AND c.user_github = :excluded_user
            )
            ORDER BY RAND(:randomSeed)
            LIMIT ' . ((int) $page - 1) * $limit . ', ' . ((int) $limit + 1),
        )->setParams([
            'event' => $event->getId(),
            'user' => $user->getId(),
            'excluded_event' => $event->getId(),
            'excluded_user' => $user->getId(),
            'randomSeed' => $randomSeed,
        ]);

        return $query->query();
    }

    /**
     * La session, si elle est planifiée et publiée, avec son événement.
     *
     * @return array{talk: Talk, event: Event|null}|null
     */
    public function getPublishedScheduledWithEvent(Talk $talk): ?array
    {
        $query = $this->getPreparedQuery(
            // Les conférenciers, les salles et les plannings ne sont plus hydratés par Ting : ils sont
            // rechargés via Doctrine (SpeakerRepository, PlanningRepository).
            'SELECT talk.session_id, talk.titre, skill, talk.genre, abstract, talk.plannifie,
            event.date_annonce_planning, event.path, event.interviews_intro, event.interviews_cta_text
            FROM afup_sessions AS talk
            LEFT JOIN afup_forum event ON talk.id_forum = event.id
            WHERE talk.session_id = :talk AND plannifie = 1 AND (talk.date_publication < NOW() OR talk.date_publication IS NULL)',
        )->setParams(['talk' => $talk->getId()]);

        $row = $query->query($this->getCollection(new Hydrator()))->first();
        if (!is_array($row) || !($row['talk'] ?? null) instanceof Talk) {
            return null;
        }

        return [
            'talk' => $row['talk'],
            'event' => ($row['event'] ?? null) instanceof Event ? $row['event'] : null,
        ];
    }

    /**
     * @param bool $applyPublicationdateFilters
     * @param bool $orderByTheme
     *
     * @return array<TalkAggregate>
     * @throws QueryException
     */
    public function getByEventWithSpeakers(Event $event, bool $applyPublicationdateFilters = true, bool $orderByTheme = false, ?int $filterByThemeId = null): array
    {
        return $this->getByEventsWithSpeakers([$event], $applyPublicationdateFilters, $orderByTheme, $filterByThemeId);
    }

    /**
     * @param list<Event> $events
     * @param bool $applyPublicationdateFilters
     * @param bool $orderByTheme
     *
     * @return array<TalkAggregate>
     * @throws QueryException
     */
    public function getByEventsWithSpeakers(array $events, bool $applyPublicationdateFilters = true, bool $orderByTheme = false, ?int $filterByThemeId = null): array
    {
        $params = [];
        $publicationdateFilters = '';
        if ($applyPublicationdateFilters) {
            $publicationdateFilters = 'AND (talk.date_publication < NOW() OR talk.date_publication IS NULL)';
        }
        $themeFilters = '';
        if ($filterByThemeId) {
            $themeFilters = 'AND afup_conference_theme.id = :filterByThemeId';
            $params['filterByThemeId'] = $filterByThemeId;
        }

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
            sprintf('SELECT talk.id_forum, talk.session_id, titre, skill, talk.genre, abstract, talk.plannifie, talk.language_code,
            talk.joindin, talk.theme, talk.position
            FROM afup_sessions AS talk
            LEFT JOIN afup_forum_planning planning ON planning.id_session = talk.session_id
            LEFT JOIN afup_conference_theme ON afup_conference_theme.id = talk.theme
            WHERE talk.id_forum IN(%s) AND plannifie = 1 %s %s
            ORDER BY %s ', $inEvents, $publicationdateFilters, $themeFilters, $orderByTheme ? 'afup_conference_theme.priority ASC, afup_conference_theme.name ASC, talk.position IS NULL, talk.position ASC, talk.date_publication DESC, talk.session_id ASC' : 'talk.position IS NULL, talk.position ASC, planning.debut ASC, planning.id_salle ASC, talk.date_publication DESC, talk.session_id ASC '),
        )->setParams($params);

        $aggregates = [];
        foreach ($this->uniqueByTalk($query->query($this->getCollection(new Hydrator()))) as $row) {
            $aggregates[] = new TalkAggregate(
                $row['talk'],
                // Les conférenciers, les salles et les plannings ne sont plus hydratés par Ting : ils sont
                // rechargés via Doctrine avec PlanningRepository::enrichTalkAggregates().
                [],
                null,
                null,
            );
        }

        return $aggregates;
    }

    /**
     * @return array<TalkAggregate>
     * @throws QueryException
     */
    public function getByEventWithSpeakersAndVotes(Event $event, string $search = '', string $orderBy = 'talk.date_soumission ASC', bool $planned = false, bool $needMentoring = false): array
    {
        $sql = <<<SQL
            SELECT talk.id_forum, talk.session_id, titre, skill, talk.genre, abstract, talk.plannifie, talk.language_code,
            talk.joindin, talk.youtube_id, talk.slides_url, talk.interview_url, talk.blog_post_url, talk.needs_mentoring,
            talk.date_soumission,
            (SELECT AVG(vote) FROM afup_sessions_vote_github asvg WHERE asvg.session_id = talk.session_id) AS vote_note,
            (SELECT COUNT(vote) FROM afup_sessions_vote_github asvg WHERE asvg.session_id = talk.session_id) AS vote_total
            FROM afup_sessions AS talk
            LEFT JOIN afup_forum_planning planning ON planning.id_session = talk.session_id
            WHERE talk.id_forum = :id %s
            ORDER BY %s 
SQL;

        $params = [
            'id' => $event->getId(),
        ];

        $where = '';
        if ($search) {
            $where .= ' AND talk.titre LIKE :search';
            $params["search"] = "%{$search}%";
        }
        if ($planned) {
            $where .= ' AND talk.plannifie = 1';
        }
        if ($needMentoring) {
            $where .= ' AND talk.needs_mentoring = 1';
        }


        $query = $this
            ->getPreparedQuery(sprintf($sql, $where, $orderBy))
            ->setParams($params);

        $aggregates = [];
        foreach ($this->uniqueByTalk($query->query($this->getCollection(new Hydrator()))) as $row) {
            $aggregates[] = new TalkAggregate(
                $row['talk'],
                // Les conférenciers, les salles et les plannings ne sont plus hydratés par Ting : ils sont
                // rechargés via Doctrine avec PlanningRepository::enrichTalkAggregates().
                [],
                null,
                null,
                $row['values']->vote_note ? new TalkAggregateVote($row['values']->vote_note, $row['values']->vote_total) : null,
            );
        }

        return $aggregates;
    }

    /**
     * Toutes les sessions de l'événement, planifiées ou non, par id.
     *
     * @return CollectionInterface<Talk>&iterable<Talk>
     */
    public function getAllByEvent(Event $event): CollectionInterface
    {
        $query = $this->getPreparedQuery(
            'SELECT talk.session_id, titre, skill, talk.genre, abstract, talk.plannifie, talk.language_code, talk.needs_mentoring, talk.staff_notes, talk.youtube_id
            FROM afup_sessions AS talk
            WHERE talk.id_forum = :event
            ORDER BY talk.session_id ASC ',
        )->setParams(['event' => $event->getId()]);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * @return CollectionInterface<Talk>
     */
    public function getScheduledTalksByEvent(int $eventId): CollectionInterface
    {
        $query = $this->getPreparedQuery(
            'SELECT * FROM afup_sessions
            WHERE id_forum = :eventId AND plannifie = 1
            ORDER BY titre ASC',
        )->setParams(['eventId' => $eventId]);

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
     * Une jointure (sur les plannings par exemple) peut renvoyer plusieurs lignes par session :
     * seule la première de chaque session est gardée, à sa place.
     *
     * @param iterable<mixed> $rows
     *
     * @return list<array{talk: Talk, values: mixed}> values : les colonnes non mappées (sous-requêtes)
     */
    private function uniqueByTalk(iterable $rows): array
    {
        $unique = [];
        foreach ($rows as $row) {
            if (!is_array($row) || !($row['talk'] ?? null) instanceof Talk) {
                continue;
            }
            $unique[(int) $row['talk']->getId()] ??= ['talk' => $row['talk'], 'values' => $row[0] ?? null];
        }

        return array_values($unique);
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
                'primary' => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'id_forum',
                'fieldName' => 'forumId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'date_soumission',
                'fieldName' => 'submittedOn',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => false],
                ],
            ])
            ->addField([
                'columnName' => 'date_publication',
                'fieldName' => 'publishedOn',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => false],
                ],
            ])
            ->addField([
                'columnName' => 'titre',
                'fieldName' => 'title',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'abstract',
                'fieldName' => 'abstract',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'staff_notes',
                'fieldName' => 'staffNotes',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'genre',
                'fieldName' => 'type',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'skill',
                'fieldName' => 'skill',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'with_workshop',
                'fieldName' => 'withWorkshop',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'workshop_abstract',
                'fieldName' => 'workshopAbstract',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'plannifie',
                'fieldName' => 'scheduled',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'needs_mentoring',
                'fieldName' => 'needsMentoring',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'youtube_id',
                'fieldName' => 'youtubeId',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'video_has_fr_subtitles',
                'fieldName' => 'videoHasFrSubtitles',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'video_has_en_subtitles',
                'fieldName' => 'videoHasEnSubtitles',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'slides_url',
                'fieldName' => 'slidesUrl',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'blog_post_url',
                'fieldName' => 'blogPostUrl',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'interview_url',
                'fieldName' => 'interviewUrl',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'joindin',
                'fieldName' => 'joindinId',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'openfeedback_path',
                'fieldName' => 'openfeedbackPath',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'language_code',
                'fieldName' => 'languageCode',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'tweets',
                'fieldName' => 'tweets',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'bluesky_posts',
                'fieldName' => 'blueskyPosts',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'transcript',
                'fieldName' => 'transcript',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'verbatim',
                'fieldName' => 'verbatim',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'markdown',
                'fieldName' => 'useMarkdown',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'has_allowed_to_sharing_with_local_offices',
                'fieldName' => 'hasAllowedToSharingWithLocalOffices',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'theme',
                'fieldName' => 'theme',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'position',
                'fieldName' => 'position',
                'type' => 'int',
            ])
        ;

        return $metadata;
    }
}
