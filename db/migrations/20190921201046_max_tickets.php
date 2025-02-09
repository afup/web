<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class MaxTickets extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
ALTER TABLE `afup_forum_tarif_event`
  ADD `max_tickets` int DEFAULT NULL
;
EOF;
        $this->execute($sql);
    }
}
