<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CleanupUserIdInPlaneteFlux extends AbstractMigration
{
    public function change(): void
    {
        $this->query(<<<SQL
            UPDATE afup_planete_flux
            SET id_personne_physique = NULL
            WHERE id_personne_physique = 0;
        SQL);
    }
}
