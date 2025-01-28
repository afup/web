<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class TechletterUrl extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('ALTER TABLE afup_techletter ADD archive_url VARCHAR(255) DEFAULT NULL');
    }
}
