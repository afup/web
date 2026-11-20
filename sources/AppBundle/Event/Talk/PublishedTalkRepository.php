<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Event\Model\Talk;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Webmozart\Assert\Assert;

/**
 * Les talks publiés tels que les montrent les pages publiques (speakers, événements, hub), lus en DBAL.
 *
 * Un talk est publié s'il est planifié, n'est ni une clinique ni une présentation des speakers (l'équivalent
 * SQL de Talk::isDisplayedOnHistory(), le filtre de la page du talk), si son créneau est terminé et si sa date
 * de publication est vide ou passée.
 */
readonly class PublishedTalkRepository
{
    private const string PLANNING_JOIN = '
        INNER JOIN (
            SELECT id_session, MIN(debut) AS debut, MIN(fin) AS fin FROM afup_forum_planning GROUP BY id_session
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
