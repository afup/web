<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class UserBadgeConstraint extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
ALTER TABLE afup_personnes_physiques_badge 
ADD CONSTRAINT badge_fk
FOREIGN KEY (badge_id)
REFERENCES afup_badge(id)
EOF;
        $this->execute($sql);
    }
}
