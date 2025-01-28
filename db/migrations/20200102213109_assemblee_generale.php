<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AssembleeGenerale extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
CREATE TABLE `afup_assemblee_generale` (
  `date` int(11) unsigned NOT NULL,
  description text
)
;
EOF;
        $this->execute($sql);
    }
}
