<?php

declare(strict_types=1);

namespace AppBundle\Github\Exception;

class UnableToFindGithubUserException extends \Exception
{
    public function __construct($username)
    {
        parent::__construct("Unable to find GitHub user matching with \"{$username}\".");
    }
}
