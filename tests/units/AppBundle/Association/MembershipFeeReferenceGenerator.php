<?php

namespace AppBundle\Association\tests\units;

use AppBundle\Association\MembershipFeeReferenceGenerator as TestedClass;

class MembershipFeeReferenceGenerator extends \atoum
{
    /**
     * @dataProvider generateDateProvider
     */
    public function testGenerate($case, $currentDate, $typePersonne, $idPersonne, $nom, $expected)
    {
        $this
            ->assert($case)
            ->when($generator = new TestedClass())
            ->then
            ->string($generator->generate($currentDate, $typePersonne, $idPersonne, $nom))
                ->isEqualTo($expected, $case)
        ;
    }

    protected function generateDateProvider()
    {
        return [
            [
                'case' => 'Cas général',
                'current_date' => new \DateTimeImmutable('2018-03-02 20:20:19'),
                'type_personne' => 0,
                'id_personne' => 1234,
                'nom' => 'DUPONT',
                'expected' => "C2018-020320182020-0-1234-DUPON-875",
            ],
        ];
    }
}
