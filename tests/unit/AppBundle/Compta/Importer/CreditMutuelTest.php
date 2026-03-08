<?php

declare(strict_types=1);

namespace AppBundle\Tests\Compta\Importer;

use AppBundle\Accounting\OperationType;
use AppBundle\Compta\Importer\CreditMutuel;
use AppBundle\Compta\Importer\Operation;
use PHPUnit\Framework\TestCase;

final class CreditMutuelTest extends TestCase
{
    public function testValidate(): void
    {
        $importerCmut = new CreditMutuel();
        $importerCmut->initialize(__DIR__ . '/_data/cmut.csv');
        self::assertTrue($importerCmut->validate());

        $importerCmut = new CreditMutuel();
        $importerCmut->initialize(__DIR__ . '/_data/cmut_extra_columns.csv');
        self::assertTrue($importerCmut->validate());

        $importerCE = new CreditMutuel();
        $importerCE->initialize(__DIR__ . '/_data/ce.csv');
        self::assertFalse($importerCE->validate());
    }

    public function testImportSimple(): void
    {
        $importer = new CreditMutuel();
        $importer->initialize(__DIR__ . '/_data/cmut.csv');

        $result = iterator_to_array($importer->extract());

        self::assertCount(3, $result);

        self::assertEquals(
            [
                new Operation('2022-01-20', 'VIR REMBOURSEMENT LA POSTE', 4.79, OperationType::Debit, 'b9b9f6e4dc1f923f654d'),
                new Operation('2022-02-22', 'VIR GOOGLE IRELAND LIMITED GG102QZGUA', 64.03, OperationType::Credit, '6518215a495b0182e609'),
                new Operation('2022-03-03', 'VIR FACTURE 22010177', 4592.0, OperationType::Debit, '96ef77c410b75945ecc3'),
            ],
            $result,
        );
    }

    public function testImportWithExtraColumns(): void
    {
        $importer = new CreditMutuel();
        $importer->initialize(__DIR__ . '/_data/cmut_extra_columns.csv');

        $result = iterator_to_array($importer->extract());

        self::assertCount(11, $result);

        self::assertEquals(
            [
                new Operation('2025-12-08', 'REMCB46461 NB0001 TPE301830201', 352.0, OperationType::Credit, '60c80ce78e7b90f8f1fb'),
                new Operation('2025-12-08', 'COMCB46461 NB0001 TPE301830201', 4.98, OperationType::Debit, '88b2d028d107b4f0da4a'),
                new Operation('2025-12-09', 'PRLV SEPA ACME FACTURE 1234-0028641', 12.60, OperationType::Debit, '563ed5a9e0ebb3173805'),
                new Operation('2025-12-09', 'REMCB54124 NB0002 TPE301830201', 423.50, OperationType::Credit, '44ad5dd78b17f817e6dc'),
                new Operation('2025-12-09', 'COMCB54124 NB0002 TPE301830201', 5.53, OperationType::Debit, '29a5b7321b107e8f597a'),
                new Operation('2025-12-10', 'FACT SGT25060760014943 DONT TVA 94EUR', 31.90, OperationType::Debit, '8f12199a4fa331af037b'),
                new Operation('2025-12-10', 'REMCB56989 NB0002 TPE301830201', 60.0, OperationType::Credit, 'e192620c9b2a8792d6bf'),
                new Operation('2025-12-10', 'COMCB56989 NB0002 TPE301830201', 0.73, OperationType::Debit, '77c775c314061e3e642e'),
                new Operation('2025-12-11', 'REMCB59844 NB0002 TPE301830201', 101.50, OperationType::Credit, '7c31cf662cf3cf2e3183'),
                new Operation('2025-12-11', 'COMCB59844 NB0002 TPE301830201', 1.26, OperationType::Debit, '5f2887f2cf772aeb2bd8'),
                new Operation('2025-12-11', 'VIR INST FAKE CORP 2025-123 2025 4412745908074573', 4161.0, OperationType::Credit, 'a40bc6292fe3e02f52cf'),
            ],
            $result,
        );
    }
}
