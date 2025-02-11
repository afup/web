<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class PlaneteBilletEncoding extends AbstractMigration
{
    public function change(): void
    {
        $this->execute("ALTER TABLE afup_planete_billet CHANGE titre titre mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_planete_billet CHANGE auteur auteur mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_planete_billet CHANGE resume resume mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_planete_billet CHANGE contenu contenu mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
    }
}
