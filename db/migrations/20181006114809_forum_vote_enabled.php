<?php


use Phinx\Migration\AbstractMigration;

class ForumVoteEnabled extends AbstractMigration
{
    public function change()
    {
        $sql = <<<SQL
ALTER TABLE `afup_forum`
ADD `vote_enabled` TINYINT DEFAULT 1
;
SQL;
        $this->execute($sql);
    }
}
