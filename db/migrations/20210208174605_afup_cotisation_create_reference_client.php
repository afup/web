<?php


use Phinx\Migration\AbstractMigration;

class AfupCotisationCreateReferenceClient extends AbstractMigration
{
    public function change()
    {
        $sql = <<<SQL
ALTER TABLE afup_cotisations
ADD reference_client VARCHAR(255) DEFAULT NULL
;
SQL;
        $this->execute($sql);
    }
}

