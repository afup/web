<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class ForumVoteEnabled extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<SQL
ALTER TABLE `afup_forum`
ADD `vote_enabled` TINYINT DEFAULT 1
;
SQL;
        $this->execute($sql);
    }
}
