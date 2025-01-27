<?php

declare(strict_types=1);

namespace Afup\Site\Association\tests\units;

use Afup\Site\Utils\Base_De_Donnees;
use AppBundle\Association\Model\Repository\UserRepository;

class Cotisations extends \atoum
{
    protected function generateCotisationProvider()
    {
        return [
            [
                'case' => 'La cotisation précédente a expiré il y a plus d\'un an, la nouvelle cotisation doit expirer dans un an',
                'date_fin' => (new \DateTime('18 months ago')),
                'expected' => new \DateTime('+1 year'),
            ],
            [
                'case' => 'La cotisation précédente a expiré hier, la nouvelle cotisation doit expirer dans un an',
                'date_fin' => (new \DateTime('yesterday')),
                'expected' => new \DateTime('+1 year'),
            ],
            [
                'case' => 'La cotisation précédente a expiré aujourd\'hui, la nouvelle cotisation doit expirer dans un an',
                'date_fin' => (new \DateTime()),
                'expected' => new \DateTime('+1 year'),
            ],
            [
                'case' => 'La cotisation précédente expire dans 1 mois, la nouvelle cotisation doit expirer dans 13 mois',
                'date_fin' => (new \DateTimeImmutable('+1 month'))->setTime(14, 0),
                'expected' => (new \DateTimeImmutable('+1 month'))->setTime(14, 0)->add(new \DateInterval('P1Y')),
            ],
        ];
    }

    /**
     * @dataProvider generateCotisationProvider
     */
    public function testFinProchaineCotisation($case, $dateFin, $expected): void
    {
        $bdd = $this->newMockInstance(Base_De_Donnees::class, null, null, [
            'hostname',
            'database',
            'user',
            'password',
        ]);

        $this
            ->given(
                $cotisations = new \Afup\Site\Association\Cotisations($bdd)
            )
            ->then
                ->string($cotisations->finProchaineCotisation(['date_fin' => $dateFin->format('U')])->format('Y-m-d'))
                    ->isEqualTo($expected->format('Y-m-d'), $case)
        ;
    }

    protected function accountCmdProvider()
    {
        return [
            [
                'Personne Morale',
                'FCOTIS-2023-202',
                ['type' => UserRepository::USER_TYPE_COMPANY, 'id' => '202']
            ],
            [
                'Personne physique',
                'C2023-211120232237-0-5-PAUL-431',
                ['type' => UserRepository::USER_TYPE_PHYSICAL, 'id' => '5']
            ],
        ];
    }

    /**
     * @dataProvider accountCmdProvider
     */
    public function testGetAccountFromCmd($case, $cmd, $expected): void
    {
        $bdd = $this->newMockInstance(Base_De_Donnees::class, null, null, [
            'hostname',
            'database',
            'user',
            'password',
        ]);

        $this
            ->given(
                $cotisations = new \Afup\Site\Association\Cotisations($bdd)
            )
            ->then
            ->array($cotisations->getAccountFromCmd($cmd))
            ->isEqualTo($expected, $case)
        ;
    }
}
