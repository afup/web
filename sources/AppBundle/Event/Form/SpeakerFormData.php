<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\GithubUser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class SpeakerFormData
{
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public $civility;
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public $firstname;
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public $lastname;
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @var string
     */
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
     * @Assert\NotBlank()
     * @var string
     */
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
     * @Assert\File(mimeTypes={"image/jpeg","image/png"})
     * @var UploadedFile|null
     */
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
