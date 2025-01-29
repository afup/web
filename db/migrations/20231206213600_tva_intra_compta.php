<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class TVAIntraCompta extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
ALTER TABLE `compta`
  ADD `tva_intra` VARCHAR(20) AFTER `nom_frs`;
EOF;
        $this->execute($sql);
    }
}
