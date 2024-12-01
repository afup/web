<?php


use Phinx\Migration\AbstractMigration;

class ComptaIdClef extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE compta MODIFY COLUMN idclef varchar(20) NOT NULL DEFAULT ''");
    }
}
