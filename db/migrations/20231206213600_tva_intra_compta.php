<?php

use Phinx\Migration\AbstractMigration;

class TVAIntraCompta extends AbstractMigration
{
    public function change()
    {
        $sql = <<<EOF
ALTER TABLE `compta`
  ADD `tva_intra` VARCHAR(20) AFTER `nom_frs`;
EOF;
        $this->execute($sql);
    }
}
