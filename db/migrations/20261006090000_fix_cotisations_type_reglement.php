<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class FixCotisationsTypeReglement extends AbstractMigration
{
    public function up(): void
    {
        $this->execute('UPDATE afup_cotisations SET type_reglement = 3 WHERE type_reglement NOT IN (0, 1, 2, 3, 4)');
    }
}
