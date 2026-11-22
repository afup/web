<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class FactureDetailsTvaNotNull extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('UPDATE `afup_compta_facture_details` SET `tva` = 0 WHERE `tva` IS NULL');
        $this->execute('ALTER TABLE `afup_compta_facture_details` MODIFY `tva` double(11,2) NOT NULL DEFAULT 0');
    }
}
