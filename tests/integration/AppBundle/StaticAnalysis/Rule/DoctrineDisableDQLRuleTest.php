<?php

declare(strict_types=1);

namespace AppBundle\Tests\StaticAnalysis\Rule;

use AppBundle\StaticAnalysis\Rule\DoctrineDisableDQLRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class DoctrineDisableDQLRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new DoctrineDisableDQLRule($this->createReflectionProvider());
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../../../../stubs/phpstan/Doctrine/DQLRepository.php'], [
            ['DQL is forbidden, use the query builder instead.', 13],
        ]);
    }
}
