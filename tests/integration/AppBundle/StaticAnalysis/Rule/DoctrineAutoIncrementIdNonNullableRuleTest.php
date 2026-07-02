<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\StaticAnalysis\Rule;

use AppBundle\StaticAnalysis\Rule\DoctrineAutoIncrementIdNonNullable;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class DoctrineAutoIncrementIdNonNullableRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new DoctrineAutoIncrementIdNonNullable();
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../../../../stubs/phpstan/Doctrine/DoctrineEntitiesWithInvalidIds.php'], [
            ['The "a" property is an auto-incremented id so it must be non-nullable.', 12],
            ['The "b" property is an auto-incremented id so it must be non-nullable.', 21],
            ['The "c" property is an auto-incremented id so it must be non-nullable.', 30],
            ['The "d" property is an auto-incremented id so it must be non-nullable.', 39],
        ]);

        $this->analyse([__DIR__ . '/../../../../stubs/phpstan/Doctrine/DoctrineEntitiesWithValidIds.php'], []);
    }
}
