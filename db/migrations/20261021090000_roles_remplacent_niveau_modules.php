<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Les droits d'accès au back-office sont désormais portés uniquement par la colonne roles.
 *
 * Plutôt que de convertir niveau / niveau_modules, on repart de zéro :
 * - seuls les rôles non liés au back-office (ROLE_MEMBER_*, ROLE_COMPANY_MANAGER) sont conservés ;
 * - ROLE_ADMIN, ROLE_SUPER_ADMIN, les rôles legacy (ROLE_VEILLE, …) et les rôles des modules sont retirés ;
 * - seule la personne physique SUPER_ADMIN_ID reçoit ROLE_SUPER_ADMIN, les accès sont ensuite réattribués depuis l'admin.
 */
class RolesRemplacentNiveauModules extends AbstractMigration
{
    private const int SUPER_ADMIN_ID = 3225;

    private const array KEPT_ROLES = ['ROLE_COMPANY_MANAGER'];

    private const string KEPT_ROLES_PREFIX = 'ROLE_MEMBER_';

    public function up(): void
    {
        // Sans le super admin, la migration retirerait tous les accès à l'admin : on ne fait rien
        if (!$this->superAdminExists()) {
            return;
        }

        // Avec les rôles des modules, la liste peut dépasser 255 caractères
        $this->table('afup_personnes_physiques')
            ->changeColumn('roles', 'text', ['null' => false])
            ->save();

        foreach ($this->fetchAll('SELECT id, roles FROM afup_personnes_physiques') as $row) {
            $roles = array_values(array_filter(
                $this->decodeRoles($row['roles']),
                fn(string $role): bool => in_array($role, self::KEPT_ROLES, true) || str_starts_with($role, self::KEPT_ROLES_PREFIX),
            ));
            if ((int) $row['id'] === self::SUPER_ADMIN_ID) {
                $roles[] = 'ROLE_SUPER_ADMIN';
            }

            $this->execute(
                'UPDATE afup_personnes_physiques SET roles = ? WHERE id = ?',
                [json_encode(array_values(array_unique($roles))), $row['id']],
            );
        }

        $this->table('afup_personnes_physiques')
            ->removeColumn('niveau')
            ->removeColumn('niveau_modules')
            ->save();
    }

    public function down(): void
    {
        // up() n'a rien fait si le super admin n'existait pas
        if ($this->table('afup_personnes_physiques')->hasColumn('niveau')) {
            return;
        }

        // Les anciens droits ne sont pas récupérables : seul le super admin retrouve l'accès à l'admin
        $this->table('afup_personnes_physiques')
            ->addColumn('niveau', 'integer', ['limit' => 255, 'signed' => false, 'default' => 0, 'after' => 'mot_de_passe'])
            ->addColumn('niveau_modules', 'char', ['limit' => 10, 'default' => '', 'after' => 'niveau'])
            ->save();

        $this->execute(
            'UPDATE afup_personnes_physiques SET niveau = 2 WHERE id = ?',
            [self::SUPER_ADMIN_ID],
        );
    }

    private function superAdminExists(): bool
    {
        return $this->fetchRow('SELECT id FROM afup_personnes_physiques WHERE id = ' . self::SUPER_ADMIN_ID) !== false;
    }

    /**
     * @return list<string>
     */
    private function decodeRoles(?string $roles): array
    {
        $decoded = json_decode((string) $roles, true);

        return is_array($decoded) ? array_values(array_filter($decoded, 'is_string')) : [];
    }
}
