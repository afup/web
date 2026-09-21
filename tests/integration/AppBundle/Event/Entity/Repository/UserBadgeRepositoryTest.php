<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\Badge;
use AppBundle\Event\Entity\Repository\BadgeRepository;
use AppBundle\Event\Entity\Repository\UserBadgeRepository;
use AppBundle\Event\Entity\UserBadge;
use Doctrine\DBAL\Connection;

final class UserBadgeRepositoryTest extends IntegrationTestCase
{
    public function testFindByUserIdReturnsBadgesJoinedAndSortedByIssueDate(): void
    {
        $userBadgeRepository = self::getContainer()->get(UserBadgeRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertBadge($connection, 1, 'Speaker 2025');
        $this->insertBadge($connection, 2, 'Membre depuis 10 ans');
        $this->insertUserBadge($connection, 42, 2, '2025-01-01');
        $this->insertUserBadge($connection, 42, 1, '2026-06-15');
        $this->insertUserBadge($connection, 43, 1, '2026-01-01');

        $userBadges = $userBadgeRepository->findByUserId(42);

        self::assertCount(2, $userBadges);
        // Tri par date d'attribution croissante
        self::assertSame(2, $userBadges[0]->badge->id);
        self::assertSame('Membre depuis 10 ans', $userBadges[0]->badge->label);
        self::assertSame('2025-01-01', $userBadges[0]->issuedAt->format('Y-m-d'));
        self::assertSame(1, $userBadges[1]->badge->id);
        self::assertSame(42, $userBadges[0]->userId);
    }

    public function testFindByUserIdReturnsEmptyArrayForUserWithoutBadge(): void
    {
        $userBadgeRepository = self::getContainer()->get(UserBadgeRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertBadge($connection, 1, 'Speaker 2025');
        $this->insertUserBadge($connection, 42, 1, '2025-01-01');

        self::assertSame([], $userBadgeRepository->findByUserId(999));
    }

    public function testSaveAndDeleteThroughTheEntityManager(): void
    {
        $userBadgeRepository = self::getContainer()->get(UserBadgeRepository::class);
        $badgeRepository = self::getContainer()->get(BadgeRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $badge = new Badge();
        $badge->label = 'Speaker 2026';
        $badge->url = 'badge_speaker_2026.png';
        $badgeRepository->save($badge);

        $userBadge = new UserBadge();
        $userBadge->badge = $badge;
        $userBadge->userId = 42;
        $userBadge->issuedAt = new \DateTime('2026-09-21');
        $userBadgeRepository->save($userBadge);

        $rows = $connection->fetchAllAssociative(
            'SELECT * FROM afup_personnes_physiques_badge WHERE afup_personne_physique_id = :userId',
            ['userId' => 42],
        );
        self::assertCount(1, $rows);
        self::assertSame($badge->id, (int) $rows[0]['badge_id']);
        self::assertSame('2026-09-21', $rows[0]['issued_at']);

        $fromDatabase = $userBadgeRepository->findOneBy([
            'userId' => 42,
            'badge' => $badge,
        ]);
        self::assertNotNull($fromDatabase);

        $userBadgeRepository->delete($fromDatabase);
        self::assertSame(
            [],
            $connection->fetchAllAssociative('SELECT * FROM afup_personnes_physiques_badge WHERE afup_personne_physique_id = :userId', ['userId' => 42]),
        );
    }

    private function insertBadge(Connection $connection, int $id, string $label): void
    {
        $connection->insert('afup_badge', [
            'id' => $id,
            'label' => $label,
            'url' => 'badge_' . $id . '.png',
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
