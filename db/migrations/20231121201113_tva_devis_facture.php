<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class TvaDevisFacture extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
ALTER TABLE `afup_compta_facture_details`
  ADD `tva` double(11,2) DEFAULT 0 AFTER `pu`;
EOF;
        $this->execute($sql);
    }
}
