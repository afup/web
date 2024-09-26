<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\GithubUser;
use Symfony\Component\Validator\Constraints as Assert;

class GithubUserFormData
{
    /**
     * @Assert\NotNull(message="L'utilisateur n'existe pas")
     * @var null|GithubUser
     */
    public $user;

    /**
     * @var bool
     */
    public $afupCrew;
}
