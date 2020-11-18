<?php

namespace App\RendezVous\Admin\EditAttendee;

use DateTimeImmutable;
use DateTimeInterface;

class EditAttendeeFormData
{
    /** @var DateTimeInterface */
    public $creation;
    /** @var string|null */
    public $firstname;
    /** @var string|null */
    public $lastname;
    /** @var string|null */
    public $company;
    /** @var string|null */
    public $phone;
    /** @var string|null */
    public $email;
    /** @var int|null */
    public $confirmed;
    /** @var int|null */
    public $presence;

    public function __construct()
    {
        $this->creation = new DateTimeImmutable();
    }
}
