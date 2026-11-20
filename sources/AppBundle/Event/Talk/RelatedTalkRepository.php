<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Webmozart\Assert\Assert;

/**
 * Talks publiés liés à la page d'un talk (cf. RelatedTalks).
 *
 * Un talk publié est planifié, visible dans l'historique (ni clinique, ni présentation des
 * speakers, cf. Talk::isDisplayedOnHistory()), a un créneau (sans créneau, sa page répond 404)
 * et sa date de publication est passée.
 *
 * Les requêtes renvoient des identifiants ; les talks, éditions et speakers sont ensuite chargés
 * par leurs repositories.
 */
final readonly class RelatedTalkRepository
{
    /**
     * Créneau le plus tôt de chaque talk : un talk planifié sur plusieurs créneaux n'est renvoyé qu'une fois.
     */
    private const string PLANNING = '(SELECT id_session, MIN(debut) AS debut FROM afup_forum_planning GROUP BY id_session)';

    private const string PUBLISHED = 'talk.plannifie = 1
        AND talk.genre NOT IN (:hiddenTypes)
        AND (talk.date_publication < NOW() OR talk.date_publication IS NULL)';

    public function __construct(
        private Connection $connection,
        private TalkRepository $talkRepository,
        private EventRepository $eventRepository,
        private SpeakerRepository $speakerRepository,
    ) {}

    /**
     * Talks publiés d'une édition, dans l'ordre du planning.
     *
     * @return list<Talk>
     */
    public function getPublishedByEventInPlanningOrder(Event $event): array
    {
        $talkIds = $this->connection->executeQuery(
            'SELECT talk.session_id
            FROM afup_sessions AS talk
            INNER JOIN ' . self::PLANNING . ' AS planning ON planning.id_session = talk.session_id
            WHERE talk.id_forum = :event AND ' . self::PUBLISHED . '
            ORDER BY planning.debut ASC, talk.session_id ASC',
            ['event' => $event->getId(), 'hiddenTypes' => self::hiddenTypes()],
            ['hiddenTypes' => ArrayParameterType::INTEGER],
        )->fetchFirstColumn();
        Assert::allIntegerish($talkIds);

        return array_values($this->loadTalks(array_map(intval(...), $talkIds)));
    }

    /**
     * Talks publiés, toutes éditions confondues, des personnes qui ont présenté ces speakers,
     * du plus récent au plus ancien.
     *
     * Un speaker étant une ligne par édition, la même personne est retrouvée par son
     * compte GitHub ou, à défaut, par son prénom et son nom.
     *
     * @param list<Speaker> $speakers
     *
     * @return list<array{talk: Talk, event: Event, speakers: list<Speaker>}> les speakers de chaque talk qui correspondent à l'un de ceux demandés
     */
    public function getPublishedBySpeakers(array $speakers, Talk $excludedTalk): array
    {
        $conditions = [];
        $params = ['excluded' => $excludedTalk->getId(), 'hiddenTypes' => self::hiddenTypes()];
        foreach ($speakers as $index => $speaker) {
            $conditions[] = sprintf('(LOWER(TRIM(speaker.prenom)) = :firstname%1$d AND LOWER(TRIM(speaker.nom)) = :lastname%1$d)', $index);
            $params['firstname' . $index] = mb_strtolower(trim((string) $speaker->getFirstname()));
            $params['lastname' . $index] = mb_strtolower(trim((string) $speaker->getLastname()));

            if ($speaker->getUser()) {
                $conditions[] = sprintf('speaker.user_github = :github%d', $index);
                $params['github' . $index] = $speaker->getUser();
            }
        }

        if ($conditions === []) {
            return [];
        }

        $rows = $this->connection->executeQuery(
            sprintf('SELECT talk.session_id AS talk_id, talk.id_forum AS event_id, speaker.conferencier_id AS speaker_id
            FROM afup_sessions AS talk
            INNER JOIN ' . self::PLANNING . ' AS planning ON planning.id_session = talk.session_id
            INNER JOIN afup_conferenciers_sessions acs ON acs.session_id = talk.session_id
            INNER JOIN afup_conferenciers speaker ON speaker.conferencier_id = acs.conferencier_id
            WHERE talk.session_id != :excluded AND ' . self::PUBLISHED . '
            AND (%s)
            ORDER BY planning.debut DESC, talk.session_id DESC, speaker.conferencier_id ASC', implode(' OR ', $conditions)),
            $params,
            ['hiddenTypes' => ArrayParameterType::INTEGER],
        )->fetchAllAssociative();

        // Par talk, dans l'ordre de la requête : l'édition et les speakers trouvés.
        $matches = [];
        foreach ($rows as $row) {
            Assert::integerish($row['talk_id']);
            Assert::integerish($row['event_id']);
            Assert::integerish($row['speaker_id']);
            $matches[(int) $row['talk_id']]['event'] = (int) $row['event_id'];
            $matches[(int) $row['talk_id']]['speakers'][] = (int) $row['speaker_id'];
        }

        $talks = $this->loadTalks(array_keys($matches));
        $events = [];
        $talksWithSpeakers = [];
        foreach ($talks as $talkId => $talk) {
            $eventId = $matches[$talkId]['event'];
            $event = $events[$eventId] ??= $this->eventRepository->get($eventId);
            $talkSpeakers = array_values(array_filter(array_map($this->speakerRepository->get(...), $matches[$talkId]['speakers'])));

            if ($event instanceof Event && $talkSpeakers !== []) {
                $talksWithSpeakers[] = ['talk' => $talk, 'event' => $event, 'speakers' => $talkSpeakers];
            }
        }

        return $talksWithSpeakers;
    }

    /**
     * @param list<int> $talkIds
     *
     * @return array<int, Talk> les talks indexés par identifiant, dans l'ordre des identifiants donnés
     */
    private function loadTalks(array $talkIds): array
    {
        if ($talkIds === []) {
            return [];
        }

        $talksById = [];
        foreach ($this->talkRepository->findList($talkIds) as $talk) {
            $talksById[$talk->getId() ?? 0] = $talk;
        }

        $talks = [];
        foreach ($talkIds as $talkId) {
            if (isset($talksById[$talkId])) {
                $talks[$talkId] = $talksById[$talkId];
            }
        }

        return $talks;
    }

    /**
     * @return list<int>
     */
    private static function hiddenTypes(): array
    {
        return [Talk::TYPE_CLINIC, Talk::TYPE_SPEAKER_INTRODUCTIONS];
    }
}
