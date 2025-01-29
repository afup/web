<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class SuppressionTrelloListId extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('ALTER TABLE afup_forum DROP COLUMN trello_list_id');
    }
}
