<?php


use Phinx\Migration\AbstractMigration;

class DateFinVenteTokenSponsor extends AbstractMigration
{
    public function change()
    {
        $this->query("ALTER TABLE afup_forum ADD date_fin_vente_token_sponsor int(11) DEFAULT NULL AFTER date_fin_vente");
    }
}
