<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class LienInterview extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
ALTER TABLE `afup_sessions`
  ADD `interview_url` varchar(255) DEFAULT NULL AFTER `date_publication`
;
EOF;
        $this->execute($sql);
    }
}
