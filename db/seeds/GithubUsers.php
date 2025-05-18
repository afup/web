<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class GithubUsers extends AbstractSeed
{
    public const ID_GITHUBUSER_UBERMUDA = 1;

    public function run(): void
    {
        $data = [
            [
                'id' => self::ID_GITHUBUSER_UBERMUDA,
                'github_id' => 10758,
                'login' => 'ubermuda',
                'name' => 'Geoffrey Bachelet',
                'company' => 'Paper Edu.',
                'profile_url' => 'https://github.com/ubermuda',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/10758?v=4',
                'afup_crew' => 1,
            ],
            [
                'id' => 2,
                'github_id' => 320372,
                'login' => 'agallou',
                'name' => 'Adrien Gallou',
                'company' => 'Unow',
                'profile_url' => 'https://github.com/agallou',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/320372?v=4',
                'afup_crew' => 1,
            ],
        ];

        $table = $this->table('afup_user_github');
        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}
