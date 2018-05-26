<?php

use Phinx\Migration\AbstractMigration;

class Init extends AbstractMigration
{
    public function change()
    {
        $this->execute(file_get_contents(__DIR__ . '/20180526091813_init.sql'));
    }
}
