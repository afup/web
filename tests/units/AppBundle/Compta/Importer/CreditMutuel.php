<?php

declare(strict_types=1);

namespace AppBundle\Compta\Importer\test\units;

use AppBundle\Compta\Importer\CreditMutuel as TestedClass;
use AppBundle\Compta\Importer\Operation;

class CreditMutuel extends \atoum
{
    public function testValidate(): void
    {
        $importerCmut = new TestedClass();
        $importerCmut->initialize(__DIR__ . '/_data/cmut.csv');
        $this->boolean($importerCmut->validate())->isTrue();


        $importerCE = new TestedClass();
        $importerCE->initialize(__DIR__ . '/_data/ce.csv');
        $this->boolean($importerCE->validate())->isFalse();
    }

    public function testExtract(): void
    {
        $importer = new TestedClass();
        $importer->initialize(__DIR__ . '/_data/cmut.csv');

        $this->boolean(true)->isTrue;

        /** @var Operation[] $operations */
        $operations = iterator_to_array($importer->extract());
        $this->phpArray($operations)
            ->hasSize(3);

        $this->object($currentOperation = $operations[0])
            ->isInstanceOf(Operation::class)
                ->string($currentOperation->getDateEcriture())->isEqualTo('2022-01-20')
                ->string($currentOperation->getDescription())->isEqualTo('VIR REMBOURSEMENT LA POSTE')
                ->string($currentOperation->getType())->isEqualTo(Operation::DEBIT)
                ->string($currentOperation->getNumeroOperation())->isEqualTo('b9b9f6e4dc1f923f654d')
                ->float($currentOperation->getMontant())->isEqualTo(4.79)
        ;

        $this->object($currentOperation = $operations[1])
            ->isInstanceOf(Operation::class)
            ->string($currentOperation->getDateEcriture())->isEqualTo('2022-02-22')
            ->string($currentOperation->getDescription())->isEqualTo('VIR GOOGLE IRELAND LIMITED GG102QZGUA')
            ->string($currentOperation->getType())->isEqualTo(Operation::CREDIT)
            ->string($currentOperation->getNumeroOperation())->isEqualTo('6518215a495b0182e609')
            ->float($currentOperation->getMontant())->isEqualTo(64.03)
        ;

        $this->object($currentOperation = $operations[2])
            ->isInstanceOf(Operation::class)
            ->string($currentOperation->getDateEcriture())->isEqualTo('2022-03-03')
            ->string($currentOperation->getDescription())->isEqualTo('VIR FACTURE 22010177')
            ->string($currentOperation->getType())->isEqualTo(Operation::DEBIT)
            ->string($currentOperation->getNumeroOperation())->isEqualTo('96ef77c410b75945ecc3')
            ->float($currentOperation->getMontant())->isEqualTo(4592)
        ;
    }
}
