<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddLinkedinToSpeakers extends AbstractMigration
{
    public function change(): void
    {
        $this->query("ALTER TABLE afup_conferenciers ADD linkedin varchar(255) DEFAULT NULL AFTER mastodon");
    }
}
