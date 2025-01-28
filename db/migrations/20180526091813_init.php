<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class Init extends AbstractMigration
{
    public function change(): void
    {
        $this->execute(file_get_contents(__DIR__ . '/20180526091813_init.sql'));
    }
}
