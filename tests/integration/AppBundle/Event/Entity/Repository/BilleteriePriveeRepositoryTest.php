<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\BilleteriePrivee;
use AppBundle\Event\Entity\Repository\BilleteriePriveeRepository;
use Doctrine\DBAL\Connection;

final class BilleteriePriveeRepositoryTest extends IntegrationTestCase
{
    public function testFindAndCountPlacesPrises(): void
    {
        $billeteriePriveeRepository = self::getContainer()->get(BilleteriePriveeRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $billeteriePrivee = $this->buildBilleteriePrivee('Billetterie école XYZ', 'TOKEN-XYZ');
        $billeteriePriveeRepository->save($billeteriePrivee);
        $billeteriePriveeRepository->save($this->buildBilleteriePrivee('Billetterie entreprise ABC', 'TOKEN-ABC'));

        $fromEvent = $billeteriePriveeRepository->findByEvent(42);
        self::assertCount(2, $fromEvent);
        self::assertSame('Billetterie entreprise ABC', $fromEvent[0]->nom);

        $billeterie = $billeteriePriveeRepository->findOneByToken('token-xyz');
        self::assertInstanceOf(BilleteriePrivee::class, $billeterie);
        self::assertSame(10, $billeterie->maxPlaces);
        self::assertNull($billeteriePriveeRepository->findOneByToken('TOKEN-INTROUVABLE'));
        self::assertNull($billeteriePriveeRepository->findOneByToken(null));

        // Deux inscriptions actives + une annulée, sur deux billeteries différentes
        // (le token doit correspondre exactement, la table des inscriptions est case-sensitive)
        $this->insertInscription($connection, 'TOKEN-XYZ', 'A', 42, 0);
        $this->insertInscription($connection, 'TOKEN-XYZ', 'B', 42, 0);
        $this->insertInscription($connection, 'TOKEN-XYZ', 'C', 42, 1);
        $this->insertInscription($connection, 'TOKEN-ABC', 'D', 42, 0);

        self::assertSame(2, $billeteriePriveeRepository->countPlacesPrisesParToken('TOKEN-XYZ'));
        self::assertSame(1, $billeteriePriveeRepository->countPlacesPrisesParToken('TOKEN-ABC'));
        self::assertSame(0, $billeteriePriveeRepository->countPlacesPrisesParToken('TOKEN-INTROUVABLE'));

        self::assertSame(8, $billeterie->getPlacesRestantes(2));
    }

    private function buildBilleteriePrivee(string $nom, string $token): BilleteriePrivee
    {
        $now = new \DateTimeImmutable();

        $billeteriePrivee = new BilleteriePrivee();
        $billeteriePrivee->eventId = 42;
        $billeteriePrivee->nom = $nom;
        $billeteriePrivee->token = $token;
        $billeteriePrivee->motDePasse = password_hash('secretxyz', PASSWORD_DEFAULT);
        $billeteriePrivee->ticketTypeId = 1;
        $billeteriePrivee->prix = 80.0;
        $billeteriePrivee->maxPlaces = 10;
        $billeteriePrivee->dateDebut = $now;
        $billeteriePrivee->dateFin = $now->modify('+1 day');
        $billeteriePrivee->createdOn = $now;

        return $billeteriePrivee;
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
