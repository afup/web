<?php

use Phinx\Migration\AbstractMigration;

class VoteFixEnum extends AbstractMigration
{
    public function change()
    {
$sql = <<<SQL
ALTER TABLE afup_vote_assemblee_generale
MODIFY `value` ENUM('oui', 'non', 'abstention')
;
SQL;
        $this->execute($sql);
    }
}
