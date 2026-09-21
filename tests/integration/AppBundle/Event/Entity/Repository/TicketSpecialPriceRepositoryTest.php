<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\Repository\TicketSpecialPriceRepository;
use AppBundle\Event\Entity\TicketSpecialPrice;
use AppBundle\Event\Model\Ticket;
use Doctrine\DBAL\Connection;

final class TicketSpecialPriceRepositoryTest extends IntegrationTestCase
{
    private const int FORUM_ID = 42;

    private TicketSpecialPriceRepository $repository;

    private Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $repository = self::getContainer()->get(TicketSpecialPriceRepository::class);
        self::assertInstanceOf(TicketSpecialPriceRepository::class, $repository);
        $this->repository = $repository;

        $connection = self::getContainer()->get(Connection::class);
        self::assertInstanceOf(Connection::class, $connection);
        $this->connection = $connection;
    }

    public function testSaveEtFind(): void
    {
        $specialPrice = $this->buildToken('TOKEN-SAVE', now: new \DateTimeImmutable('-1 hour'));
        $this->repository->save($specialPrice);

        /** @var TicketSpecialPrice|null $loaded */
        $loaded = $this->repository->find($specialPrice->id);
        self::assertInstanceOf(TicketSpecialPrice::class, $loaded);
        self::assertSame('TOKEN-SAVE', $loaded->token);
        self::assertSame(80.0, $loaded->price);
        self::assertSame(self::FORUM_ID, $loaded->eventId);
        self::assertSame(101, $loaded->creatorId);
    }

    public function testFindUnusedTokenRetourneLeTokenDansSaFenetreDeValidite(): void
    {
        $specialPrice = $this->buildToken('TOKEN-VALIDE', now: new \DateTimeImmutable('-1 hour'));
        $this->repository->save($specialPrice);

        $found = $this->repository->findUnusedToken(self::FORUM_ID, 'TOKEN-VALIDE');
        self::assertInstanceOf(TicketSpecialPrice::class, $found);
        self::assertSame($specialPrice->id, $found->id);
    }

    public function testFindUnusedTokenIgnoreLeTokenDejaUtilise(): void
    {
        $specialPrice = $this->buildToken('TOKEN-UTILISE', now: new \DateTimeImmutable('-1 hour'));
        $this->repository->save($specialPrice);
        $this->insertInscription($this->connection, 'TOKEN-UTILISE', 'A', self::FORUM_ID, 0);

        self::assertNull($this->repository->findUnusedToken(self::FORUM_ID, 'TOKEN-UTILISE'));
    }

    public function testFindUnusedTokenIgnoreLesInscriptionsAnnulees(): void
    {
        $specialPrice = $this->buildToken('TOKEN-ANNULE', now: new \DateTimeImmutable('-1 hour'));
        $this->repository->save($specialPrice);
        $this->insertInscription($this->connection, 'TOKEN-ANNULE', 'B', self::FORUM_ID, Ticket::STATUS_CANCELLED);

        $found = $this->repository->findUnusedToken(self::FORUM_ID, 'TOKEN-ANNULE');
        self::assertInstanceOf(TicketSpecialPrice::class, $found);
    }

    public function testFindUnusedTokenIgnoreUnTokenHorsFenetreDeValidite(): void
    {
        $this->repository->save($this->buildToken('TOKEN-FUTUR', now: null, dateStart: new \DateTimeImmutable('+1 hour')));
        $this->repository->save($this->buildToken('TOKEN-PASSE', now: null, dateEnd: new \DateTimeImmutable('-1 hour')));

        self::assertNull($this->repository->findUnusedToken(self::FORUM_ID, 'TOKEN-FUTUR'));
        self::assertNull($this->repository->findUnusedToken(self::FORUM_ID, 'TOKEN-PASSE'));
    }

    public function testFindUnusedTokenIgnoreUnTokenDAutreEvenement(): void
    {
        $specialPrice = $this->buildToken('TOKEN-AUTRE-FORUM', now: new \DateTimeImmutable('-1 hour'));
        $specialPrice->eventId = self::FORUM_ID + 1;
        $this->repository->save($specialPrice);

        self::assertNull($this->repository->findUnusedToken(self::FORUM_ID, 'TOKEN-AUTRE-FORUM'));
    }

    public function testGetByEventRetourneLesTokensAvecCreateurEtUtilisation(): void
    {
        $this->insertUtilisateur($this->connection, 200, 'Jean', 'Dupont');
        $this->insertUtilisateur($this->connection, 201, 'Marie', 'Martin');

        $unused = $this->buildToken('TOKEN-LISTE-UNUSED', now: new \DateTimeImmutable('-1 hour'), creatorId: 201);
        $this->repository->save($unused);
        $used = $this->buildToken('TOKEN-LISTE-USED', now: new \DateTimeImmutable('-1 hour'), creatorId: 200);
        $this->repository->save($used);
        $this->insertInscription($this->connection, 'TOKEN-LISTE-USED', 'C', self::FORUM_ID, 0);
        // Une inscription annulee ne consomme pas le token
        $this->insertInscription($this->connection, 'TOKEN-LISTE-UNUSED', 'D', self::FORUM_ID, Ticket::STATUS_CANCELLED);

        $rows = $this->repository->getByEvent(self::FORUM_ID);
        self::assertCount(2, $rows);

        // Tri par id decroissant : le dernier token cree apparait en premier
        /** @var array{specialPrice: TicketSpecialPrice, creatorPrenom: ?string, creatorNom: ?string, used: bool} $firstRow */
        $firstRow = $rows[0];
        /** @var array{specialPrice: TicketSpecialPrice, creatorPrenom: ?string, creatorNom: ?string, used: bool} $secondRow */
        $secondRow = $rows[1];

        self::assertSame('TOKEN-LISTE-USED', $firstRow['specialPrice']->token);
        self::assertTrue($firstRow['used']);
        self::assertSame('Jean', $firstRow['creatorPrenom']);
        self::assertSame('Dupont', $firstRow['creatorNom']);

        self::assertSame('TOKEN-LISTE-UNUSED', $secondRow['specialPrice']->token);
        self::assertFalse($secondRow['used']);
        self::assertSame('Marie', $secondRow['creatorPrenom']);
        self::assertSame('Martin', $secondRow['creatorNom']);
    }

    public function testGetByEventSansCreateurConnuRenvoieDesNomsNuls(): void
    {
        $specialPrice = $this->buildToken('TOKEN-SANS-CREATEUR', now: new \DateTimeImmutable('-1 hour'));
        $specialPrice->creatorId = 999999;
        $this->repository->save($specialPrice);

        $rows = $this->repository->getByEvent(self::FORUM_ID);
        self::assertCount(1, $rows);
        self::assertNull($rows[0]['creatorPrenom']);
        self::assertNull($rows[0]['creatorNom']);
        self::assertFalse($rows[0]['used']);
    }

    private function buildToken(
        string $token,
        ?\DateTimeImmutable $now = null,
        ?\DateTimeImmutable $dateStart = null,
        ?\DateTimeImmutable $dateEnd = null,
        int $creatorId = 101,
    ): TicketSpecialPrice {
        $now ??= new \DateTimeImmutable();
        $specialPrice = new TicketSpecialPrice();
        $specialPrice->eventId = self::FORUM_ID;
        $specialPrice->token = $token;
        $specialPrice->price = 80.0;
        $specialPrice->dateStart = $dateStart ?? $now;
        $specialPrice->dateEnd = $dateEnd ?? $now->modify('+1 day');
        $specialPrice->description = 'Description de test';
        $specialPrice->createdOn = $now;
        $specialPrice->creatorId = $creatorId;

        return $specialPrice;
    }

    private function insertUtilisateur(Connection $connection, int $id, string $prenom, string $nom): void
    {
        $connection->insert('afup_personnes_physiques', [
            'id' => $id,
            'login' => 'user' . $id,
            'roles' => 'ROLE_USER',
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => 'user' . $id . '@example.com',
            'adresse' => '',
            'etat' => 0,
        ]);
    }

    private function insertInscription(Connection $connection, string $token, string $reference, int $forumId, int $etat): void
    {
        $connection->insert('afup_inscription_forum', [
            'reference' => $reference,
            'special_price_token' => $token,
            'id_forum' => $forumId,
            'etat' => $etat,
        ]);
    }
}
