<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddDateDebutCFP extends AbstractMigration
{
    public function change(): void
    {
        $this->query('ALTER TABLE afup_forum ADD date_debut_appel_conferencier int(11) DEFAULT NULL AFTER date_fin_appel_projet');
    }
}
