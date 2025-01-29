<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class TechletterSubscriptions extends AbstractSeed
{
    public function run(): void
    {
        $subscriptionDate = (new DateTime())->format('Y-m-d H:i:s');
        $data = [
            [
                'user_id' => Users::ID_USER_ADMIN,
                'subscription_date' => $subscriptionDate,
            ],
            [
                'user_id' => Users::ID_USER_EXPIRIE,
                'subscription_date' => $subscriptionDate,
            ],
        ];

        $table = $this->table('afup_techletter_subscriptions');

        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
