<?php

declare(strict_types=1);

namespace AppBundle\StaticAnalysis\Rule;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;

/**
 * @implements Rule<MethodCall>
 */
final class DoctrineRepositoryRule implements Rule
{
    private const FORBIDDEN_METHODS = [
        'createQueryBuilder',
    ];

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {}

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Node\Identifier) {
            return [];
        }

        $methodName = $node->name->toString();

        if (!in_array($methodName, self::FORBIDDEN_METHODS, true)) {
            return [];
        }

        $callerType = $scope->getType($node->var);

        // Check if the caller is a repository (extends ServiceEntityRepository)
        $serviceEntityRepositoryType = new ObjectType(ServiceEntityRepository::class);
        if (!$serviceEntityRepositoryType->isSuperTypeOf($callerType)->yes()) {
            return [];
        }

        $serviceEntityClassReflection = $this->reflectionProvider->getClass(ServiceEntityRepository::class);

        // Check if we're inside a repository class
        $classReflection = $scope->getClassReflection();
        if ($classReflection !== null) {
            // Allow calls if we're inside a class that extends ServiceEntityRepository
            if ($classReflection->isSubclassOfClass($serviceEntityClassReflection)) {
                return [];
            }
        }

        // This is a forbidden call - report an error
        return [
            RuleErrorBuilder::message(
                sprintf(
                    'Calling method %s() on a repository is not allowed outside of repository classes. ' .
                    'Consider creating a custom method in your repository that wraps this functionality.',
                    $methodName
                )
            )
                ->identifier('repository.directMethodCall')
                ->build(),
        ];
    }
}
