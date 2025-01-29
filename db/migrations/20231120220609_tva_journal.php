<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class TvaJournal extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
ALTER TABLE `compta`
  ADD `montant_ht_soumis_tva_0` double(11,2) DEFAULT NULL AFTER `idcompte`,
  ADD `montant_ht_soumis_tva_5_5` double(11,2) DEFAULT NULL AFTER `idcompte`,
  ADD `montant_ht_soumis_tva_10` double(11,2) DEFAULT NULL AFTER `idcompte`,
  ADD `montant_ht_soumis_tva_20` double(11,2) DEFAULT NULL AFTER `idcompte`
;
EOF;
        $this->execute($sql);
    }
}
