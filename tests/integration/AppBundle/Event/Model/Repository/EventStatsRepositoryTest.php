<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Model\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Model\EventStats\SpecialPriceStats;
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
        $connection->insert('afup_forum_billeterie_privee', [
            'id_forum' => 42,
            'nom' => 'Billetterie de test',
            'token' => 'TOKEN-STATS-1',
            'mot_de_passe' => 'nope',
            'id_tarif' => Ticket::TYPE_SPECIAL_PRICE,
            'prix' => 80.0,
            'max_places' => 10,
            'date_debut' => '2020-01-01 00:00:00',
            'date_fin' => '2099-12-31 23:59:59',
            'created_on' => '2020-01-01 00:00:00',
        ]);
        $this->insertInscription($connection, 'TOKEN-STATS-1', 'STATS-A', 42, Ticket::STATUS_PAID, 80.0);
        $this->insertInscription($connection, 'TOKEN-STATS-1', 'STATS-B', 42, Ticket::STATUS_WAITING, 80.0);
        // Une place annulée ne doit pas être comptée
        $this->insertInscription($connection, 'TOKEN-STATS-1', 'STATS-C', 42, Ticket::STATUS_CANCELLED, 80.0);
        // Une place via un token visiteur (billetterie distincte)
        $this->insertInscription($connection, 'TOKEN-STATS-V', 'STATS-V', 42, Ticket::STATUS_PAID, 100.0);

        $stats = $eventStatsRepository->getStatsForTicketTypes(42, null);

        self::assertSame(3, $stats->paying[Ticket::TYPE_SPECIAL_PRICE] ?? 0);
        self::assertSame(260.0, $stats->realAmounts[Ticket::TYPE_SPECIAL_PRICE] ?? 0.0);
        self::assertSame(2, $stats->specialPriceDistinctAmounts);

        self::assertSame(2, $stats->specialPrice->paying[SpecialPriceStats::BUCKET_BILLETTERIE_PRIVEE]);
        self::assertSame(2, $stats->specialPrice->registered[SpecialPriceStats::BUCKET_BILLETTERIE_PRIVEE]);
        self::assertSame(1, $stats->specialPrice->distinctAmounts[SpecialPriceStats::BUCKET_BILLETTERIE_PRIVEE]);
        self::assertSame(160.0, $stats->specialPrice->realAmounts[SpecialPriceStats::BUCKET_BILLETTERIE_PRIVEE]);
        self::assertSame(1, $stats->specialPrice->paying[SpecialPriceStats::BUCKET_TOKEN_VISITEUR]);
        self::assertSame(1, $stats->specialPrice->distinctAmounts[SpecialPriceStats::BUCKET_TOKEN_VISITEUR]);
        self::assertSame(100.0, $stats->specialPrice->realAmounts[SpecialPriceStats::BUCKET_TOKEN_VISITEUR]);
    }

    public function testSpecialPriceDistinctAmountsDetectsMultiplePrices(): void
    {
        $eventStatsRepository = self::getContainer()->get(EventStatsRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        // Deux tarifs spéciaux de prix différents (billetteries privées ou tokens visiteurs)
        $this->insertInscription($connection, 'TOKEN-STATS-A', 'STATS-D', 43, Ticket::STATUS_PAID, 80.0);
        $this->insertInscription($connection, 'TOKEN-STATS-B', 'STATS-E', 43, Ticket::STATUS_WAITING, 50.0);
        // Un tarif spécial annulé à un autre prix ne doit pas être compté
        $this->insertInscription($connection, 'TOKEN-STATS-C', 'STATS-F', 43, Ticket::STATUS_CANCELLED, 99.0);

        $stats = $eventStatsRepository->getStatsForTicketTypes(43, null);

        self::assertSame(2, $stats->specialPriceDistinctAmounts);
        self::assertSame(130.0, $stats->realAmounts[Ticket::TYPE_SPECIAL_PRICE] ?? 0.0);
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
