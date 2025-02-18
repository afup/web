<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class Init extends AbstractMigration
{
    public function change(): void
    {
        // This condition is needed to be able to run the migration on environments
        // where the database already exists (e.g. preprod and prod).
        if ($this->hasTable('afup_forum')) {
            return;
        }

        $this->execute(file_get_contents(__DIR__ . '/20250218154608_init.sql'));
    }
}
