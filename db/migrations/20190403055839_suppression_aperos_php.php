<?php


use Phinx\Migration\AbstractMigration;

class SuppressionAperosPhp extends AbstractMigration
{
    public function change()
    {
        $this->execute('DROP TABLE IF EXISTS afup_aperos');
        $this->execute('DROP TABLE IF EXISTS afup_aperos_inscrits');
        $this->execute('DROP TABLE IF EXISTS afup_aperos_villes');
    }
}
