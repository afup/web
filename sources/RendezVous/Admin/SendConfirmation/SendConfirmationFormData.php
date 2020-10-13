<?php

namespace App\RendezVous\Admin\SendConfirmation;

use Symfony\Component\Validator\Constraints as Assert;

class SendConfirmationFormData
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $subject;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $body;
}
