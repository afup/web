<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class HideInAccountingJournalReglement extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('ALTER TABLE compta_reglement ADD COLUMN hide_in_accounting_journal_at DATETIME DEFAULT NULL');
        $this->execute('UPDATE compta_reglement SET hide_in_accounting_journal_at = NOW() WHERE reglement LIKE "paypal"');
    }
}
