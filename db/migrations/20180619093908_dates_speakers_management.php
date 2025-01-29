<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class DatesSpeakersManagement extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<SQL
ALTER TABLE `afup_forum`
ADD `date_fin_saisie_repas_speakers` int(11) unsigned DEFAULT NULL,
ADD `date_fin_saisie_nuites_hotel` int(11) unsigned DEFAULT NULL
;
SQL;

        $this->execute($sql);
    }
}
