<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class ComptaIdClef extends AbstractMigration
{
    public function change(): void
    {
        $this->execute("ALTER TABLE compta MODIFY COLUMN idclef varchar(20) NOT NULL DEFAULT ''");
    }
}
