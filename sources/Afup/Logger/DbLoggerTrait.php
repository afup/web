<?php

declare(strict_types=1);

namespace Afup\Site\Logger;

use Afup\Site\Utils\Logs;
use AppBundle\Association\Model\User;

trait DbLoggerTrait
{
    public function log($message, User $user = null): void
    {
        $id = $user instanceof User ? $user->getId() : 0;
        Logs::initialiser($GLOBALS['AFUP_DB'], $id);
        Logs::log($message);
    }
}
