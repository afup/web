<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Model\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Ticket;
use Doctrine\DBAL\Connection;

final class EventStatsRepositoryTest extends IntegrationTestCase
{
    public function testRealAmountsIncludeBilleteriePriveeTickets(): void
    {
        $eventStatsRepository = self::getContainer()->get(EventStatsRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        // Deux places via une billetterie privée : une réglée, une en attente de règlement
        $this->insertInscription($connection, 'TOKEN-STATS-1', 'STATS-A', 42, Ticket::STATUS_PAID, 80.0);
        $this->insertInscription($connection, 'TOKEN-STATS-1', 'STATS-B', 42, Ticket::STATUS_WAITING, 80.0);
        // Une place annulée ne doit pas être comptée
        $this->insertInscription($connection, 'TOKEN-STATS-1', 'STATS-C', 42, Ticket::STATUS_CANCELLED, 80.0);

        $stats = $eventStatsRepository->getStatsForTicketTypes(42, null);

        self::assertSame(2, $stats->paying[Ticket::TYPE_SPECIAL_PRICE] ?? 0);
        self::assertSame(160.0, $stats->realAmounts[Ticket::TYPE_SPECIAL_PRICE] ?? 0.0);
    }

    private function insertInscription(
        Connection $connection,
        string $token,
        string $reference,
        int $forumId,
        int $etat,
        float $montant,
    ): void {
        $connection->insert('afup_inscription_forum', [
            'reference' => $reference,
            'special_price_token' => $token,
            'id_forum' => $forumId,
            'etat' => $etat,
            'type_inscription' => Ticket::TYPE_SPECIAL_PRICE,
            'montant' => $montant,
            'date' => time(),
        ]);
    }
}
