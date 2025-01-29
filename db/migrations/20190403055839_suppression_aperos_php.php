<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class SuppressionAperosPhp extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('DROP TABLE IF EXISTS afup_aperos');
        $this->execute('DROP TABLE IF EXISTS afup_aperos_inscrits');
        $this->execute('DROP TABLE IF EXISTS afup_aperos_villes');
    }
}
