<?php

use Phinx\Migration\AbstractMigration;

class SlackAlternateEmail extends AbstractMigration
{
    public function change()
    {
        $this->execute('ALTER TABLE afup_personnes_physiques ADD slack_alternate_email VARCHAR(255) DEFAULT NULL');
    }
}
