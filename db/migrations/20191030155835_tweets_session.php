<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class TweetsSession extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<SQL
ALTER TABLE `afup_sessions`
ADD `tweets` text DEFAULT NULL
;
SQL;
        $this->execute($sql);
    }
}
