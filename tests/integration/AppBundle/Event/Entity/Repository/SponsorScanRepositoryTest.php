<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\Repository\SponsorScanRepository;
use AppBundle\Event\Entity\SponsorScan;
use AppBundle\Event\Model\SponsorTicket;
use Doctrine\DBAL\Connection;

final class SponsorScanRepositoryTest extends IntegrationTestCase
{
    private const SPONSOR_TICKET_ID = 10;

    public function testGetBySponsorTicketReturnsActiveScansWithTicketInfo(): void
    {
        $repository = self::getContainer()->get(SponsorScanRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $ticketId1 = $this->insertInscription($connection, 'Dupont', 'Pierre', 'pierre.dupont@example.com');
        $ticketId2 = $this->insertInscription($connection, 'Martin', 'Claire', 'claire.martin@example.com');

        $this->insertScan($connection, $ticketId1, '2026-06-01 10:00:00');
        $this->insertScan($connection, $ticketId2, '2026-06-02 10:00:00');
        // Un scan supprimé et un scan d'un autre ticket sponsor ne doivent pas remonter
        $deletedScanId = $this->insertScan($connection, $ticketId1, '2026-06-03 10:00:00');
        $connection->update('afup_forum_sponsor_scan', ['deleted_on' => '2026-06-04 10:00:00'], ['id' => $deletedScanId]);
        $this->insertScan($connection, $ticketId2, '2026-06-04 10:00:00', 20);

        $scans = $repository->getBySponsorTicket($this->buildSponsorTicket(self::SPONSOR_TICKET_ID));

        self::assertCount(2, $scans);
        // Tri par date de création décroissante
        self::assertSame('claire.martin@example.com', $scans[0]['email']);
        self::assertSame('pierre.dupont@example.com', $scans[1]['email']);
        self::assertSame('Martin', $scans[0]['nom']);
        self::assertSame('Claire', $scans[0]['prenom']);
        self::assertSame('2026-06-02 10:00:00', $scans[0]['created_on']);
        self::assertArrayHasKey('id', $scans[0]);
    }

    public function testFindOneBySponsorTicketAndTicket(): void
    {
        $repository = self::getContainer()->get(SponsorScanRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $ticketId = $this->insertInscription($connection, 'Dupont', 'Pierre', 'pierre.dupont@example.com');
        $scanId = $this->insertScan($connection, $ticketId, '2026-06-01 10:00:00');

        $scan = $repository->findOneBy(['sponsorTicketId' => self::SPONSOR_TICKET_ID, 'ticketId' => $ticketId]);

        self::assertInstanceOf(SponsorScan::class, $scan);
        self::assertSame($scanId, $scan->id);
        self::assertSame($ticketId, $scan->ticketId);
        self::assertSame(self::SPONSOR_TICKET_ID, $scan->sponsorTicketId);
        self::assertNull($scan->deletedOn);

        self::assertNull($repository->findOneBy(['sponsorTicketId' => self::SPONSOR_TICKET_ID, 'ticketId' => 999]));
    }

    public function testSoftDeleteIsPersistedAndExcludedFromList(): void
    {
        $repository = self::getContainer()->get(SponsorScanRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $ticketId = $this->insertInscription($connection, 'Dupont', 'Pierre', 'pierre.dupont@example.com');
        $this->insertScan($connection, $ticketId, '2026-06-01 10:00:00');

        $scan = $repository->findOneBy(['sponsorTicketId' => self::SPONSOR_TICKET_ID, 'ticketId' => $ticketId]);
        self::assertInstanceOf(SponsorScan::class, $scan);

        // Suppression logique : le scan reste en base mais n'apparait plus dans la liste
        $scan->deletedOn = new \DateTimeImmutable('now');
        $repository->save($scan);

        self::assertNotNull($repository->findOneBy(['sponsorTicketId' => self::SPONSOR_TICKET_ID, 'id' => $scan->id]));
        self::assertSame([], $repository->getBySponsorTicket($this->buildSponsorTicket(self::SPONSOR_TICKET_ID)));
    }

    private function buildSponsorTicket(int $id): SponsorTicket
    {
        $sponsorTicket = new SponsorTicket();
        $sponsorTicket->setId($id);

        return $sponsorTicket;
    }

    private function insertInscription(Connection $connection, string $nom, string $prenom, string $email): int
    {
        $connection->insert('afup_inscription_forum', [
            'reference' => 'REF-' . uniqid(),
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'id_forum' => 42,
            'etat' => 0,
        ]);

        return (int) $connection->lastInsertId();
    }

    private function insertScan(Connection $connection, int $ticketId, string $createdOn, int $sponsorTicketId = self::SPONSOR_TICKET_ID): int
    {
        $connection->insert('afup_forum_sponsor_scan', [
            'sponsor_ticket_id' => $sponsorTicketId,
            'ticket_id' => $ticketId,
            'created_on' => $createdOn,
        ]);

        return (int) $connection->lastInsertId();
    }
}
