<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class SpeakerInfos extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<SQL
ALTER TABLE `afup_conferenciers`
ADD `will_attend_speakers_diner` tinyint(1) unsigned DEFAULT NULL,
ADD `has_special_diet` tinyint(1) unsigned DEFAULT NULL,
ADD `special_diet_description` TEXT  DEFAULT NULL,
ADD `hotel_nights` SET('before', 'between', 'after') DEFAULT NULL
;
SQL;

        $this->execute($sql);
    }
}
