<?php

namespace Afup\Site\Association\tests\units;


use Afup\Site\Utils\Base_De_Donnees;

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
    public function testFinProchaineCotisation($case, $dateFin, $expected)
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
}
