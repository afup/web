<?php

use Phinx\Migration\AbstractMigration;

class AfupMeetup extends AbstractMigration
{
    public function up()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS `afup_meetup` (
            `id_meetup` int(11) NOT NULL AUTO_INCREMENT,
            `date` datetime NOT NULL,
            `title` varchar(255) NOT NULL,
            `location` varchar(255) NOT NULL,
            `antenne_id` int(11) NOT NULL,
            PRIMARY KEY (`id_meetup`),
            KEY `antenne_id` (`antenne_id`),
            CONSTRAINT `afup_meetup_ibfk_1` FOREIGN KEY (`antenne_id`) REFERENCES `afup_antenne` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;');
    }

    public function down()
    {
        $this->execute('DROP TABLE IF EXISTS `afup_meetup`');
    }
}
