<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class VoteFixEnum extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<SQL
ALTER TABLE afup_vote_assemblee_generale
MODIFY `value` ENUM('oui', 'non', 'abstention')
;
SQL;
        $this->execute($sql);
    }
}
