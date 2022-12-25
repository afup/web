<?php

use Phinx\Seed\AbstractSeed;

class TechletterSubscriptions extends AbstractSeed
{
    public function run()
    {
        $subscriptionDate = (new DateTime())->format(DateTime::ATOM);
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
