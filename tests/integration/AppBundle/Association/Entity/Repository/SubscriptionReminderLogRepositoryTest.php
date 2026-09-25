<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Association\Entity\Repository;

use AppBundle\Association\Entity\Repository\SubscriptionReminderLogRepository;
use Afup\Tests\Support\IntegrationTestCase;
use Doctrine\DBAL\Connection;

final class SubscriptionReminderLogRepositoryTest extends IntegrationTestCase
{
    private Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = self::getContainer()->get(SubscriptionReminderLogRepository::class);
        $this->connection = self::getContainer()->get(Connection::class);

        // Données de base reproductibles pour chaque test : deux personnes
        // physiques dont une rattachée à une personne morale.
        $this->connection->insert('afup_personnes_physiques', [
            'id' => 1,
            'roles' => '[]',
            'adresse' => '1 rue du Test',
            'nom' => 'Paul',
            'prenom' => 'Personne',
            'email' => 'paul@example.com',
        ]);
        $this->connection->insert('afup_personnes_physiques', [
            'id' => 2,
            'roles' => '[]',
            'adresse' => '2 rue du Test',
            'nom' => 'Dupont',
            'prenom' => 'Edmond',
            'email' => 'edmond@example.com',
            'id_personne_morale' => 10,
        ]);
        $this->connection->insert('afup_personnes_morales', [
            'id' => 10,
            'raison_sociale' => 'MyCorp',
            'nom' => 'Dupont',
            'prenom' => 'Edmond',
            'email' => 'mycorp@example.com',
            'siret' => '4445451',
            'adresse' => '1 rue du Test',
            'code_postal' => '69001',
            'ville' => 'LYON',
            'id_pays' => 'FR',
        ]);

        $this->insertReminderLog(userId: 1, date: '2022-04-01', sent: true);
        $this->insertReminderLog(userId: 2, date: '2022-03-01', sent: false);
        $this->insertReminderLog(userId: 2, date: '2022-02-01', sent: true);
    }

    private function insertReminderLog(int $userId, string $date, bool $sent, ?int $id = null): void
    {
        $this->connection->insert('afup_subscription_reminder_log', [
            'user_id' => $userId,
            'user_type' => 0,
            'email' => 'relance@example.com',
            'reminder_date' => $date,
            'reminder_key' => '15DaysAfter',
            'mail_sent' => (int) $sent,
        ] + (is_int($id) ? ['id' => $id] : []));
    }

    public function testGetPaginatedLogsSortsByDateAndMapsScalarValues(): void
    {
        $repository = self::getContainer()->get(SubscriptionReminderLogRepository::class);

        $logs = $repository->getPaginatedLogs();

        self::assertCount(3, $logs);

        $first = $logs[0];
        self::assertSame('Personne', $first->prenom);
        self::assertSame('Paul', $first->nom);
        self::assertSame(1, $first->appId);
        self::assertTrue($first->mailSent);
        self::assertSame('15DaysAfter', $first->reminderKey);
        self::assertSame('2022-04-01', $first->reminderDate->format('Y-m-d'));

        $second = $logs[1];
        self::assertSame('Edmond', $second->prenom);
        self::assertSame('Dupont', $second->nom);
        self::assertSame('2022-03-01', $second->reminderDate->format('Y-m-d'));
    }

    public function testGetPaginatedLogsHidesCompanyWhenMemberIsPhysical(): void
    {
        $repository = self::getContainer()->get(SubscriptionReminderLogRepository::class);

        $logs = $repository->getPaginatedLogs();

        self::assertNull($logs[0]->apmId);
        self::assertNull($logs[0]->raisonSociale);
    }

    public function testGetPaginatedLogsReturnsCompanyInformation(): void
    {
        $repository = self::getContainer()->get(SubscriptionReminderLogRepository::class);

        $logs = $repository->getPaginatedLogs();

        self::assertSame(10, $logs[1]->apmId);
        self::assertSame('MyCorp', $logs[1]->raisonSociale);
        self::assertFalse($logs[1]->mailSent);
    }

    public function testGetPaginatedLogsIsPaginated(): void
    {
        $repository = self::getContainer()->get(SubscriptionReminderLogRepository::class);

        $pageOne = $repository->getPaginatedLogs(page: 1, limit: 2);
        $pageTwo = $repository->getPaginatedLogs(page: 2, limit: 2);

        // Une remontée de plus que la limite indique qu'une page suivante existe.
        self::assertCount(3, $pageOne);
        self::assertCount(1, $pageTwo);
        self::assertSame('2022-02-01', $pageTwo[0]->reminderDate->format('Y-m-d'));
    }

    public function testGetPaginatedLogsWithDeletedUserReturnsNullMemberInformation(): void
    {
        // Un log orphelin (utilisateur supprimé depuis) doit apparaître
        // sans information de membre au lieu de lever une exception.
        $this->connection->insert('afup_subscription_reminder_log', [
            'user_id' => 9999,
            'user_type' => 0,
            'email' => 'supprime@example.com',
            'reminder_date' => '2022-05-01',
            'reminder_key' => '15DaysAfter',
            'mail_sent' => 0,
        ]);

        $repository = self::getContainer()->get(SubscriptionReminderLogRepository::class);

        $logs = $repository->getPaginatedLogs();

        self::assertCount(4, $logs);
        self::assertSame('2022-05-01', $logs[0]->reminderDate->format('Y-m-d'));
        self::assertNull($logs[0]->nom);
        self::assertNull($logs[0]->prenom);
        self::assertNull($logs[0]->appId);
        self::assertFalse($logs[0]->mailSent);
    }
}
