<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdatePlaneteFluxNullables extends AbstractMigration
{
    public function change(): void
    {
        ($table = $this->table('afup_planete_flux'))
            ->changeColumn('nom', $table->getColumn('nom')->getType(), [
                'null' => false,
            ])
            ->changeColumn('url', $table->getColumn('url')->getType(), [
                'null' => false,
            ])
            ->changeColumn('feed', $table->getColumn('feed')->getType(), [
                'null' => false,
            ])
            ->changeColumn('etat', $table->getColumn('etat')->getType(), [
                'null' => false,
            ])
            ->update();
    }
}
