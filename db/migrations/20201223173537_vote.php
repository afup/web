<?php

use Phinx\Migration\AbstractMigration;

class Vote extends AbstractMigration
{
    public function change()
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS `afup_assemblee_generale_question`;

CREATE TABLE `afup_assemblee_generale_question` (
  `id` int(11) AUTO_INCREMENT,
  `date` int(11) unsigned NOT NULL,
  `label` varchar(255) NOT NULL,
  `opened_at` DATETIME DEFAULT NULL,
  `closed_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL;

        $this->execute($sql);


        $sql = <<<SQL
DROP TABLE IF EXISTS `afup_vote_assemblee_generale`;

CREATE TABLE `afup_vote_assemblee_generale` (
  `afup_assemblee_generale_question_id` int(11) AUTO_INCREMENT,
  `afup_personnes_physiques_id` smallint(5) unsigned NOT NULL,
  `weight` INT(11) NOT NULL,
  `value` ENUM('oui', 'non', 'absention'),
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`afup_assemblee_generale_question_id`, `afup_personnes_physiques_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `afup_vote_assemblee_generale` ADD CONSTRAINT const_question FOREIGN KEY (`afup_assemblee_generale_question_id`) REFERENCES afup_assemblee_generale_question (`id`);
SQL;

        $this->execute($sql);
    }
}
