<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\GithubUser;
use Symfony\Component\Validator\Constraints as Assert;

class GithubUserFormData
{
    /**
     * @var null|GithubUser
     */
    #[Assert\NotNull(message: "L'utilisateur n'existe pas")]
    public $user;

    /**
     * @var bool
     */
    public $afupCrew;
}
