<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class AddSocialNetworkToTweet extends AbstractMigration
{
    public function change(): void
    {
        $this->table('tweet')
            ->addColumn('social_network', 'string', [
                'limit' => 20,
                'null' => false,
                'default' => 'twitter',
            ])
            ->update();
    }
}
