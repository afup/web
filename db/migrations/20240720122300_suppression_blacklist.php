<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class SuppressionBlacklist extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('DROP TABLE IF EXISTS afup_blacklist');
    }
}
