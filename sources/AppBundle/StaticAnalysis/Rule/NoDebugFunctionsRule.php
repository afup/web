<?php

declare(strict_types=1);

namespace AppBundle\StaticAnalysis\Rule;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Cette règle PHPStan bloque les oublis de fonctions de debug.
 *
 * @implements Rule<FuncCall>
 */
final class NoDebugFunctionsRule implements Rule
{
    private const FORBIDDEN_FUNCTIONS = ['var_dump', 'dump', 'dd', 'print_r'];

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Name) {
            return [];
        }

        $functionName = strtolower((string) $node->name);

        if (!in_array($functionName, self::FORBIDDEN_FUNCTIONS, true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf('Usage of %s() is forbidden.', $functionName))
                ->identifier('afup.forbiddenFunctionCall')
                ->build(),
        ];
    }
}
