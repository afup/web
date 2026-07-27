<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Association\Model\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use Doctrine\DBAL\Connection;

final class CompanyMemberRepositoryTest extends IntegrationTestCase
{
    public function testCountByStatusCountsCompaniesNotPhysicalPersons(): void
    {
        $companyMemberRepository = self::getContainer()->get(CompanyMemberRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        // Une seule personne morale active, deux en attente.
        $companyMemberRepository->save($this->buildCompanyMember(CompanyMember::STATUS_ACTIVE));
        $companyMemberRepository->save($this->buildCompanyMember(CompanyMember::STATUS_PENDING));
        $companyMemberRepository->save($this->buildCompanyMember(CompanyMember::STATUS_PENDING));

        // Cinq personnes physiques actives, aucune en attente : des comptes
        // volontairement différents de ceux des personnes morales ci-dessus,
        // pour détecter toute confusion entre les deux tables.
        for ($i = 0; $i < 5; $i++) {
            $this->insertPhysicalPerson($connection, "physique{$i}@example.com", CompanyMember::STATUS_ACTIVE);
        }

        self::assertSame(1, $companyMemberRepository->countByStatus(CompanyMember::STATUS_ACTIVE));
        self::assertSame(2, $companyMemberRepository->countByStatus(CompanyMember::STATUS_PENDING));
    }

    private function buildCompanyMember(int $status): CompanyMember
    {
        static $counter = 0;
        $counter++;

        return (new CompanyMember())
            ->setFirstName('Prénom')
            ->setLastName('Nom')
            ->setEmail("morale{$counter}@example.com")
            ->setCompanyName('Société de test')
            ->setSiret('12345678901234')
            ->setAddress('1 rue du Test')
            ->setZipCode('75000')
            ->setCity('Paris')
            ->setCountry('FR')
            ->setStatus($status);
    }

    private function insertPhysicalPerson(Connection $connection, string $email, int $status): void
    {
        $connection->insert('afup_personnes_physiques', [
            'roles' => '[]',
            'adresse' => '1 rue du Test',
            'email' => $email,
            'etat' => $status,
        ]);
    }
}
