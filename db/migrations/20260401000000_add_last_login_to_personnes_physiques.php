<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddLastLoginToPersonnesPhysiques extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_personnes_physiques')
            ->addColumn('last_login', 'datetime', ['null' => true])
            ->save();
    }
}
