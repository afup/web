<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\StaticAnalysis\Rule;

use AppBundle\StaticAnalysis\Rule\NoGetUserMethodRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class NoGetUserMethodRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new NoGetUserMethodRule($this->createReflectionProvider());
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../../../../stubs/phpstan/ControllerWithGetUser.php'], [
            ["Don't use getUser() in a Controller, inject the Authentication service instead.", 13],
        ]);

        $this->analyse([__DIR__ . '/../../../../stubs/phpstan/GetUserInNotController.php'], []);
    }
}
