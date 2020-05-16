<?php

namespace Afup\Site\Logger;

use Afup\Site\Utils\Logs;
use AppBundle\Association\Model\User;

trait DbLoggerTrait
{
    public function log($message, User $user = null)
    {
        $id = null !== $user ? $user->getId() : 0;
        Logs::initialiser($GLOBALS['AFUP_DB'], $id);
        Logs::log($message);
    }
}
