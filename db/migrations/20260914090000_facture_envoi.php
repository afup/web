<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class FactureEnvoi extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
ALTER TABLE `afup_compta_facture`
  ADD `date_envoi` datetime DEFAULT NULL AFTER `date_paiement`,
  ADD `envoye_par` varchar(50) DEFAULT NULL AFTER `date_envoi`;
EOF;
        $this->execute($sql);
    }
}
