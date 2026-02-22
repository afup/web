<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSuperAperoTables extends AbstractMigration
{
    public function change(): void
    {
        $this->table('super_apero')
            ->addColumn('date', 'date', ['null' => false])
            ->create();

        $this->table('super_apero_meetup')
            ->addColumn('super_apero_id', 'integer', [
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('antenne', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('meetup_id', 'integer', ['null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addForeignKey('super_apero_id', 'super_apero', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
