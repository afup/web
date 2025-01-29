<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class MembershipReason extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
ALTER TABLE `afup_personnes_morales`
  ADD `membership_reason` varchar(255) DEFAULT NULL
;
EOF;
        $this->execute($sql);
    }
}
