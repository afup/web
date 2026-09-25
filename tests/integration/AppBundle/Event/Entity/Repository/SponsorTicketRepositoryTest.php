<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\Repository\SponsorTicketRepository;
use AppBundle\Event\Entity\SponsorTicket;

final class SponsorTicketRepositoryTest extends IntegrationTestCase
{
    public function testSaveFindAndFindByEventId(): void
    {
        $sponsorTicketRepository = self::getContainer()->get(SponsorTicketRepository::class);

        $sponsorTicketRepository->save($this->buildSponsorTicket(42, 'Société ABC', 'TOKEN-ABC'));
        $societeXyz = $this->buildSponsorTicket(42, 'Société XYZ', 'TOKEN-XYZ');
        $sponsorTicketRepository->save($societeXyz);
        $sponsorTicketRepository->save($this->buildSponsorTicket(43, 'Société Autre', 'TOKEN-AUTRE'));

        $fromEvent = $sponsorTicketRepository->findByEventId(42);
        self::assertCount(2, $fromEvent);
        self::assertSame('Société ABC', $fromEvent[0]->societe);
        self::assertSame('Société XYZ', $fromEvent[1]->societe);

        $found = $sponsorTicketRepository->find($societeXyz->id);
        self::assertInstanceOf(SponsorTicket::class, $found);
        self::assertSame('Société XYZ', $found->societe);

        $byToken = $sponsorTicketRepository->findOneBy(['token' => 'TOKEN-ABC']);
        self::assertInstanceOf(SponsorTicket::class, $byToken);
        self::assertSame(5, $byToken->maxInvitations);
        self::assertSame(2, $byToken->getPendingInvitations());
        self::assertNull($sponsorTicketRepository->findOneBy(['token' => 'TOKEN-INTROUVABLE']));

        $byToken->usedInvitations = 3;
        $sponsorTicketRepository->save($byToken);
        self::assertSame(2, $sponsorTicketRepository->find($byToken->id)->getPendingInvitations());

        $id = $byToken->id;
        $sponsorTicketRepository->delete($byToken);
        self::assertNull($sponsorTicketRepository->find($id));
    }

    private function buildSponsorTicket(int $eventId, string $societe, string $token): SponsorTicket
    {
        $now = new \DateTimeImmutable();

        $sponsorTicket = new SponsorTicket();
        $sponsorTicket->eventId = $eventId;
        $sponsorTicket->societe = $societe;
        $sponsorTicket->token = $token;
        $sponsorTicket->contactEmail = 'contact@example.com';
        $sponsorTicket->maxInvitations = 5;
        $sponsorTicket->usedInvitations = 3;
        $sponsorTicket->createdOn = $now;
        $sponsorTicket->editedOn = $now;
        $sponsorTicket->creatorId = 1;
        $sponsorTicket->qrCodesScannerAvailable = true;

        return $sponsorTicket;
    }
}
