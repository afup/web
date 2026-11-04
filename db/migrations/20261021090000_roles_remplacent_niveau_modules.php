<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Les droits d'accès au back-office sont désormais portés uniquement par la colonne roles.
 *
 * Cette migration ne fait que le changement de schéma :
 * - roles passe en text (avec les rôles de modules, la liste peut dépasser 255 caractères) ;
 * - les colonnes niveau / niveau_modules sont supprimées.
 *
 * Le wipe des droits (rôles admin / legacy / de modules retirés, seuls ROLE_MEMBER_*
 * et ROLE_COMPANY_MANAGER conservés) est fait via la commande console
 * bin/console security:reset-backoffice-roles, à lancer manuellement.
 */
class RolesRemplacentNiveauModules extends AbstractMigration
{
    public function up(): void
    {
        // Avec les rôles des modules, la liste peut dépasser 255 caractères
        $this->table('afup_personnes_physiques')
            ->changeColumn('roles', 'text', ['null' => false])
            ->save();

        $this->table('afup_personnes_physiques')
            ->removeColumn('niveau')
            ->removeColumn('niveau_modules')
            ->save();
    }

    public function down(): void
    {
        // Les anciens droits ne sont pas récupérables : le retour arrière ne remet que le schéma
        if (!$this->table('afup_personnes_physiques')->hasColumn('niveau')) {
            $this->table('afup_personnes_physiques')
                ->addColumn('niveau', 'integer', ['limit' => 255, 'signed' => false, 'default' => 0, 'after' => 'mot_de_passe'])
                ->addColumn('niveau_modules', 'char', ['limit' => 10, 'default' => '', 'after' => 'niveau'])
                ->save();
        }
    }
}
