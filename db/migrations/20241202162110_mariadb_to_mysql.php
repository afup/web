<?php

use Phinx\Migration\AbstractMigration;

class MariaDBToMysql extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE afup_sessions ALTER COLUMN skill SET DEFAULT 0");
        $this->execute("ALTER TABLE afup_sessions ALTER COLUMN has_allowed_to_sharing_with_local_offices SET DEFAULT 0");
        $this->execute("ALTER TABLE compta MODIFY idevenement TINYINT(5) NULL");
        $this->execute("ALTER TABLE compta_categorie MODIFY idevenement TINYINT(5) NULL");
        $this->execute("ALTER TABLE afup_compta_facture MODIFY date_facture DATE NULL");
        $this->execute("ALTER TABLE afup_compta_facture MODIFY numero_facture VARCHAR(50) NULL");
        $this->execute("ALTER TABLE compta MODIFY idclef VARCHAR(20) NULL");
        $this->execute("ALTER TABLE afup_inscription_forum MODIFY transport_mode SMALLINT NULL");
    }
}

