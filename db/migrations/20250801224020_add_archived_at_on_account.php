<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddArchivedAtOnAccount extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('ALTER TABLE compta_compte ADD COLUMN archived_at DATETIME DEFAULT NULL');
    }
}
