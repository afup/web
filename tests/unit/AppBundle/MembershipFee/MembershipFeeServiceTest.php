<?php

declare(strict_types=1);

namespace AppBundle\Tests\MembershipFee;

use AppBundle\Association\MemberType;
use AppBundle\MembershipFee\MembershipFeeService;
use AppBundle\MembershipFee\Model\MembershipFee;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use AppBundle\MembershipFee\OnlinePaymentHandler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class MembershipFeeServiceTest extends TestCase
{
    public static function generateCotisationProvider(): array
    {
        return [
            'La cotisation précédente a expiré il y a plus d\'un an, la nouvelle cotisation doit expirer dans un an' => [
                'dateFin' => (new \DateTime('18 months ago')),
                'expected' => new \DateTime('+1 year'),
            ],
            'La cotisation précédente a expiré hier, la nouvelle cotisation doit expirer dans un an' => [
                'dateFin' => (new \DateTime('yesterday')),
                'expected' => new \DateTime('+1 year'),
            ],
            'La cotisation précédente a expiré aujourd\'hui, la nouvelle cotisation doit expirer dans un an' => [
                'dateFin' => (new \DateTime()),
                'expected' => new \DateTime('+1 year'),
            ],
            'La cotisation précédente expire dans 1 mois, la nouvelle cotisation doit expirer dans 13 mois' => [
                'dateFin' => new \DateTimeImmutable('+1 month')->setTime(14, 0),
                'expected' => new \DateTimeImmutable('+1 month')->setTime(14, 0)->add(new \DateInterval('P1Y')),
            ],
            'Date d\'expiration pendant les heures d\'été' => [
                'dateFin' => (new \DateTime('2026-08-13 00:45:00')),
                'expected' => new \DateTime('2027-08-13 00:45:00'),
                'now' => new \DateTimeImmutable('2026-08-13 00:45:00'),
            ],
            'Date d\'expiration pendant les heures d\'hiver' => [
                'dateFin' => (new \DateTime('2026-02-13 00:45:00')),
                'expected' => new \DateTime('2027-02-13 00:45:00'),
                'now' => new \DateTimeImmutable('2026-02-13 00:45:00'),
            ],
        ];
    }

    #[DataProvider('generateCotisationProvider')]
    public function testFinProchaineCotisation(\DateTimeInterface $dateFin, \DateTimeInterface $expected, ?\DateTimeImmutable $now = null): void
    {
        $membershipFeeRepository = $this->createStub(MembershipFeeRepository::class);

        $membershipFeeService = new MembershipFeeService($membershipFeeRepository, new MockClock($now ?? new \DateTimeImmutable()));

        $membershipFee = new MembershipFee();
        $membershipFee->setEndDate(new \DateTime('@' . $dateFin->format('U')));

        $actual = $membershipFeeService->getNextSubscriptionExpiration($membershipFee);

        self::assertEquals($expected->format('Y-m-d'), $actual->format('Y-m-d'));
    }

    public static function accountCmdProvider(): array
    {
        return [
            'Personne Morale' => [
                'FCOTIS-2023-202',
                ['type' => MemberType::MemberCompany->value, 'id' => '202'],
            ],
            'Personne physique' => [
                'C2023-211120232237-0-5-PAUL-431',
                ['type' => MemberType::MemberPhysical->value, 'id' => '5'],
            ],
        ];
    }

    #[DataProvider('accountCmdProvider')]
    public function testGetAccountFromCmd(string $cmd, array $expected): void
    {
        $membershipFeeRepository = $this->createStub(MembershipFeeRepository::class);

        $membershipFeeService = new MembershipFeeService($membershipFeeRepository, new MockClock());

        $actual = new OnlinePaymentHandler(
            $membershipFeeService,
        )->getAccountFromCmd($cmd);

        self::assertEquals($expected, $actual);
    }
}
