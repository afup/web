<?php

declare(strict_types=1);

namespace Afup\Site\Corporate;

use Afup\Site\Utils\Base_De_Donnees;

class _Site_Base_De_Donnees extends Base_De_Donnees
{
    public function __construct()
    {
        parent::__construct(
            getenv('DATABASE_HOST'),
            getenv('DATABASE_NAME'),
            getenv('DATABASE_USER'),
            getenv('DATABASE_PASSWORD'),
            getenv('DATABASE_PORT'),
        );
    }
}
