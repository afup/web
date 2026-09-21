<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\TalkInvitation;
use AppBundle\Event\Enum\TalkInvitationState;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TalkInvitationValidationTest extends IntegrationTestCase
{
    public function testValidInvitationPassesValidation(): void
    {
        self::assertCount(0, $this->violationsFor($this->createValidInvitation()));
    }

    public function testInvalidEmailTriggersValidationError(): void
    {
        $invitation = $this->createValidInvitation();
        $invitation->email = 'not-an-email';

        self::assertGreaterThan(0, count($this->violationsFor($invitation)));
    }

    public function testBlankEmailTriggersValidationError(): void
    {
        $invitation = $this->createValidInvitation();
        $invitation->email = '';

        self::assertGreaterThan(0, count($this->violationsFor($invitation)));
    }

    public function testBlankTokenTriggersValidationError(): void
    {
        $invitation = $this->createValidInvitation();
        $invitation->token = '';

        self::assertGreaterThan(0, count($this->violationsFor($invitation)));
    }

    public function testTalkIdBelowOneTriggersValidationError(): void
    {
        $invitation = $this->createValidInvitation();
        $invitation->talkId = 0;

        self::assertGreaterThan(0, count($this->violationsFor($invitation)));
    }

    public function testSubmittedByZeroTriggersValidationError(): void
    {
        $invitation = $this->createValidInvitation();
        $invitation->submittedBy = 0;

        self::assertGreaterThan(0, count($this->violationsFor($invitation)));
    }

    private function violationsFor(TalkInvitation $invitation): ConstraintViolationListInterface
    {
        return self::getContainer()->get(ValidatorInterface::class)->validate($invitation);
    }

    private function createValidInvitation(): TalkInvitation
    {
        $invitation = new TalkInvitation();
        $invitation->talkId = 42;
        $invitation->submittedBy = 20;
        $invitation->submittedOn = new \DateTime('2026-09-21 10:00:00');
        $invitation->token = 'token-abc';
        $invitation->email = 'invite@example.com';
        $invitation->state = TalkInvitationState::Pending;

        return $invitation;
    }
}
