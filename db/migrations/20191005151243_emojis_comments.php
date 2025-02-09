<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class EmojisComments extends AbstractMigration
{
    public function change(): void
    {
        $this->execute("ALTER TABLE afup_inscription_forum CHANGE commentaires commentaires text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
    }
}
