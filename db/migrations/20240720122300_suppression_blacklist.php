<?php

use Phinx\Migration\AbstractMigration;

class SuppressionBlacklist extends AbstractMigration
{
    public function change()
    {
        $this->execute('DROP TABLE IF EXISTS afup_blacklist');
    }
}
