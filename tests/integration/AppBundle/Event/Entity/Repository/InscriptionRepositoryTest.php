<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Association\Genre;
use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Entity\Repository\InscriptionRepository;
use AppBundle\Event\Entity\TicketEventType;
use AppBundle\Event\Entity\TicketType;
use AppBundle\Event\Model\Event;
use Doctrine\DBAL\Connection;
use AppBundle\Event\Enum\DistanceTransport;
use AppBundle\Event\Enum\InscriptionEtat;
use AppBundle\Event\Enum\JourTarif;
use AppBundle\Event\Enum\ModeTransport;

final class InscriptionRepositoryTest extends IntegrationTestCase
{
    private const int EVENT_ID = 777;
    private const int TARIF_PUBLIC = 200;
    private const int TARIF_PRIVE = 201;
    private const int TARIF_SANS_EVENEMENT = 202;

    public function testCrudEtConversionDesColonnes(): void
    {
        $repository = self::getContainer()->get(InscriptionRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $inscription = $this->buildInscription('REF-CRUD', 'Lovelace', 'Ada');
        $inscription->date = new \DateTime('@1700000000');
        $inscription->amount = 149.5;
        $inscription->genre = Genre::Femme;
        $inscription->companyCitation = true;
        $inscription->newsletter = true;
        $inscription->optin = true;
        $inscription->day1Checkin = true;
        $inscription->memberId = 12;
        $inscription->memberType = 1;
        $inscription->transportMode = ModeTransport::Train;
        $inscription->transportDistance = DistanceTransport::De100A500;
        $repository->save($inscription);

        $row = $connection->fetchAssociative('SELECT * FROM afup_inscription_forum WHERE id = ?', [$inscription->id]);
        self::assertIsArray($row);
        self::assertSame(1700000000, (int) $row['date']);
        self::assertSame(149.5, (float) $row['montant']);
        self::assertSame('femme', $row['genre']);
        self::assertSame(1, (int) $row['citer_societe']);
        self::assertSame(1, (int) $row['newsletter_afup']);
        self::assertSame(1, (int) $row['mail_partenaire']);
        self::assertSame(1, (int) $row['presence_day1']);
        self::assertNull($row['presence_day2']);
        self::assertSame('Lovelace', $row['nom']);
        self::assertSame('Ada', $row['prenom']);
        self::assertSame(80, (int) $row['transport_mode']);
        self::assertSame(100, (int) $row['transport_distance']);
        self::assertSame(0, (int) $row['etat']);
        self::assertSame(0, (int) $row['facturation']);
        self::assertSame(self::EVENT_ID, (int) $row['id_forum']);

        $id = $inscription->id;
        self::getContainer()->get('doctrine')->getManager()->clear();

        $found = $repository->find($id);
        self::assertInstanceOf(Inscription::class, $found);
        self::assertSame(1700000000, $found->date->getTimestamp());
        self::assertSame(149.5, $found->amount);
        self::assertSame(Genre::Femme, $found->genre);
        self::assertTrue($found->companyCitation);
        self::assertTrue($found->day1Checkin);
        self::assertNull($found->day2Checkin);
        self::assertSame(ModeTransport::Train, $found->transportMode);
        self::assertSame(DistanceTransport::De100A500, $found->transportDistance);
        self::assertSame(InscriptionEtat::Creee, $found->status);
        self::assertSame('Ada Lovelace', $found->getLabel());

        $repository->delete($found);
        self::assertNull($repository->find($id));
    }

    public function testModeDeTransportHistoriqueAZero(): void
    {
        $repository = self::getContainer()->get(InscriptionRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        // Valeur enregistrée par l'ancien formulaire quand le champ était laissé vide
        $connection->insert('afup_inscription_forum', [
            'reference' => 'REF-TRANSPORT-0',
            'id_forum' => self::EVENT_ID,
            'transport_mode' => 0,
            'transport_distance' => 0,
        ]);

        $inscriptions = $repository->getByReference('REF-TRANSPORT-0');
        self::assertCount(1, $inscriptions);
        self::assertSame(ModeTransport::NonRenseigne, $inscriptions[0]->transportMode);
        self::assertSame(DistanceTransport::Moins25, $inscriptions[0]->transportDistance);
        self::assertNotContains(ModeTransport::NonRenseigne, ModeTransport::choixParLibelle());
    }

    public function testGetByReferenceWithTicketEventTypeIgnoreLesInscriptionsSansTarif(): void
    {
        $repository = self::getContainer()->get(InscriptionRepository::class);
        $this->insertTarifs();

        $avecTarif = $this->buildInscription('REF-CMD', 'Hopper', 'Grace');
        $avecTarif->ticketTypeId = self::TARIF_PUBLIC;
        $repository->save($avecTarif);

        $sansTarif = $this->buildInscription('REF-CMD', 'Turing', 'Alan');
        $sansTarif->ticketTypeId = self::TARIF_SANS_EVENEMENT;
        $repository->save($sansTarif);

        $autreCommande = $this->buildInscription('REF-AUTRE', 'Knuth', 'Donald');
        $autreCommande->ticketTypeId = self::TARIF_PUBLIC;
        $repository->save($autreCommande);

        self::assertCount(2, $repository->getByReference('REF-CMD'));

        $inscriptions = $repository->getByReferenceWithTicketEventType('REF-CMD');
        self::assertCount(1, $inscriptions);
        self::assertSame('Hopper', $inscriptions[0]->lastname);
        self::assertInstanceOf(TicketEventType::class, $inscriptions[0]->ticketEventType);
        self::assertSame(120.0, $inscriptions[0]->ticketEventType->price);
    }

    public function testGetPublicSoldTickets(): void
    {
        $repository = self::getContainer()->get(InscriptionRepository::class);
        $this->insertTarifs();

        foreach ([
            [self::TARIF_PUBLIC, InscriptionEtat::Reglee],
            [self::TARIF_PUBLIC, InscriptionEtat::Creee],
            [self::TARIF_PUBLIC, InscriptionEtat::Annulee],
            [self::TARIF_PRIVE, InscriptionEtat::Reglee],
        ] as $index => [$ticketTypeId, $status]) {
            $inscription = $this->buildInscription('REF-SOLD-' . $index, 'Nom', 'Prenom');
            $inscription->ticketTypeId = $ticketTypeId;
            $inscription->status = $status;
            $repository->save($inscription);
        }

        $event = new Event()->setId(self::EVENT_ID);
        $ticketType = new TicketType();
        $ticketType->id = self::TARIF_PUBLIC;

        self::assertSame(2, $repository->getPublicSoldTickets($event));
        self::assertSame(2, $repository->getPublicSoldTicketsByDay(JourTarif::Un->value, $event));
        self::assertSame(0, $repository->getPublicSoldTicketsByDay(JourTarif::Deux->value, $event));
        self::assertSame(2, $repository->getPublicSoldTicketsOfType($event, $ticketType));
        self::assertSame(0, $repository->getPublicSoldTickets(new Event()->setId(self::EVENT_ID + 1)));
    }

    public function testGetTotalOfSoldTicketsByMember(): void
    {
        $repository = self::getContainer()->get(InscriptionRepository::class);

        foreach ([InscriptionEtat::Reglee, InscriptionEtat::Reglee, InscriptionEtat::Creee] as $index => $status) {
            $inscription = $this->buildInscription('REF-MEMBER-' . $index, 'Nom', 'Prenom');
            $inscription->memberId = 42;
            $inscription->memberType = 1;
            $inscription->status = $status;
            $repository->save($inscription);
        }

        self::assertSame(2, $repository->getTotalOfSoldTicketsByMember(1, 42, self::EVENT_ID));
        self::assertSame(0, $repository->getTotalOfSoldTicketsByMember(0, 42, self::EVENT_ID));
        self::assertSame(0, $repository->getTotalOfSoldTicketsByMember(1, 42, self::EVENT_ID + 1));
    }

    public function testGetByEventWithAllTrieEtFiltre(): void
    {
        $repository = self::getContainer()->get(InscriptionRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $repository->save($this->buildInscription('REF-B', 'Berners-Lee', 'Tim'));
        $repository->save($this->buildInscription('REF-A', 'Allen', 'Frances'));
        $repository->save($this->buildInscription('REF-C', 'Cerf', 'Vint'));
        $autreEvenement = $this->buildInscription('REF-D', 'Dijkstra', 'Edsger');
        $autreEvenement->forumId = self::EVENT_ID + 1;
        $repository->save($autreEvenement);

        $connection->insert('afup_facturation_forum', [
            'reference' => 'REF-C',
            'montant' => 0,
            'societe' => 'Arpanet',
            'adresse' => '',
            'id_forum' => self::EVENT_ID,
        ]);

        $event = new Event()->setId(self::EVENT_ID);

        $aggregates = $repository->getByEventWithAll($event, null, 'label', 'asc');
        self::assertSame(['Allen', 'Berners-Lee', 'Cerf'], array_map(
            static fn($aggregate): ?string => $aggregate->ticket->lastname,
            $aggregates,
        ));
        self::assertNull($aggregates[0]->invoice);
        self::assertNull($aggregates[0]->ticketType);

        $aggregates = $repository->getByEventWithAll($event, null, 'label', 'desc');
        self::assertSame('Cerf', $aggregates[0]->ticket->lastname);

        $aggregates = $repository->getByEventWithAll($event, 'timberners', null, null);
        self::assertCount(1, $aggregates);
        self::assertSame('Berners-Lee', $aggregates[0]->ticket->lastname);

        // La recherche porte aussi sur la société de la facture
        $aggregates = $repository->getByEventWithAll($event, 'arpa', null, null);
        self::assertCount(1, $aggregates);
        self::assertSame('Cerf', $aggregates[0]->ticket->lastname);
    }

    private function buildInscription(string $reference, string $lastname, string $firstname): Inscription
    {
        $inscription = new Inscription();
        $inscription->reference = $reference;
        $inscription->lastname = $lastname;
        $inscription->firstname = $firstname;
        $inscription->email = strtolower($firstname . '.' . $lastname) . '@example.com';
        $inscription->forumId = self::EVENT_ID;

        return $inscription;
    }

    private function insertTarifs(): void
    {
        $connection = self::getContainer()->get(Connection::class);

        foreach ([
            ['id' => self::TARIF_PUBLIC, 'technical_name' => 'T_PUBLIC', 'public' => 1],
            ['id' => self::TARIF_PRIVE, 'technical_name' => 'T_PRIVE', 'public' => 0],
            ['id' => self::TARIF_SANS_EVENEMENT, 'technical_name' => 'T_SANS_EVENEMENT', 'public' => 1],
        ] as $tarif) {
            $connection->insert('afup_forum_tarif', $tarif + [
                'pretty_name' => 'Tarif',
                'members_only' => 0,
                'cfp_submitter_only' => 0,
                'default_price' => 100.0,
                'active' => 1,
                'day' => 'one',
            ]);
        }

        foreach ([self::TARIF_PUBLIC, self::TARIF_PRIVE] as $tarifId) {
            $connection->insert('afup_forum_tarif_event', [
                'id_tarif' => $tarifId,
                'id_event' => self::EVENT_ID,
                'price' => 120.0,
                'date_start' => '2020-01-01 00:00:00',
                'date_end' => '2099-12-31 00:00:00',
                'description' => 'Tarif de test',
                'max_tickets' => 10,
            ]);
        }
    }
}
