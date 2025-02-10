<?php

declare(strict_types=1);

namespace AppBundle\Github\Exception;

class UnableToGetGithubUserInfosException extends \Exception
{
    public function __construct($status, $payload)
    {
        parent::__construct("Unable to check GitHub user infos (status: {$status}, payload: {$payload}).");
    }
}
