<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\TalkInvitation;
use AppBundle\Event\Entity\Repository\TalkInvitationRepository;
use AppBundle\Event\Enum\TalkInvitationState;
use Doctrine\DBAL\Connection;

final class TalkInvitationRepositoryTest extends IntegrationTestCase
{
    public function testGetPendingInvitationsByTalkIdReturnsOnlyPendingInvitationsOfTalk(): void
    {
        $repository = self::getContainer()->get(TalkInvitationRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertInvitation($connection, 1, 'pending@example.com', TalkInvitationState::Pending->value);
        $this->insertInvitation($connection, 1, 'accepted@example.com', TalkInvitationState::Accepted->value);
        $this->insertInvitation($connection, 2, 'other-talk@example.com', TalkInvitationState::Pending->value);

        $invitations = $repository->getPendingInvitationsByTalkId(1);

        self::assertCount(1, $invitations);
        self::assertSame('pending@example.com', $invitations[0]->email);
        self::assertSame(TalkInvitationState::Pending, $invitations[0]->state);
        self::assertSame(1, $invitations[0]->talkId);
    }

    public function testGetPendingInvitationsByTalkIdReturnsEmptyArrayForUnknownTalk(): void
    {
        $repository = self::getContainer()->get(TalkInvitationRepository::class);

        self::assertSame([], $repository->getPendingInvitationsByTalkId(9999));
    }

    public function testSavePersistsInvitation(): void
    {
        $repository = self::getContainer()->get(TalkInvitationRepository::class);

        $invitation = new TalkInvitation();
        $invitation->talkId = 10;
        $invitation->submittedBy = 20;
        $invitation->submittedOn = new \DateTime('2026-09-21 10:00:00');
        $invitation->token = 'token-abc';
        $invitation->email = 'invite@example.com';
        $invitation->state = TalkInvitationState::Pending;

        $repository->save($invitation);

        $fromDatabase = $repository->findOneBy(['talkId' => 10, 'token' => 'token-abc']);
        self::assertInstanceOf(TalkInvitation::class, $fromDatabase);
        self::assertSame('invite@example.com', $fromDatabase->email);
        self::assertSame(20, $fromDatabase->submittedBy);
        self::assertSame(TalkInvitationState::Pending, $fromDatabase->state);
    }

    private function insertInvitation(Connection $connection, int $talkId, string $email, int $state): void
    {
        $connection->insert('afup_sessions_invitation', [
            'talk_id' => $talkId,
            'state' => $state,
            'submitted_on' => '2026-09-21 10:00:00',
            'submitted_by' => 20,
            'token' => 'token-' . md5($email),
            'email' => $email,
        ]);
    }
}
