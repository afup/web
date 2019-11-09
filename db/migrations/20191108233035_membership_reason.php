<?php

use Phinx\Migration\AbstractMigration;

class MembershipReason extends AbstractMigration
{
    public function change()
    {
        $sql = <<<EOF
ALTER TABLE `afup_personnes_morales`
  ADD `membership_reason` varchar(255) DEFAULT NULL
;
EOF;
        $this->execute($sql);
    }
}
