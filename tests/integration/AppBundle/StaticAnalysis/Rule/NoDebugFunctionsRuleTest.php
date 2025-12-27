<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\StaticAnalysis\Rule;

use AppBundle\StaticAnalysis\Rule\NoDebugFunctionsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class NoDebugFunctionsRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new NoDebugFunctionsRule();
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../../../../stubs/phpstan/debug-functions.php'], [
            ['Usage of var_dump() is forbidden.', 5],
            ['Usage of print_r() is forbidden.', 7],
            ['Usage of dump() is forbidden.', 9],
            ['Usage of dd() is forbidden.', 11],
        ]);
    }
}
