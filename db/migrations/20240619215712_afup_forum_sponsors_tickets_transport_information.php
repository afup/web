<?php


use Phinx\Migration\AbstractMigration;

class AfupForumSponsorsTicketsTransportInformation extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE `afup_forum_sponsors_tickets` ADD `transport_mode` TINYINT, ADD `transport_distance` SMALLINT");
    }
}
