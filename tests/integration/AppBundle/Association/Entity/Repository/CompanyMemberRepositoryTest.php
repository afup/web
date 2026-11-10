<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Association\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Association\Entity\CompanyMember;
use AppBundle\Association\Enum\CompanyMemberStatus;
use AppBundle\Association\Entity\Repository\CompanyMemberRepository;
use AppBundle\Association\MemberType;
use AppBundle\Association\Model\User;
use AppBundle\MembershipFee\Entity\Cotisation;
use AppBundle\MembershipFee\Entity\Repository\CotisationRepository;
use Doctrine\DBAL\Connection;

final class CompanyMemberRepositoryTest extends IntegrationTestCase
{
    private CompanyMemberRepository $repository;
    private Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(CompanyMemberRepository::class);
        $this->connection = self::getContainer()->get(Connection::class);
    }

    public function testSaveFindAndRemove(): void
    {
        $company = $this->createCompany('Société de test');
        $id = $company->id;
        self::assertNotNull($id);

        $row = $this->connection->fetchAssociative('SELECT * FROM afup_personnes_morales WHERE id = :id', ['id' => $id]);
        self::assertIsArray($row);
        self::assertSame('Société de test', $row['raison_sociale']);
        self::assertSame('Paris', $row['ville']);
        self::assertSame('FR', $row['id_pays']);
        self::assertSame(CompanyMemberStatus::Active->value, (int) $row['etat']);

        self::getContainer()->get('doctrine')->getManager()->clear();

        $found = $this->repository->find($id);
        self::assertInstanceOf(CompanyMember::class, $found);
        self::assertSame('Société de test', $found->companyName);
        self::assertSame(3, $found->maxMembers);
        self::assertNull($found->lastSubscription, 'find() ne calcule pas la dernière cotisation');

        $this->repository->remove($found);

        self::assertFalse($this->connection->fetchOne('SELECT 1 FROM afup_personnes_morales WHERE id = :id', ['id' => $id]));
    }

    public function testRemoveIsRefusedWhenTheCompanyHasMembershipFees(): void
    {
        $company = $this->createCompany('Avec cotisation');
        $this->createMembershipFee(MemberType::MemberCompany, (int) $company->id, new \DateTime('+1 month'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Impossible de supprimer une personne morale qui a des cotisations');

        $this->repository->remove($company);
    }

    public function testRemoveIsRefusedWhenTheCompanyHasMembers(): void
    {
        $company = $this->createCompany('Avec membres');
        $this->insertPhysicalPerson('membre@example.com', User::STATUS_ACTIVE, (int) $company->id);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Impossible de supprimer une personne morale qui a des membres');

        $this->repository->remove($company);
    }

    public function testFindByIdLoadsTheLastSubscription(): void
    {
        $company = $this->createCompany('Cotisante');
        $otherCompany = $this->createCompany('Autre');
        $lastEnd = new \DateTime('+6 months');
        $this->createMembershipFee(MemberType::MemberCompany, (int) $company->id, new \DateTime('-6 months'));
        $this->createMembershipFee(MemberType::MemberCompany, (int) $company->id, $lastEnd);
        // Cotisation d'une personne physique portant le même identifiant : ne doit pas être prise en compte.
        $this->createMembershipFee(MemberType::MemberPhysical, (int) $otherCompany->id, new \DateTime('+2 years'));

        $found = $this->repository->findById((int) $company->id);
        self::assertInstanceOf(CompanyMember::class, $found);
        self::assertSame($lastEnd->getTimestamp(), $found->lastSubscription?->getTimestamp());
        self::assertTrue($found->hasUpToDateMembershipFee());

        $other = $this->repository->findById((int) $otherCompany->id);
        self::assertInstanceOf(CompanyMember::class, $other);
        self::assertNull($other->lastSubscription);
        self::assertFalse($other->hasUpToDateMembershipFee());

        self::assertNull($this->repository->findById(999_999));
    }

    public function testFindDisplayableCompanies(): void
    {
        $upToDate = $this->createCompany('À jour', publicProfile: true);
        $this->createMembershipFee(MemberType::MemberCompany, (int) $upToDate->id, new \DateTime('+1 month'));

        $expired = $this->createCompany('Expirée', publicProfile: true);
        $this->createMembershipFee(MemberType::MemberCompany, (int) $expired->id, new \DateTime('-1 month'));

        $private = $this->createCompany('Privée', publicProfile: false);
        $this->createMembershipFee(MemberType::MemberCompany, (int) $private->id, new \DateTime('+1 month'));

        $this->createCompany('Sans cotisation', publicProfile: true);

        $companies = $this->repository->findDisplayableCompanies();

        self::assertSame(['À jour'], array_map(static fn(CompanyMember $c): string => $c->companyName, $companies));
    }

    public function testLoadAll(): void
    {
        $company = $this->createCompany('Une');
        $this->createCompany('Deux');
        $this->createMembershipFee(MemberType::MemberCompany, (int) $company->id, new \DateTime('+1 month'));

        $companies = $this->repository->loadAll();

        self::assertCount(2, $companies);
        $byName = [];
        foreach ($companies as $c) {
            $byName[$c->companyName] = $c;
        }
        self::assertTrue($byName['Une']->hasUpToDateMembershipFee());
        self::assertFalse($byName['Deux']->hasUpToDateMembershipFee());
    }

    public function testSearch(): void
    {
        $this->createCompany('Zeta Corp', city: 'Lyon');
        $this->createCompany('Alpha SARL', city: 'Paris', status: CompanyMemberStatus::Pending);
        $this->createCompany('Beta SAS', city: 'Lille');

        $names = static fn(array $companies): array => array_map(static fn(CompanyMember $c): string => $c->companyName, $companies);

        self::assertSame(['Beta SAS', 'Zeta Corp'], $names($this->repository->search()));
        self::assertSame(['Zeta Corp', 'Beta SAS'], $names($this->repository->search('name', 'desc')));
        self::assertSame(['Alpha SARL', 'Beta SAS', 'Zeta Corp'], $names($this->repository->search('name', 'asc', null, false)));
        self::assertSame(['Alpha SARL', 'Beta SAS', 'Zeta Corp'], $names($this->repository->search('status', 'asc', null, false)));
        // La direction s'applique à tous les champs de tri (état puis raison sociale)
        self::assertSame(['Zeta Corp', 'Beta SAS', 'Alpha SARL'], $names($this->repository->search('status', 'desc', null, false)));

        // Filtre insensible à la casse sur la raison sociale, ou sur la ville
        self::assertSame(['Zeta Corp'], $names($this->repository->search('name', 'asc', 'zeta')));
        self::assertSame(['Beta SAS'], $names($this->repository->search('name', 'asc', 'Lille')));
        // Plusieurs mots : un OU entre eux, combiné en ET avec le filtre sur l'état
        self::assertSame(['Beta SAS', 'Zeta Corp'], $names($this->repository->search('name', 'asc', 'Zeta Lille Alpha')));
        self::assertSame(['Alpha SARL', 'Beta SAS', 'Zeta Corp'], $names($this->repository->search('name', 'asc', 'Zeta Lille Alpha', false)));
    }

    public function testSearchRejectsUnknownSort(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->repository->search('unknown');
    }

    public function testCountByStatusCountsCompaniesNotPhysicalPersons(): void
    {
        // Une seule personne morale active, deux en attente.
        $this->createCompany('Active');
        $this->createCompany('En attente 1', status: CompanyMemberStatus::Pending);
        $this->createCompany('En attente 2', status: CompanyMemberStatus::Pending);

        // Cinq personnes physiques actives, aucune en attente : des comptes
        // volontairement différents de ceux des personnes morales ci-dessus,
        // pour détecter toute confusion entre les deux tables.
        for ($i = 0; $i < 5; $i++) {
            $this->insertPhysicalPerson("physique{$i}@example.com", User::STATUS_ACTIVE);
        }

        self::assertSame(1, $this->repository->countByStatus(CompanyMemberStatus::Active));
        self::assertSame(2, $this->repository->countByStatus(CompanyMemberStatus::Pending));
        self::assertSame(0, $this->repository->countByStatus(CompanyMemberStatus::Inactive));
    }

    public function testGetList(): void
    {
        $b = $this->createCompany('Bravo');
        $a = $this->createCompany('Alpha');

        self::assertSame([
            (int) $a->id => sprintf('Alpha (id : %d)', $a->id),
            (int) $b->id => sprintf('Bravo (id : %d)', $b->id),
        ], $this->repository->getList());
    }

    public function testCountActiveByCompany(): void
    {
        $company = $this->createCompany('Avec membres');
        $this->insertPhysicalPerson('un@example.com', User::STATUS_ACTIVE, (int) $company->id);
        $this->insertPhysicalPerson('deux@example.com', User::STATUS_ACTIVE, (int) $company->id);
        $this->insertPhysicalPerson('seul@example.com', User::STATUS_ACTIVE);

        $counts = $this->repository->countActiveByCompany();

        self::assertSame(2, $counts[(int) $company->id]);
        self::assertSame(1, $counts[0]);
    }

    public function testSearchCompanyMemberSubscriptions(): void
    {
        $company = $this->createCompany('Recherchée', email: 'contact@recherchee.example.com');
        $fee = $this->createMembershipFee(MemberType::MemberCompany, (int) $company->id, new \DateTime('+1 month'), 'Virement XYZ');
        // Même identifiant, mais personne physique : exclue.
        $this->createMembershipFee(MemberType::MemberPhysical, (int) $company->id, new \DateTime('+1 month'), 'Virement XYZ');

        $results = $this->repository->searchCompanyMemberSubscriptions('XYZ');

        self::assertCount(1, $results);
        self::assertSame('Recherchée', $results[0]['raison_sociale']);
        self::assertSame('contact@recherchee.example.com', $results[0]['email']);
        self::assertSame($fee->id, (int) $results[0]['id']);
        self::assertSame('Virement XYZ', $results[0]['informations_reglement']);

        self::assertCount(1, $this->repository->searchCompanyMemberSubscriptions('recherchee.example'));
        self::assertCount(0, $this->repository->searchCompanyMemberSubscriptions('introuvable'));
    }

    private function createCompany(
        string $name,
        string $city = 'Paris',
        CompanyMemberStatus $status = CompanyMemberStatus::Active,
        bool $publicProfile = false,
        ?string $email = null,
    ): CompanyMember {
        static $counter = 0;
        $counter++;

        $company = new CompanyMember();
        $company->firstName = 'Prénom';
        $company->lastName = 'Nom';
        $company->email = $email ?? "morale{$counter}@example.com";
        $company->companyName = $name;
        $company->siret = '12345678901234';
        $company->address = '1 rue du Test';
        $company->zipCode = '75000';
        $company->city = $city;
        $company->country = 'FR';
        $company->status = $status;
        $company->maxMembers = 3;
        $company->publicProfileEnabled = $publicProfile;
        $this->repository->save($company);

        return $company;
    }

    private function createMembershipFee(MemberType $type, int $memberId, \DateTime $end, ?string $paymentInfo = null): Cotisation
    {
        static $invoiceCounter = 0;
        $invoiceCounter++;

        $fee = new Cotisation();
        $fee->numeroFacture = sprintf('TEST-%04d', $invoiceCounter);
        $fee->typePersonne = $type;
        $fee->idPersonne = $memberId;
        $fee->montant = 150.0;
        $fee->dateDebut = (clone $end)->modify('-1 year');
        $fee->dateFin = $end;
        $fee->informationsReglement = $paymentInfo;
        self::getContainer()->get(CotisationRepository::class)->save($fee);

        return $fee;
    }

    private function insertPhysicalPerson(string $email, int $status, int $companyId = 0): void
    {
        $this->connection->insert('afup_personnes_physiques', [
            'roles' => '[]',
            'adresse' => '1 rue du Test',
            'email' => $email,
            'etat' => $status,
            'id_personne_morale' => $companyId,
        ]);
    }
}
