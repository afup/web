<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Event\Model\Talk;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Webmozart\Assert\Assert;

/**
 * Les talks publiés tels que les montrent toutes les pages publiques (talk, speakers, événements, hub), l'index
 * Algolia et le sitemap, lus en DBAL.
 *
 * Un talk est publié s'il est planifié, n'est ni une clinique ni une présentation des speakers, si son créneau
 * est terminé et si sa date de publication est vide ou passée : afup.org ne montre que les talks passés.
 */
readonly class PublishedTalkRepository
{
    private const string PLANNING_JOIN = '
        INNER JOIN (
            SELECT id_session, MIN(debut) AS debut, MIN(fin) AS fin, MIN(id_salle) AS id_salle FROM afup_forum_planning GROUP BY id_session
        ) planning ON planning.id_session = talk.session_id';

    private const string CONDITIONS = '
        talk.plannifie = 1
        AND talk.genre NOT IN (' . Talk::TYPE_CLINIC . ', ' . Talk::TYPE_SPEAKER_INTRODUCTIONS . ')
        AND planning.fin <= UNIX_TIMESTAMP()
        AND (talk.date_publication < NOW() OR talk.date_publication IS NULL)';

    /** Colonnes lues par self::talk() et self::event(). */
    private const string TALK_AND_EVENT_COLUMNS = 'talk.session_id, talk.titre, talk.youtube_id,
        event.id AS event_id, event.titre AS event_titre, event.path AS event_path, event.date_debut AS event_date_debut, event.date_fin AS event_date_fin';

    public function __construct(private Connection $connection) {}

    /**
     * @return list<int>
     */
    public function getIds(): array
    {
        return array_map(self::toInt(...), $this->connection->fetchFirstColumn(
            'SELECT talk.session_id FROM afup_sessions talk'
            . self::PLANNING_JOIN . '
            WHERE ' . self::CONDITIONS . '
            ORDER BY talk.session_id',
        ));
    }

    public function isPublished(int $talkId): bool
    {
        return false !== $this->connection->fetchOne(
            'SELECT 1 FROM afup_sessions talk'
            . self::PLANNING_JOIN . '
            WHERE talk.session_id = :talkId AND ' . self::CONDITIONS,
            ['talkId' => $talkId],
        );
    }

    /**
     * Les lignes speaker rattachées à une personne qui ont un talk publié, toutes éditions confondues,
     * une ligne par couple speaker / talk : de quoi construire l'index des personnes.
     * Les lignes sans personne sont écartées.
     *
     * @return list<array{speaker_id: int, personne_id: int, slug: string, firstname: string, lastname: string, talk_id: int, starts_at: int}>
     */
    public function getSpeakerAppearances(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT speaker.conferencier_id, personne.id AS personne_id, personne.slug, speaker.prenom, speaker.nom,
            talk.session_id, planning.debut
            FROM afup_conferenciers speaker
            INNER JOIN afup_conferenciers_personnes personne ON personne.id = speaker.personne_id
            INNER JOIN afup_conferenciers_sessions cs ON cs.conferencier_id = speaker.conferencier_id
            INNER JOIN afup_sessions talk ON talk.session_id = cs.session_id'
            . self::PLANNING_JOIN . '
            WHERE ' . self::CONDITIONS,
        );

        return array_map(static fn(array $row): array => [
            'speaker_id' => self::toInt($row['conferencier_id']),
            'personne_id' => self::toInt($row['personne_id']),
            'slug' => self::toString($row['slug']),
            'firstname' => self::nullableString($row['prenom']) ?? '',
            'lastname' => self::nullableString($row['nom']) ?? '',
            'talk_id' => self::toInt($row['session_id']),
            'starts_at' => self::toInt($row['debut']),
        ], $rows);
    }

    /**
     * Les talks publiés d'une édition dans l'ordre du planning (créneau, salle), avec leurs speakers.
     *
     * @return list<EventTalk>
     */
    public function getEventTalks(int $eventId): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT talk.session_id, talk.titre, talk.youtube_id, planning.debut,
            speaker.conferencier_id, speaker.prenom, speaker.nom, speaker.personne_id
            FROM afup_sessions talk'
            . self::PLANNING_JOIN . '
            LEFT JOIN afup_conferenciers_sessions cs ON cs.session_id = talk.session_id
            LEFT JOIN afup_conferenciers speaker ON speaker.conferencier_id = cs.conferencier_id
            WHERE talk.id_forum = :eventId AND ' . self::CONDITIONS . '
            ORDER BY planning.debut ASC, planning.id_salle ASC, talk.session_id ASC, speaker.conferencier_id ASC',
            ['eventId' => $eventId],
        );

        /** @var array<int, array{talk: PublishedTalk, starts_at: \DateTimeImmutable, speakers: list<PublishedSpeaker>}> $talks */
        $talks = [];
        foreach ($rows as $row) {
            $talkId = self::toInt($row['session_id']);
            $talks[$talkId] ??= ['talk' => self::talk($row), 'starts_at' => self::timestamp($row['debut']), 'speakers' => []];
            if (null !== $row['conferencier_id']) {
                $talks[$talkId]['speakers'][] = self::speaker($row);
            }
        }

        return array_values(array_map(
            static fn(array $talk): EventTalk => new EventTalk($talk['talk'], $talk['starts_at'], $talk['speakers']),
            $talks,
        ));
    }

    /**
     * Les éditions qui ont au moins un talk publié, de la plus récente à la plus ancienne, avec le nombre de
     * talks publiés et de vidéos, en une seule requête agrégée.
     *
     * @return list<EventTalksSummary>
     */
    public function getEventSummaries(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            "SELECT event.id AS event_id, event.titre AS event_titre, event.path AS event_path,
            event.date_debut AS event_date_debut, event.date_fin AS event_date_fin,
            COUNT(talk.session_id) AS talk_count,
            SUM(talk.youtube_id IS NOT NULL AND talk.youtube_id <> '') AS video_count
            FROM afup_sessions talk"
            . self::PLANNING_JOIN . '
            INNER JOIN afup_forum event ON event.id = talk.id_forum
            WHERE ' . self::CONDITIONS . '
            GROUP BY event.id
            ORDER BY event.date_debut DESC, event.id DESC',
        );

        return array_map(static fn(array $row): EventTalksSummary => new EventTalksSummary(
            self::event($row),
            self::toInt($row['talk_count']),
            self::toInt($row['video_count'] ?? 0),
        ), $rows);
    }

    /**
     * Les talks publiés de ces lignes speaker, avec leur événement, du plus récent au plus ancien.
     *
     * @param list<int> $speakerIds
     *
     * @return list<array{speaker_id: int, talk: PublishedTalk, event: PublishedEvent, starts_at: \DateTimeImmutable, github_login: string|null}>
     */
    public function getSpeakerTalks(array $speakerIds): array
    {
        if ($speakerIds === []) {
            return [];
        }

        $rows = $this->connection->fetchAllAssociative(
            'SELECT speaker.conferencier_id, github.login AS github_login, planning.debut, ' . self::TALK_AND_EVENT_COLUMNS . '
            FROM afup_conferenciers speaker
            INNER JOIN afup_conferenciers_sessions cs ON cs.conferencier_id = speaker.conferencier_id
            INNER JOIN afup_sessions talk ON talk.session_id = cs.session_id'
            . self::PLANNING_JOIN . '
            INNER JOIN afup_forum event ON event.id = talk.id_forum
            LEFT JOIN afup_user_github github ON github.id = speaker.user_github
            WHERE speaker.conferencier_id IN (:speakerIds)
            AND ' . self::CONDITIONS . '
            ORDER BY planning.debut DESC, talk.session_id DESC, speaker.conferencier_id DESC',
            ['speakerIds' => $speakerIds],
            ['speakerIds' => ArrayParameterType::INTEGER],
        );

        return array_map(static fn(array $row): array => [
            'speaker_id' => self::toInt($row['conferencier_id']),
            'talk' => self::talk($row),
            'event' => self::event($row),
            'starts_at' => self::timestamp($row['debut']),
            'github_login' => null !== $row['github_login'] ? self::toString($row['github_login']) : null,
        ], $rows);
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function talk(array $row): PublishedTalk
    {
        return new PublishedTalk(
            self::toInt($row['session_id']),
            self::toString($row['titre']),
            self::nullableString($row['youtube_id']),
        );
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function speaker(array $row): PublishedSpeaker
    {
        return new PublishedSpeaker(
            self::toInt($row['conferencier_id']),
            self::toString($row['prenom'] ?? ''),
            self::toString($row['nom'] ?? ''),
            null !== $row['personne_id'] ? self::toInt($row['personne_id']) : null,
        );
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function event(array $row): PublishedEvent
    {
        return new PublishedEvent(
            self::toInt($row['event_id']),
            self::toString($row['event_titre']),
            self::toString($row['event_path']),
            null !== $row['event_date_debut'] ? new \DateTimeImmutable(self::toString($row['event_date_debut'])) : null,
            null !== $row['event_date_fin'] ? new \DateTimeImmutable(self::toString($row['event_date_fin'])) : null,
        );
    }

    private static function timestamp(mixed $value): \DateTimeImmutable
    {
        return new \DateTimeImmutable('@' . self::toInt($value))->setTimezone(new \DateTimeZone(date_default_timezone_get()));
    }

    private static function toInt(mixed $value): int
    {
        Assert::integerish($value);

        return (int) $value;
    }

    private static function toString(mixed $value): string
    {
        Assert::scalar($value);

        return (string) $value;
    }

    private static function nullableString(mixed $value): ?string
    {
        return null !== $value && '' !== $value ? self::toString($value) : null;
    }
}
