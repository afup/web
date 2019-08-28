<?php

use Phinx\Migration\AbstractMigration;

class LienInterview extends AbstractMigration
{
    public function change()
    {
        $sql = <<<EOF
ALTER TABLE `afup_sessions`
  ADD `interview_url` varchar(255) DEFAULT NULL AFTER `date_publication`
;
EOF;
        $this->execute($sql);
    }
}
