<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class SessionWorkshop extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
ALTER TABLE `afup_sessions`
  ADD `workshop_abstract` text AFTER `skill`,
  ADD `with_workshop` tinyint(1) NOT NULL DEFAULT '0' AFTER `skill`;
EOF;
        $this->execute($sql);
    }
}
