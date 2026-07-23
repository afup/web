<?php

declare(strict_types=1);

namespace AppBundle\StaticAnalysis\Rule;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Class_>
 */
final class DoctrineAutoIncrementIdNonNullable implements Rule
{
    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$this->hasAttribute($node, Entity::class)) {
            return [];
        }

        $errors = [];
        foreach ($node->getProperties() as $property) {
            if (!$this->hasAttribute($property, Id::class)) {
                continue;
            }

            if (!$this->hasAttribute($property, GeneratedValue::class)) {
                continue;
            }

            if (!$this->isNullableInt($property->type)) {
                continue;
            }

            $errors[] = RuleErrorBuilder::message(sprintf('The "%s" property is an auto-incremented id so it must be non-nullable.', $property->props[0]->name->toString()))
                ->identifier('afup.doctrine.idNonNullable')
                ->line($property->getStartLine())
                ->build();
        }

        return $errors;
    }

    /**
     * @param class-string $attributeClass
     */
    private function hasAttribute(ClassLike|Property $source, string $attributeClass): bool
    {
        foreach ($source->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                if (ltrim($attribute->name->toString(), '\\') === $attributeClass) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isNullableInt(?Node $type): bool
    {
        return $type instanceof NullableType
            && $type->type instanceof Identifier
            && $type->type->toLowerString() === 'int';
    }
}
