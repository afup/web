<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Event\Model\Talk;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

final readonly class LatestVideoRepository
{
    public function __construct(private Connection $connection) {}

    /**
     * Derniers talks publiés avec une vidéo, du créneau terminé le plus récent au plus ancien.
     * « Publié » reprend les règles de la page talk : talk planifié, affiché dans l'historique
     * (ni clinique, ni introduction des speakers), date de publication passée ou vide,
     * et tous ses créneaux de planning terminés.
     *
     * @return list<LatestVideo>
     */
    public function getLatest(\DateTimeInterface $now, int $limit): array
    {
        /** @var list<array{session_id: int|string, titre: string, youtube_id: string, event_title: string}> $rows */
        $rows = $this->connection->fetchAllAssociative(
            'SELECT talk.session_id, talk.titre, talk.youtube_id, event.titre AS event_title
            FROM afup_sessions AS talk
            INNER JOIN (
                SELECT id_session, MAX(fin) AS fin
                FROM afup_forum_planning
                GROUP BY id_session
            ) AS planning ON planning.id_session = talk.session_id
            INNER JOIN afup_forum AS event ON event.id = talk.id_forum
            WHERE talk.plannifie = 1
            AND talk.genre NOT IN (:clinic, :speakerIntroductions)
            AND LENGTH(talk.youtube_id) > 0
            AND (talk.date_publication IS NULL OR talk.date_publication < :now)
            AND planning.fin <= :nowTimestamp
            ORDER BY planning.fin DESC, talk.session_id DESC
            LIMIT :limit',
            [
                'clinic' => Talk::TYPE_CLINIC,
                'speakerIntroductions' => Talk::TYPE_SPEAKER_INTRODUCTIONS,
                'now' => $now->format('Y-m-d H:i:s'),
                'nowTimestamp' => $now->getTimestamp(),
                'limit' => $limit,
            ],
            ['limit' => ParameterType::INTEGER],
        );

        return array_map(
            static fn(array $row): LatestVideo => new LatestVideo(
                (int) $row['session_id'],
                // Slug construit par le modèle, pour rester identique à celui de la page talk
                (new Talk())->setTitle($row['titre'])->getSlug(),
                $row['titre'],
                $row['youtube_id'],
                $row['event_title'],
            ),
            $rows,
        );
    }
}
