<?php

use Phinx\Migration\AbstractMigration;

class SuppressionAccreditationPresse extends AbstractMigration
{
    public function change()
    {
        $this->execute('DROP TABLE IF EXISTS afup_accreditation_presse');
    }
}
