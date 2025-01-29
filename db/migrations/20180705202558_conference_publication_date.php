<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class ConferencePublicationDate extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<SQL
ALTER TABLE `afup_sessions`
ADD `date_publication` datetime DEFAULT NULL
;
SQL;
        $this->execute($sql);
    }
}
