<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class SlackAlternateEmail extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('ALTER TABLE afup_personnes_physiques ADD slack_alternate_email VARCHAR(255) DEFAULT NULL');
    }
}
