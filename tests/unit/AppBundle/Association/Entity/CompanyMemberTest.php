<?php

declare(strict_types=1);

namespace AppBundle\Tests\Association\Entity;

use AppBundle\Association\CompanyMembership\SubscriptionManagement;
use AppBundle\Association\Entity\CompanyMember;
use AppBundle\Association\Entity\CompanyMemberInvitation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CompanyMemberTest extends TestCase
{
    #[DataProvider('companies')]
    public function testMembershipFee(?int $maxMembers, float $expectedAmount): void
    {
        $companyMember = new CompanyMember();
        $companyMember->maxMembers = $maxMembers;

        self::assertEquals($expectedAmount, $companyMember->getMembershipFee());
    }

    /**
     * @return array<string, array{?int, float}>
     */
    public static function companies(): array
    {
        return [
            'null' => [null, SubscriptionManagement::AFUP_COTISATION_PERSONNE_MORALE],
            'zero' => [0, SubscriptionManagement::AFUP_COTISATION_PERSONNE_MORALE],
            'under' => [2, SubscriptionManagement::AFUP_COTISATION_PERSONNE_MORALE],
            'equal' => [3, SubscriptionManagement::AFUP_COTISATION_PERSONNE_MORALE],
            'just over' => [4, 2 * SubscriptionManagement::AFUP_COTISATION_PERSONNE_MORALE],
            'over' => [6, 2 * SubscriptionManagement::AFUP_COTISATION_PERSONNE_MORALE],
        ];
    }

    public function testHasUpToDateMembershipFee(): void
    {
        $now = new \DateTimeImmutable('2026-06-15 12:00:00');
        $companyMember = new CompanyMember();

        self::assertFalse($companyMember->hasUpToDateMembershipFee($now), 'sans cotisation');

        $companyMember->lastSubscription = $now->modify('+1 day');
        self::assertTrue($companyMember->hasUpToDateMembershipFee($now));

        $companyMember->lastSubscription = $now->modify('-1 day');
        self::assertFalse($companyMember->hasUpToDateMembershipFee($now));
    }

    public function testFormattedRelatedAfupOffices(): void
    {
        $companyMember = new CompanyMember();
        self::assertSame([], $companyMember->getFormattedRelatedAfupOffices());

        $companyMember->setFormattedRelatedAfupOffices(['lyon', 'bordeaux']);
        self::assertSame('bordeaux,lyon', $companyMember->relatedAfupOffices);
        self::assertSame(['bordeaux', 'lyon'], $companyMember->getFormattedRelatedAfupOffices());

        $companyMember->setFormattedRelatedAfupOffices([]);
        self::assertNull($companyMember->relatedAfupOffices);
    }

    #[DataProvider('twitterHandles')]
    public function testCleanedTwitterHandle(?string $handle, ?string $expected): void
    {
        $companyMember = new CompanyMember();
        $companyMember->twitterHandle = $handle;

        self::assertSame($expected, $companyMember->getCleanedTwitterHandle());
    }

    /**
     * @return array<string, array{?string, ?string}>
     */
    public static function twitterHandles(): array
    {
        return [
            'null' => [null, null],
            'vide' => ['', null],
            'arobase' => ['@afup', 'afup'],
            'url' => ['https://twitter.com/afup', 'afup'],
        ];
    }

    public function testSlug(): void
    {
        $companyMember = new CompanyMember();
        $companyMember->companyName = 'Société Générale & Co';

        self::assertSame('societe-generale-co', $companyMember->getSlug());
    }

    public function testValidInvitationsIgnoresEmptyEntries(): void
    {
        $invitation = new CompanyMemberInvitation();
        $companyMember = new CompanyMember();
        $companyMember->invitations = [null, $invitation, null];

        self::assertSame([1 => $invitation], $companyMember->getValidInvitations());
    }
}
