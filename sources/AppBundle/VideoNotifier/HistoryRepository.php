<?php

declare(strict_types=1);

namespace AppBundle\VideoNotifier;

use AppBundle\Event\Model\Talk;
use Doctrine\DBAL\Connection;

final readonly class HistoryRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function insert(HistoryEntry $entry): void
    {
        $this->connection->createQueryBuilder()
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
            ])
            ->execute();
    }

    /**
     * @param array<Talk> $talks
     * @return array<int, int>
     */
    public function getNumberOfStatusesPerTalk(array $talks): array
    {
        $rows = ($qb = $this->connection->createQueryBuilder())
            ->from('video_notifier_history', 'h')
            ->select('h.talk_id', 'COUNT(h.id) AS quantity')
            ->where(
                $qb->expr()->in('h.talk_id', array_map(fn (Talk $talk): ?int => $talk->getId(), $talks))
            )
            ->groupBy('h.talk_id')
            ->execute()
            ->fetchAllAssociative();

        $map = [];

        foreach ($rows as $row) {
            $map[$row['talk_id']] = $row['quantity'];
        }

        return $map;
    }
}
