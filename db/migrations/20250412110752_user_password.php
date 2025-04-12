<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class UserPassword extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_personnes_physiques')
            ->changeColumn('mot_de_passe', 'text')
            ->save();
    }
}
