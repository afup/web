<?php

use Phinx\Migration\AbstractMigration;

class AfupMeetup extends AbstractMigration
{
    public function up()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS `afup_meetup` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `date` datetime NOT NULL,
            `title` varchar(255) NOT NULL,
            `location` varchar(255) NOT NULL,
            `description` TEXT,
            `antenne_name` varchar(255) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;');
    }
}
