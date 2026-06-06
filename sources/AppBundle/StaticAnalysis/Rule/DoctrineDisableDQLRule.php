<?php

declare(strict_types=1);

namespace AppBundle\StaticAnalysis\Rule;

use PhpParser\Node\Identifier;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Type;

/**
 * @implements Rule<MethodCall>
 */
final readonly class DoctrineDisableDQLRule implements Rule
{
    private ClassReflection $entityManagerClass;

    public function __construct(private ReflectionProvider $reflectionProvider)
    {
        $this->entityManagerClass = $this->reflectionProvider->getClass(EntityManagerInterface::class);
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Identifier) {
            return [];
        }

        if ($node->name->toLowerString() !== 'createquery') {
            return [];
        }

        $calledOnType = $scope->getType($node->var);

        if ($this->isEntityManager($calledOnType)) {
            return [
                RuleErrorBuilder::message('DQL is forbidden, use the query builder instead.')
                    ->identifier('afup.doctrine.noDQL')
                    ->build(),
            ];
        }

        return [];
    }

    private function isEntityManager(Type $type): bool
    {
        return array_any($type->getObjectClassNames(), fn($className): bool => $className === $this->entityManagerClass->getName()
        || $this->reflectionProvider->getClass($className)->isSubclassOfClass($this->entityManagerClass));
    }
}
