<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Les droits d'accès au back-office sont désormais portés uniquement par la colonne roles :
 * - niveau = 2 (accès à l'admin) devient ROLE_ADMIN ;
 * - chaque chiffre de niveau_modules devient ROLE_<MODULE>_READER (1) ou ROLE_<MODULE>_WRITER (2) ;
 * - le rôle legacy ROLE_VEILLE devient ROLE_VEILLE_READER.
 *
 * Les rôles de la matrice, ROLE_ADMIN et les autres rôles legacy déjà présents en base étaient ignorés
 * (recalculés depuis niveau / niveau_modules) : ils sont remplacés par le résultat du calcul.
 */
class RolesRemplacentNiveauModules extends AbstractMigration
{
    /** Modules dans l'ordre des index de la chaîne niveau_modules. */
    private const array MODULES = ['APERO', 'MEMBRES', 'SITE', 'EVENT', 'ANTENNES', 'VEILLE', 'COMPTA', 'AG', 'PLANETE'];

    private const array LEGACY_ROLES = ['ROLE_APERO', 'ROLE_ANNUAIRE', 'ROLE_SITE', 'ROLE_FORUM', 'ROLE_ANTENNE', 'ROLE_VEILLE'];

    public function up(): void
    {
        // Avec les rôles des modules, la liste peut dépasser 255 caractères
        $this->table('afup_personnes_physiques')
            ->changeColumn('roles', 'text', ['null' => false])
            ->save();

        $computedRoles = ['ROLE_ADMIN', ...self::LEGACY_ROLES];
        foreach (self::MODULES as $module) {
            $computedRoles[] = 'ROLE_' . $module . '_READER';
            $computedRoles[] = 'ROLE_' . $module . '_WRITER';
        }

        foreach ($this->fetchAll('SELECT id, niveau, niveau_modules, roles FROM afup_personnes_physiques') as $row) {
            $storedRoles = $this->decodeRoles($row['roles']);
            $levels = str_split(str_pad((string) $row['niveau_modules'], count(self::MODULES), '0'));

            $roles = array_values(array_diff($storedRoles, $computedRoles));
            if ((int) $row['niveau'] === 2) {
                $roles[] = 'ROLE_ADMIN';
            }
            foreach (self::MODULES as $index => $module) {
                $level = (int) $levels[$index];
                if ($module === 'VEILLE' && in_array('ROLE_VEILLE', $storedRoles, true)) {
                    $level = max($level, 1);
                }
                if ($level >= 2) {
                    $roles[] = 'ROLE_' . $module . '_WRITER';
                } elseif ($level === 1) {
                    $roles[] = 'ROLE_' . $module . '_READER';
                }
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
        $this->table('afup_personnes_physiques')
            ->addColumn('niveau', 'integer', ['limit' => 255, 'signed' => false, 'default' => 0, 'after' => 'mot_de_passe'])
            ->addColumn('niveau_modules', 'char', ['limit' => 10, 'default' => '', 'after' => 'niveau'])
            ->save();

        foreach ($this->fetchAll('SELECT id, roles FROM afup_personnes_physiques') as $row) {
            $roles = $this->decodeRoles($row['roles']);
            $levels = '';
            foreach (self::MODULES as $module) {
                $levels .= match (true) {
                    in_array('ROLE_' . $module . '_WRITER', $roles, true) => '2',
                    in_array('ROLE_' . $module . '_READER', $roles, true) => '1',
                    default => '0',
                };
            }

            $this->execute(
                'UPDATE afup_personnes_physiques SET niveau = ?, niveau_modules = ? WHERE id = ?',
                [in_array('ROLE_ADMIN', $roles, true) ? 2 : 0, $levels, $row['id']],
            );
        }
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
