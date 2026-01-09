<?php

declare(strict_types=1);

namespace AppBundle\Tests\StaticAnalysis\Rule;

use AppBundle\StaticAnalysis\Rule\DoctrineRepositoryRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

final class DoctrineRepositoryRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new DoctrineRepositoryRule($this->createReflectionProvider());
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../../../../stubs/phpstan/Doctrine/SomeExampleClass.php'], [
            ['Calling method createQueryBuilder() outside of repository classes is not allowed.', 20],
            ['Calling method createResultSetMappingBuilder() outside of repository classes is not allowed.', 21],
            ['Calling method getClassName() outside of repository classes is not allowed.', 24],
            ['Calling method matching() outside of repository classes is not allowed.', 25],
            ['Calling method count() outside of repository classes is not allowed.', 26],

            ['Calling method createQueryBuilder() outside of repository classes is not allowed.', 28],
            ['Calling method createResultSetMappingBuilder() outside of repository classes is not allowed.', 29],
            ['Calling method getClassName() outside of repository classes is not allowed.', 32],
            ['Calling method matching() outside of repository classes is not allowed.', 33],
            ['Calling method count() outside of repository classes is not allowed.', 34],
        ]);

        $this->analyse([__DIR__ . '/../../../../stubs/phpstan/Doctrine/WithoutOverridesRepository.php'], []);
        $this->analyse([__DIR__ . '/../../../../stubs/phpstan/Doctrine/WithOverridesRepository.php'], []);
        $this->analyse([__DIR__ . '/../../../../stubs/phpstan/Doctrine/SubClassRepository.php'], []);
        $this->analyse([__DIR__ . '/../../../../stubs/phpstan/Doctrine/NotRepository.php'], []);
        $this->analyse([__DIR__ . '/../../../../stubs/phpstan/Doctrine/SubNotRepository.php'], []);
    }
}
