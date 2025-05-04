<?php

declare(strict_types=1);

namespace Afup\Site\Tests\Association;

use Afup\Site\Association\Cotisations;
use Afup\Site\Utils\Base_De_Donnees;
use AppBundle\Association\Model\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

final class CotisationsTest extends TestCase
{
    public function generateCotisationProvider(): array
    {
        return [
            'La cotisation précédente a expiré il y a plus d\'un an, la nouvelle cotisation doit expirer dans un an' => [
                'date_fin' => (new \DateTime('18 months ago')),
                'expected' => new \DateTime('+1 year'),
            ],
            'La cotisation précédente a expiré hier, la nouvelle cotisation doit expirer dans un an' => [
                'date_fin' => (new \DateTime('yesterday')),
                'expected' => new \DateTime('+1 year'),
            ],
            'La cotisation précédente a expiré aujourd\'hui, la nouvelle cotisation doit expirer dans un an' => [
                'date_fin' => (new \DateTime()),
                'expected' => new \DateTime('+1 year'),
            ],
            'La cotisation précédente expire dans 1 mois, la nouvelle cotisation doit expirer dans 13 mois' => [
                'date_fin' => (new \DateTimeImmutable('+1 month'))->setTime(14, 0),
                'expected' => (new \DateTimeImmutable('+1 month'))->setTime(14, 0)->add(new \DateInterval('P1Y')),
            ],
        ];
    }

    /**
     * @dataProvider generateCotisationProvider
     */
    public function testFinProchaineCotisation(\DateTimeInterface $dateFin, \DateTimeInterface $expected): void
    {
        $bdd = new Base_De_Donnees('', '', '', '');

        $cotisations = new Cotisations($bdd);

        $actual = $cotisations->finProchaineCotisation(['date_fin' => $dateFin->format('U')]);

        self::assertEquals($expected->format('Y-m-d'), $actual->format('Y-m-d'));
    }

    public function accountCmdProvider(): array
    {
        return [
            'Personne Morale' => [
                'FCOTIS-2023-202',
                ['type' => UserRepository::USER_TYPE_COMPANY, 'id' => '202'],
            ],
            'Personne physique' => [
                'C2023-211120232237-0-5-PAUL-431',
                ['type' => UserRepository::USER_TYPE_PHYSICAL, 'id' => '5'],
            ],
        ];
    }

    /**
     * @dataProvider accountCmdProvider
     */
    public function testGetAccountFromCmd(string $cmd, array $expected): void
    {
        $bdd = new Base_De_Donnees('', '', '', '');

        $cotisations = new Cotisations($bdd);

        $actual = $cotisations->getAccountFromCmd($cmd);

        self::assertEquals($expected, $actual);
    }
}
