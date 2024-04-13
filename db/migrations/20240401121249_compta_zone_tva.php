<?php

use Phinx\Migration\AbstractMigration;

class ComptaZoneTva extends AbstractMigration
{
    public function change()
    {
        $sql = <<<EOF
ALTER TABLE `compta`
  ADD `tva_zone` VARCHAR(25) DEFAULT NULL AFTER `tva_intra`;
EOF;
        $this->execute($sql);
    }
}
