<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class SponsorTickets extends AbstractSeed
{
    public const string TOKEN_GREEN = 'token-sponsor-tickets-vert-test';
    public const string TOKEN_ORANGE = 'token-sponsor-tickets-orange-test';
    public const string TOKEN_RED = 'token-sponsor-tickets-rouge-test';

    public function run(): void
    {
        $now = new DateTime('now');

        // Seed idempotent : suppression de la donnee de test si elle existe deja
        $this->execute(sprintf(
            "DELETE FROM afup_forum_sponsors_tickets WHERE token IN ('%s', '%s', '%s')",
            self::TOKEN_GREEN,
            self::TOKEN_ORANGE,
            self::TOKEN_RED,
        ));

        $data = [
            [
                'id' => 101,
                'company' => 'Sponsor Zéro Utilisé',
                'token' => self::TOKEN_GREEN,
                'contact_email' => 'vert@sponsor-test.com',
                'max_invitations' => 4,
                'used_invitations' => 0,
                'id_forum' => Event::ID_FORUM,
                'created_on' => $now->format('Y-m-d H:i:s'),
                'edited_on' => $now->format('Y-m-d H:i:s'),
                'creator_id' => Users::ID_USER_ADMIN,
                'qr_codes_scanner_available' => 0,
            ],
            [
                'id' => 102,
                'company' => 'Sponsor Deux Tiers Utilisé',
                'token' => self::TOKEN_ORANGE,
                'contact_email' => 'deuxtiers@sponsor-test.com',
                'max_invitations' => 3,
                'used_invitations' => 2,
                'id_forum' => Event::ID_FORUM,
                'created_on' => $now->format('Y-m-d H:i:s'),
                'edited_on' => $now->format('Y-m-d H:i:s'),
                'creator_id' => Users::ID_USER_ADMIN,
                'qr_codes_scanner_available' => 0,
            ],
            [
                'id' => 103,
                'company' => 'Sponsor Complet',
                'token' => self::TOKEN_RED,
                'contact_email' => 'complet@sponsor-test.com',
                'max_invitations' => 1,
                'used_invitations' => 1,
                'id_forum' => Event::ID_FORUM,
                'created_on' => $now->format('Y-m-d H:i:s'),
                'edited_on' => $now->format('Y-m-d H:i:s'),
                'creator_id' => Users::ID_USER_ADMIN,
                'qr_codes_scanner_available' => 0,
            ],
        ];

        $this->table('afup_forum_sponsors_tickets')->insert($data)->saveData();
    }
}
