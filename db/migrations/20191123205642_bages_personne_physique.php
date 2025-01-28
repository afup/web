<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class BagesPersonnePhysique extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
CREATE TABLE `afup_personnes_physiques_badge` (
  `afup_personne_physique_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `issued_at` date NOT NULL,
  PRIMARY KEY (`afup_personne_physique_id`, `badge_id`)
)
EOF;
        $this->execute($sql);
    }
}
