<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Association\Genre;
use AppBundle\Event\Entity\GithubUser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class SpeakerFormData
{
    public ?Genre $genre = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    public $firstname;
    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    public $lastname;
    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Email]
    public $email;
    /**
     * @var string|null
     */
    public $company;
    /**
     * @var string|null
     */
    public $locality;
    /**
     * @var string|null
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

    public ?string $linkedin = null;

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
