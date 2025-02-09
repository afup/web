<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class AfupSessionsDiffusionAntennes extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('afup_sessions');
        $table->addColumn('has_allowed_to_sharing_with_local_offices', 'boolean', [
            'null' => false,
        ])->update();
    }
}
