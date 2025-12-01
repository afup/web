<?php

declare(strict_types=1);

namespace AppBundle\StaticAnalysis\Rule;

use AppBundle\Security\Authentication;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Règle PHPStan pour forcer l'utilisation du service Authentication pour
 * récupérer l'utilisateur courant.
 *
 * @see Authentication
 *
 * @implements Rule<MethodCall>
 */
final readonly class NoGetUserMethodRule implements Rule
{
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

        if ($node->name->toLowerString() !== 'getuser') {
            return [];
        }

        $callerType = $scope->getType($node->var);

        $abstractControllerClassReflection = $this->reflectionProvider->getClass(AbstractController::class);
        foreach ($callerType->getObjectClassNames() as $className) {
            if (!$this->reflectionProvider->hasClass($className)) {
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($className);

            if ($classReflection->isSubclassOfClass($abstractControllerClassReflection)) {
                return [
                    RuleErrorBuilder::message("Don't use getUser() in a Controller, inject the Authentication service instead.")
                        ->identifier('afup.controllerGetUser')
                        ->build(),
                ];
            }
        }

        return [];
    }
}
