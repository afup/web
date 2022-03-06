<?php

use AppBundle\Association\Model\Repository\UserRepository;
use Phinx\Seed\AbstractSeed;

class SubscriptionReminder extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'user_id' => Users::ID_USER_PERSONNE_MORALE,
                'user_type' => UserRepository::USER_TYPE_COMPANY,
                'email' => 'raoul.dupont@mycorp.fr',
                'reminder_date' => '2022-01-01',
                'reminder_key' => '15DaysAfter',
                'mail_sent' => 0,
            ],
            [
                'user_id' => Users::ID_USER_PERSONNE_MORALE,
                'user_type' => UserRepository::USER_TYPE_COMPANY,
                'email' => 'raoul.dupont@mycorp.fr',
                'reminder_date' => '2022-02-01',
                'reminder_key' => '15DaysAfter',
                'mail_sent' => 1,
            ],
            [
                'user_id' => Users::ID_USER_PERSONNE_PHYSIQUE,
                'user_type' => UserRepository::USER_TYPE_PHYSICAL,
                'email' => 'paul.personne@mycorp.fr',
                'reminder_date' => '2022-03-01',
                'reminder_key' => '15DaysAfter',
                'mail_sent' => 0,
            ],
            [
                'user_id' => Users::ID_USER_PERSONNE_PHYSIQUE,
                'user_type' => UserRepository::USER_TYPE_PHYSICAL,
                'email' => 'paul.personne@mycorp.fr',
                'reminder_date' => '2022-04-01',
                'reminder_key' => '15DaysAfter',
                'mail_sent' => 1,
            ],
        ];

        $table = $this->table('afup_subscription_reminder_log');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
