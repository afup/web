<?php

declare(strict_types=1);

namespace Afup\Site\Corporate;

use Afup\Site\Utils\Base_De_Donnees;

class _Site_Base_De_Donnees extends Base_De_Donnees
{
    public function __construct()
    {
        parent::__construct(
            $_ENV['DATABASE_HOST'],
            $_ENV['DATABASE_NAME'],
            $_ENV['DATABASE_USER'],
            $_ENV['DATABASE_PASSWORD'],
            $_ENV['DATABASE_PORT'],
        );
    }
}
