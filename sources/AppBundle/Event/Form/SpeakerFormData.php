<?php

namespace AppBundle\Event\Form;

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
     * @Assert\NotBlank()
     * @var string
     */
    public $biography;
    /**
     * @var string
     */
    public $twitter;
    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png"})
     * @var UploadedFile|null
     */
    public $photo;
}
