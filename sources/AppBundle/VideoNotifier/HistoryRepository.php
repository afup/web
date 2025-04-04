<?php

declare(strict_types=1);

namespace AppBundle\VideoNotifier;

use AppBundle\Doctrine\DoctrineConnection;
use AppBundle\Event\Model\Talk;
use Doctrine\DBAL\Query\QueryBuilder;

final class HistoryRepository
{
    private DoctrineConnection $connection;

    public function __construct(DoctrineConnection $connection)
    {
        $this->connection = $connection;
    }

    public function insert(HistoryEntry $entry): void
    {
        $this->connection->statement(
            fn (QueryBuilder $qb) => $qb
                ->insert('video_notifier_history')
                ->values([
                    'talk_id' => '?',
                    'status_id_bluesky' => '?',
                    'status_id_mastodon' => '?',
                ])
                ->setParameters([
                    $entry->getTalkId(),
                    $entry->getStatusIdBluesky(),
                    $entry->getStatusIdMastodon(),
                ]),
        );
    }

    /**
     * @param array<Talk> $talks
     * @return array<int, int>
     */
    public function getNumberOfStatusesPerTalk(array $talks): array
    {
        $results = $this->connection->mapMany(
            CountResult::class,
            fn (QueryBuilder $qb) => $qb
                ->from('video_notifier_history', 'h')
                ->select('h.talk_id', 'COUNT(h.id) AS quantity')
                ->where(
                    $qb->expr()->in('h.talk_id', array_map(fn (Talk $talk): ?int => $talk->getId(), $talks))
                )
                ->groupBy('h.talk_id')
        );

        $map = [];

        foreach ($results as $result) {
            $map[$result->talkId] = $result->quantity;
        }

        return $map;
    }
}
