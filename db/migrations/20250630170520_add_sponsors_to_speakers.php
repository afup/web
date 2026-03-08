<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSponsorsToSpeakers extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_conferenciers')
            ->addColumn('has_hosting_sponsor', 'boolean', [
                'default' => false,
                'null' => false,
            ])
            ->addColumn('travel_refund_needed', 'boolean', [
                'default' => true,
                'null' => false,
            ])
            ->addColumn('travel_refund_sponsored', 'boolean', [
                'default' => false,
                'null' => false,
            ])
            ->update();
    }
}
