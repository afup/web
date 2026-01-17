<?php

declare(strict_types=1);

namespace AppBundle\StaticAnalysis\Rule;

use Doctrine\ORM\EntityRepository;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Type;
use Symfony\Component\Form\AbstractType;

/**
 * @implements Rule<MethodCall>
 */
final readonly class DoctrineRepositoryRule implements Rule
{
    private const FORBIDDEN_METHODS = [
        'createQueryBuilder',
        'createResultSetMappingBuilder',
        'getClassName',
        'matching',
        'count',
    ];

    private ClassReflection $repositoryClassReflection;
    private ClassReflection $formClassReflection;

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {
        $this->repositoryClassReflection = $this->reflectionProvider->getClass(EntityRepository::class);
        $this->formClassReflection = $this->reflectionProvider->getClass(AbstractType::class);
    }

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

        if (
            // Seules certaines méthodes sont interdites
            !in_array($methodName, self::FORBIDDEN_METHODS, true)

            // On vérifie qu'on est bien dans une classe
            || !$scope->isInClass()

            // Si l'appel est fait depuis l'intérieur d'un repository, c'est autorisé
            || $this->isRepositoryClass($scope->getClassReflection())
            // Si l'appel est fait depuis l'intérieur d'un formulaire, c'est autorisé
            || $this->isFormClass($scope->getClassReflection())
        ) {
            return [];
        }

        $calledOnType = $scope->getType($node->var);

        // Si la méthode est déclarée dans une classe qui n'est pas un repository, c'est autorisé
        if (!$this->isRepositoryClass($calledOnType)) {
            return [];
        }

        // Si la méthode est surchargée, c'est autorisé
        if ($this->isMethodOverridden($calledOnType, $methodName)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf('Calling method %s() outside of repository classes is not allowed.', $methodName))
                ->identifier('afup.doctrine.repositoryMethods')
                ->build(),
        ];
    }

    private function isRepositoryClass(ClassReflection|Type $target): bool
    {
        if ($target instanceof Type) {
            $classes = $target->getObjectClassNames();

            foreach ($classes as $class) {
                $classReflection = $this->reflectionProvider->getClass($class);

                if ($classReflection->getName() === $this->repositoryClassReflection->getName()
                    || $classReflection->isSubclassOfClass($this->repositoryClassReflection)
                ) {
                    return true;
                }
            }

            return false;
        }

        return $target->getName() === $this->repositoryClassReflection->getName()
            || $target->isSubclassOfClass($this->repositoryClassReflection);
    }

    private function isFormClass(ClassReflection|Type $target): bool
    {
        if ($target instanceof Type) {
            $classes = $target->getObjectClassNames();

            foreach ($classes as $class) {
                $classReflection = $this->reflectionProvider->getClass($class);

                if ($classReflection->getName() === $this->formClassReflection->getName()
                    || $classReflection->isSubclassOfClass($this->formClassReflection)
                ) {
                    return true;
                }
            }

            return false;
        }

        return $target->getName() === $this->formClassReflection->getName()
            || $target->isSubclassOfClass($this->formClassReflection);
    }

    private function isMethodOverridden(Type $type, string $methodName): bool
    {
        if ($type->isObject()->no()) {
            return false;
        }

        foreach ($type->getObjectClassNames() as $objectClassName) {
            $classReflection = $this->reflectionProvider->getClass($objectClassName);

            if (!$classReflection->hasNativeMethod($methodName)) {
                continue;
            }

            $declaringClass = $classReflection->getNativeMethod($methodName)->getDeclaringClass();

            if ($declaringClass->getName() === $this->repositoryClassReflection->getName()) {
                return false;
            }
        }

        return true;
    }
}
