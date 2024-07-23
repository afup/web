<?php

use Phinx\Migration\AbstractMigration;

class SuppressionTrelloListId extends AbstractMigration
{
    public function change()
    {
        $this->execute('ALTER TABLE afup_forum DROP COLUMN trello_list_id');
    }
}
