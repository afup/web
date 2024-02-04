<?php


use Phinx\Migration\AbstractMigration;

class AfupConferenciersDiffusionAntennes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('afup_sessions');
        $table->addColumn('has_allowed_to_sharing_with_local_offices', 'boolean', [
            'null' => false,
        ])->update();
    }
}
