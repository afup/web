<?php

declare(strict_types=1);

namespace AppBundle\Security\Exception;

final class UnexpectedUserTypeException extends \LogicException
{
    public function __construct(string $expectedUserType)
    {
        parent::__construct(sprintf('A `%s` user was expected', $expectedUserType));
    }
}
