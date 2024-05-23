<?php


use Phinx\Migration\AbstractMigration;

class AfupForumTransportInformation extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE `afup_forum` ADD `transport_information_enabled` TINYINT DEFAULT 0");
        $this->execute("ALTER TABLE `afup_inscription_forum` ADD `transport_mode` TINYINT, ADD `transport_distance` SMALLINT");
    }
}
