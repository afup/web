<?php

declare(strict_types=1);

namespace AppBundle\Tests\Compta\Importer;

use AppBundle\Compta\Importer\CreditMutuel;
use PHPUnit\Framework\TestCase;

final class CreditMutuelTest extends TestCase
{
    public function testValidate(): void
    {
        $importerCmut = new CreditMutuel();
        $importerCmut->initialize(__DIR__ . '/_data/cmut.csv');
        self::assertTrue($importerCmut->validate());

        $importerCE = new CreditMutuel();
        $importerCE->initialize(__DIR__ . '/_data/ce.csv');
        self::assertFalse($importerCE->validate());
    }
}
