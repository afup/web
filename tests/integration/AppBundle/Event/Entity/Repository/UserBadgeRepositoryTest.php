<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\Repository\UserBadgeRepository;
use AppBundle\Event\Entity\UserBadge;
use Doctrine\DBAL\Connection;

final class UserBadgeRepositoryTest extends IntegrationTestCase
{
    public function testSavePersistsTheAssociation(): void
    {
        $repository = self::getContainer()->get(UserBadgeRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertBadge($connection, 1, 'Speaker 2025');
        $repository->save($this->buildUserBadge(101, 1, '2025-09-01'));

        $rows = $connection->fetchAllAssociative(
            'SELECT afup_personne_physique_id, badge_id, issued_at FROM afup_personnes_physiques_badge WHERE afup_personne_physique_id = 101',
        );

        self::assertCount(1, $rows);
        self::assertSame('101', (string) $rows[0]['afup_personne_physique_id']);
        self::assertSame('1', (string) $rows[0]['badge_id']);
        self::assertSame('2025-09-01', (string) $rows[0]['issued_at']);
    }

    public function testFindByUserIdReturnsBadgesWithLabelOrderedByDate(): void
    {
        $repository = self::getContainer()->get(UserBadgeRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertBadge($connection, 1, 'Speaker 2025');
        $this->insertBadge($connection, 2, 'Benevole 2024');
        $this->insertUserBadge($connection, 101, 1, '2025-01-01');
        $this->insertUserBadge($connection, 101, 2, '2024-01-01');

        $badges = $repository->findByUserId(101);

        // Tri par date d'attribution croissante
        self::assertCount(2, $badges);
        self::assertSame(101, $badges[0]->userId);
        self::assertSame(2, $badges[0]->badgeId);
        self::assertSame('Benevole 2024', $badges[0]->badgeLabel);
        self::assertSame('2024-01-01', $badges[0]->issuedAt->format('Y-m-d'));
        self::assertSame(1, $badges[1]->badgeId);
        self::assertSame('Speaker 2025', $badges[1]->badgeLabel);
        self::assertSame('2025-01-01', $badges[1]->issuedAt->format('Y-m-d'));
    }

    public function testFindByUserIdReturnsEmptyListForUnknownUser(): void
    {
        $repository = self::getContainer()->get(UserBadgeRepository::class);

        self::assertSame([], $repository->findByUserId(9999));
    }

    public function testDeleteRemovesTheAssociation(): void
    {
        $repository = self::getContainer()->get(UserBadgeRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertBadge($connection, 1, 'Speaker 2025');
        $this->insertUserBadge($connection, 101, 1, '2025-01-01');

        $userBadge = $repository->find(['badgeId' => 1, 'userId' => 101]);
        self::assertInstanceOf(UserBadge::class, $userBadge);

        $repository->delete($userBadge);

        $rows = $connection->fetchAllAssociative(
            'SELECT badge_id FROM afup_personnes_physiques_badge WHERE afup_personne_physique_id = 101',
        );

        self::assertSame([], $rows);
    }

    private function buildUserBadge(int $userId, int $badgeId, string $issuedAt): UserBadge
    {
        $userBadge = new UserBadge();
        $userBadge->userId = $userId;
        $userBadge->badgeId = $badgeId;
        $userBadge->issuedAt = new \DateTimeImmutable($issuedAt);

        return $userBadge;
    }

    private function insertBadge(Connection $connection, int $id, string $label): void
    {
        $connection->insert('afup_badge', [
            'id' => $id,
            'label' => $label,
            'url' => 'https://afup.org/images/badges/test.png',
        ]);
    }

    private function insertUserBadge(Connection $connection, int $userId, int $badgeId, string $issuedAt): void
    {
        $connection->insert('afup_personnes_physiques_badge', [
            'afup_personne_physique_id' => $userId,
            'badge_id' => $badgeId,
            'issued_at' => $issuedAt,
        ]);
    }
}
