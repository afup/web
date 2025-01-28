<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class PricesWithVat extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
ALTER TABLE afup_forum
    ADD has_prices_defined_with_vat TINYINT(1) DEFAULT 0 NOT NULL
;
EOF;
        $this->execute($sql);
        $sql = <<<EOF
UPDATE afup_forum SET has_prices_defined_with_vat = 1;
EOF;
        $this->execute($sql);
    }
}
