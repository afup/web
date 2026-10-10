<?php

declare(strict_types=1);

namespace AppBundle\StaticAnalysis\Rule;

use PhpParser\Node;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Cette règle PHPStan garantit que tout controller du back-office admin
 * porte un attribut #[IsGranted] (matrice READ/WRITE par module, ou
 * ROLE_SUPER_ADMIN sur les pages sensibles), au niveau de la classe ou
 * sur la méthode __invoke.
 *
 * La matrice READ/WRITE est le vrai garde d'accès du back-office ;
 * sans ce garde, une route /admin n'est protégée que par la règle
 * console access_control (ROLE_ADMIN), ce qui laisse passer des comptes
 * LEVEL_ADMIN sans rôle fonctionnel.
 *
 * @implements Rule<Class_>
 */
class AdminControllerIsGrantedRule implements Rule
{
    private const string ADMIN_CONTROLLER_NAMESPACE = 'AppBundle\Controller\Admin';

    /** Surpropriété utilisée par les tests pour analyser des fixtures dans le namespace des stubs. */
    private string $controllerNamespace;

    public function __construct(?string $controllerNamespace = null)
    {
        $this->controllerNamespace = $controllerNamespace ?? self::ADMIN_CONTROLLER_NAMESPACE;
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $classFqn = $node->namespacedName?->toString() ?? '';
        if ($classFqn === '' || !str_starts_with($classFqn, $this->controllerNamespace . '\\')) {
            return [];
        }

        // Enums, traits et interfaces sont d'autres types de nœuds : seules les
        // classes non abstraites sont des entry points HTTP.
        if ($node->isAbstract()) {
            return [];
        }

        // Listeners noyau qui vivent dans ce namespace : pas des controllers.
        if (str_ends_with($classFqn, 'Listener')) {
            return [];
        }

        if ($this->hasIsGrantedAttribute($node->attrGroups)) {
            return [];
        }

        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof ClassMethod
                && $stmt->name->toString() === '__invoke'
                && $this->hasIsGrantedAttribute($stmt->attrGroups)
            ) {
                return [];
            }
        }

        return [
            RuleErrorBuilder::message('Controller d\'administration sans attribut #[IsGranted] : ajoutez un rôle de la matrice READ/WRITE (ou ROLE_SUPER_ADMIN) au niveau de la classe ou sur __invoke.')
                ->identifier('afup.missingIsGrantedOnAdminController')
                ->build(),
        ];
    }

    /** @param AttributeGroup[] $attributeGroups */
    private function hasIsGrantedAttribute(array $attributeGroups): bool
    {
        foreach ($attributeGroups as $attributeGroup) {
            foreach ($attributeGroup->attrs as $attribute) {
                if ((string) $attribute->name === 'IsGranted' || (string) $attribute->name === IsGranted::class) {
                    return true;
                }
            }
        }

        return false;
    }
}
