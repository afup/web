<?php

namespace Afup\Site\Logger;

use Afup\Site\Utils\Logs;
use AppBundle\Association\Model\User;

trait DbLoggerTrait
{
    public function log($message, User $user = null)
    {
        $id = null !== $user ? $user->getId() : null;
        Logs::initialiser($GLOBALS['bdd'], $id);
        Logs::log($message);
    }
}
