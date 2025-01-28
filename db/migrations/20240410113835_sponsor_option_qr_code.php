<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class SponsorOptionQrCode extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
ALTER TABLE afup_forum_sponsors_tickets
    ADD qr_codes_scanner_available TINYINT(1) DEFAULT 0 NOT NULL;
EOF;
        $this->execute($sql);
    }
}
