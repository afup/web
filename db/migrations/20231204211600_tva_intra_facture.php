<?php

use Phinx\Migration\AbstractMigration;

class TvaIntraFacture extends AbstractMigration
{
    public function change()
    {
        $sql = <<<EOF
ALTER TABLE `afup_compta_facture`
  ADD `tva_intra` VARCHAR(20) AFTER `email`;
EOF;
        $this->execute($sql);
    }
}
