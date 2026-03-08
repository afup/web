<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DeleteAfupNiveauPartenariatTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_niveau_partenariat')->drop()->save();
    }
}
