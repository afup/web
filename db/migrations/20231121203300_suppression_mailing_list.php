<?php

use Phinx\Migration\AbstractMigration;

class SuppressionMailingList extends AbstractMigration
{
    public function change()
    {
        $this->execute('DROP TABLE IF EXISTS afup_mailing_lists');
    }
}
