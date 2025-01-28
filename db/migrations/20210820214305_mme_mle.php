<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class MmeMle extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<SQL
UPDATE afup_conferenciers set civilite = 'Mme' where civilite = 'Mlle';
UPDATE afup_inscription_forum set civilite = 'Mme' where civilite = 'Mlle';
UPDATE afup_personnes_morales set civilite = 1 where civilite = 2;
SQL;
        $this->execute($sql);
    }
}
