<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Association\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Association\Entity\CompanyMemberInvitation;
use AppBundle\Association\Entity\Repository\CompanyMemberInvitationRepository;
use AppBundle\Association\Enum\InvitationEtat;
use AppBundle\Association\Entity\CompanyMember;
use AppBundle\Association\Entity\Repository\CompanyMemberRepository;

final class CompanyMemberInvitationRepositoryTest extends IntegrationTestCase
{
    public function testLoadPendingInvitationsByCompanyReturnsOnlyPendingOfTheCompany(): void
    {
        $companyMemberRepository = self::getContainer()->get(CompanyMemberRepository::class);
        $repository = self::getContainer()->get(CompanyMemberInvitationRepository::class);

        $company = $this->createCompany($companyMemberRepository, 'societe-a@example.com');
        $otherCompany = $this->createCompany($companyMemberRepository, 'societe-b@example.com');

        $this->persistInvitation($repository, $company->id, 'en-attente@example.com', InvitationEtat::EnAttente);
        $this->persistInvitation($repository, $company->id, 'annulee@example.com', InvitationEtat::Annulee);
        $this->persistInvitation($repository, $company->id, 'acceptee@example.com', InvitationEtat::Acceptee);
        $this->persistInvitation($repository, $otherCompany->id, 'autre-societe@example.com', InvitationEtat::EnAttente);

        $invitations = $repository->loadPendingInvitationsByCompany((int) $company->id);

        self::assertCount(1, $invitations);
        self::assertSame('en-attente@example.com', $invitations[0]->email);
        self::assertSame(InvitationEtat::EnAttente, $invitations[0]->status);
    }

    public function testLoadPendingInvitationsByCompanySortsBySubmissionDate(): void
    {
        $companyMemberRepository = self::getContainer()->get(CompanyMemberRepository::class);
        $repository = self::getContainer()->get(CompanyMemberInvitationRepository::class);

        $company = $this->createCompany($companyMemberRepository, 'tri@example.com');

        $ancienne = $this->persistInvitation($repository, $company->id, 'ancienne@example.com', InvitationEtat::EnAttente);
        $ancienne->submittedOn = new \DateTime('-2 days');
        $repository->save($ancienne);

        $recente = $this->persistInvitation($repository, $company->id, 'recente@example.com', InvitationEtat::EnAttente);

        $invitations = $repository->loadPendingInvitationsByCompany((int) $company->id);

        self::assertSame(['ancienne@example.com', 'recente@example.com'], array_map(
            static fn(CompanyMemberInvitation $invitation): string => $invitation->email,
            $invitations,
        ));
        self::assertGreaterThan($ancienne->submittedOn, $recente->submittedOn);
    }

    public function testFindPendingByEmailReturnsPendingInvitationOfTheCompany(): void
    {
        $companyMemberRepository = self::getContainer()->get(CompanyMemberRepository::class);
        $repository = self::getContainer()->get(CompanyMemberInvitationRepository::class);

        $company = $this->createCompany($companyMemberRepository, 'recherche@example.com');
        $otherCompany = $this->createCompany($companyMemberRepository, 'recherche-autre@example.com');

        $this->persistInvitation($repository, $company->id, 'invite@example.com', InvitationEtat::EnAttente);

        $invitation = $repository->findPendingByEmail((int) $company->id, 'invite@example.com');

        self::assertNotNull($invitation);
        self::assertSame('invite@example.com', $invitation->email);
        self::assertSame((int) $company->id, $invitation->companyId);

        // Le même email chez une autre société ne doit rien retourner.
        self::assertNull($repository->findPendingByEmail((int) $otherCompany->id, 'invite@example.com'));
    }

    public function testFindPendingByEmailIgnoresCancelledInvitation(): void
    {
        $companyMemberRepository = self::getContainer()->get(CompanyMemberRepository::class);
        $repository = self::getContainer()->get(CompanyMemberInvitationRepository::class);

        $company = $this->createCompany($companyMemberRepository, 'annulation@example.com');

        $invitation = $this->persistInvitation($repository, $company->id, 'annulee@example.com', InvitationEtat::EnAttente);
        $invitation->status = InvitationEtat::Annulee;
        $repository->save($invitation);

        self::assertNull($repository->findPendingByEmail((int) $company->id, 'annulee@example.com'));
    }

    public function testSaveThenUpdateKeepsSameRow(): void
    {
        $companyMemberRepository = self::getContainer()->get(CompanyMemberRepository::class);
        $repository = self::getContainer()->get(CompanyMemberInvitationRepository::class);
        $connection = self::getContainer()->get(\Doctrine\DBAL\Connection::class);

        $company = $this->createCompany($companyMemberRepository, 'cycle@example.com');

        $invitation = $this->persistInvitation($repository, $company->id, 'cycle@example.com', InvitationEtat::EnAttente);
        $id = $invitation->id;

        $invitation->status = InvitationEtat::Acceptee;
        $repository->save($invitation);

        self::assertSame($id, $invitation->id);
        self::assertSame(
            InvitationEtat::Acceptee->value,
            (int) $connection->fetchOne('SELECT status FROM afup_personnes_morales_invitations WHERE id = :id', ['id' => $id]),
        );
        self::assertSame(1, (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM afup_personnes_morales_invitations WHERE company_id = :companyId',
            ['companyId' => $company->id],
        ));
    }

    private function createCompany(CompanyMemberRepository $repository, string $email): CompanyMember
    {
        static $counter = 0;
        $counter++;

        $company = new CompanyMember();
        $company->firstName = 'Prénom';
        $company->lastName = 'Nom';
        $company->email = $email;
        $company->companyName = 'Société de test ' . $counter;
        $company->siret = '12345678901234';
        $company->address = '1 rue du Test';
        $company->zipCode = '75000';
        $company->city = 'Paris';
        $company->country = 'FR';
        $repository->save($company);

        return $company;
    }

    private function persistInvitation(
        CompanyMemberInvitationRepository $repository,
        int $companyId,
        string $email,
        InvitationEtat $status,
    ): CompanyMemberInvitation {
        $invitation = new CompanyMemberInvitation();
        $invitation->companyId = $companyId;
        $invitation->email = $email;
        $invitation->token = base64_encode(random_bytes(30));
        $invitation->manager = false;
        $invitation->submittedOn = new \DateTime();
        $invitation->status = $status;
        $repository->save($invitation);

        return $invitation;
    }
}
