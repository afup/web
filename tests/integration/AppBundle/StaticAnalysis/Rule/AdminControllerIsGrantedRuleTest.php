<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\StaticAnalysis\Rule;

use AppBundle\StaticAnalysis\Rule\AdminControllerIsGrantedRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/** @extends RuleTestCase<AdminControllerIsGrantedRule> */
final class AdminControllerIsGrantedRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        // Les fixtures vivent dans le namespace autoloadé des stubs.
        return new AdminControllerIsGrantedRule('Afup\Tests\Stubs\PHPStan');
    }

    public function testRule(): void
    {
        $u = __DIR__ . '/../../../../stubs/phpstan';

        $this->analyse([
            $u . '/ControllerWithoutIsGranted.php',
            $u . '/InvokeWithoutIsGranted.php',
        ], [
            [
                'Controller d\'administration sans attribut #[IsGranted] : ajoutez un rôle de la matrice READ/WRITE (ou ROLE_SUPER_ADMIN) au niveau de la classe ou sur __invoke.',
                7,
            ],
            [
                'Controller d\'administration sans attribut #[IsGranted] : ajoutez un rôle de la matrice READ/WRITE (ou ROLE_SUPER_ADMIN) au niveau de la classe ou sur __invoke.',
                7,
            ],
        ]);
    }

    public function testRoleAdminAloneIsReported(): void
    {
        $u = __DIR__ . '/../../../../stubs/phpstan';

        $this->analyse([
            $u . '/ControllerWithRoleAdmin.php',
            $u . '/HomeAction.php',
            $u . '/ControllerClassLevelIsGranted.php',
            $u . '/ControllerInvokeLevelIsGranted.php',
            $u . '/KernelAdminListener.php',
        ], [
            [
                'Controller d\'administration protégé uniquement par #[IsGranted(\'ROLE_ADMIN\')] : ce rôle ne donne aucun droit fonctionnel, utilisez un rôle de la matrice READ/WRITE (ou ROLE_SUPER_ADMIN).',
                9,
            ],
        ]);
    }
}
