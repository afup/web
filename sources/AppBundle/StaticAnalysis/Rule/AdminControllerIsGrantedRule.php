<?php

declare(strict_types=1);

namespace AppBundle\StaticAnalysis\Rule;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Scalar\String_;
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
 * ayant seulement ROLE_ADMIN, sans rôle fonctionnel.
 *
 * Pour la même raison, un #[IsGranted('ROLE_ADMIN')] seul ne protège rien :
 * il n'est toléré que sur les pages communes à toute la console
 * (tableau de bord et menu).
 *
 * @implements Rule<Class_>
 */
class AdminControllerIsGrantedRule implements Rule
{
    private const string ADMIN_CONTROLLER_NAMESPACE = 'AppBundle\Controller\Admin';

    /** Controllers communs à toute la console, relatifs au namespace contrôlé. */
    private const array ROLE_ADMIN_ALLOWED_CONTROLLERS = ['HomeAction', 'GetMenuAction'];

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

        $isGrantedAttributes = $this->findIsGrantedAttributes($node->attrGroups);
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->name->toString() === '__invoke') {
                $isGrantedAttributes = [...$isGrantedAttributes, ...$this->findIsGrantedAttributes($stmt->attrGroups)];
            }
        }

        if ($isGrantedAttributes === []) {
            return [
                RuleErrorBuilder::message('Controller d\'administration sans attribut #[IsGranted] : ajoutez un rôle de la matrice READ/WRITE (ou ROLE_SUPER_ADMIN) au niveau de la classe ou sur __invoke.')
                    ->identifier('afup.missingIsGrantedOnAdminController')
                    ->build(),
            ];
        }

        foreach ($isGrantedAttributes as $attribute) {
            if (!$this->isRoleAdmin($attribute)) {
                return [];
            }
        }

        $shortName = substr($classFqn, strlen($this->controllerNamespace) + 1);
        if (in_array($shortName, self::ROLE_ADMIN_ALLOWED_CONTROLLERS, true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message('Controller d\'administration protégé uniquement par #[IsGranted(\'ROLE_ADMIN\')] : ce rôle ne donne aucun droit fonctionnel, utilisez un rôle de la matrice READ/WRITE (ou ROLE_SUPER_ADMIN).')
                ->identifier('afup.adminRoleOnAdminController')
                ->build(),
        ];
    }

    /**
     * @param AttributeGroup[] $attributeGroups
     *
     * @return list<Attribute>
     */
    private function findIsGrantedAttributes(array $attributeGroups): array
    {
        $attributes = [];
        foreach ($attributeGroups as $attributeGroup) {
            foreach ($attributeGroup->attrs as $attribute) {
                if ((string) $attribute->name === 'IsGranted' || (string) $attribute->name === IsGranted::class) {
                    $attributes[] = $attribute;
                }
            }
        }

        return $attributes;
    }

    /** Le premier argument (positionnel ou nommé "attribute") est-il la chaîne 'ROLE_ADMIN' ? */
    private function isRoleAdmin(Attribute $attribute): bool
    {
        foreach ($attribute->args as $index => $arg) {
            if (($arg->name === null && $index === 0) || $arg->name?->toString() === 'attribute') {
                return $arg->value instanceof String_ && $arg->value->value === 'ROLE_ADMIN';
            }
        }

        return false;
    }
}
