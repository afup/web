<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\GithubUser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class SpeakerFormData
{
    /**
     * @var string
     */
    #[Assert\NotBlank]
    public $civility;
    /**
     * @var string
     */
    #[Assert\NotBlank]
    public $firstname;
    /**
     * @var string
     */
    #[Assert\NotBlank]
    public $lastname;
    /**
     * @var string
     */
    #[Assert\NotBlank]
    #[Assert\Email]
    public $email;
    /**
     * @var string
     */
    public $company;
    /**
     * @var string
     */
    public $locality;
    /**
     * @var string
     */
    #[Assert\NotBlank]
    public $biography;
    /**
     * @var string
     */
    public $twitter;
    /**
     * @var string
     */
    public $mastodon;

    public ?string $bluesky = null;

    /**
     * @var UploadedFile|null
     */
    #[Assert\File(mimeTypes: ['image/jpeg', 'image/png'])]
    public $photoFile;

    /**
     * @var null|GithubUser|int
     */
    public $githubUser;

    /**
     * @var string
     */
    public $phoneNumber;

    /**
     * @var string
     */
    public $referentPerson;

    /**
     * @var string
     */
    public $referentPersonEmail;
}
