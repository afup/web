<?php


use Phinx\Migration\AbstractMigration;

class SuppressionAperosPhp extends AbstractMigration
{
    public function change()
    {
        $this->dropTable('afup_aperos');
        $this->dropTable('afup_aperos_inscrits');
        $this->dropTable('afup_aperos_villes');
    }
}
