<?php

use Phinx\Migration\AbstractMigration;

class NeedsUpToDateMembership extends AbstractMigration
{
    public function change()
    {
        $sql = <<<EOF
ALTER TABLE `afup_personnes_physiques`
  ADD `needs_up_to_date_membership` tinyint DEFAULT 0
;
EOF;
        $this->execute($sql);
    }
}
