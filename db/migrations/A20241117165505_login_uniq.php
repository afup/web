<?php

use Phinx\Migration\AbstractMigration;

class LoginUniq extends AbstractMigration
{
    public function change()
    {
        $this->execute("CREATE UNIQUE INDEX idx_login_unique ON afup_personnes_physiques (login)");
    }
}