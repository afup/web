<?php

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Speaker implements NotifyPropertyInterface
{
    use NotifyProperty;

    const NIGHT_BEFORE = 'before';
    const NIGHT_BETWEEN = 'between';
    const NIGHT_AFTER = 'after';

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $eventId;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $user;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $civility;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $firstname;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $lastname;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $locality;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $biography;

    /**
     * @var string
     */
    private $twitter;

    /**
     * @var GithubUser
     */
    private $githubUser;

    /**
     * @var string|null
     */
    private $photo;

    /**
     * Wrapper for SpeakerType to allow picture upload
     *
     * @Assert\File(mimeTypes={"image/jpeg","image/png"})
     * @var UploadedFile|null
     */
    private $photoFile;

    /**
     * @var bool|null
     */
    private $willAttendSpeakersDiner;

    /**
     * @var bool|null
     */
    private $hasSpecialDiet;

    /**
     * @var string
     */
    private $specialDietDescription;

    /**
     * @var string|null
     */
    private $hotelNights;

    /**
     * @var string|null
     */
    private $phoneNumber;

    /**
     * @var string|null
     */
    private $referentPerson;

    /**
     * @var string|null
     */
    private $referentPersonEmail;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Speaker
     */
    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $user
     * @return Speaker
     */
    public function setUser($user)
    {
        $this->propertyChanged('user', $this->user, $user);
        $this->user = $user;
        return $this;
    }

    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     * @return Speaker
     */
    public function setEventId($eventId)
    {
        $this->propertyChanged('eventId', $this->eventId, $eventId);
        $this->eventId = $eventId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * @param string $civility
     * @return Speaker
     */
    public function setCivility($civility)
    {
        $this->propertyChanged('civility', $this->civility, $civility);
        $this->civility = $civility;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return Speaker
     */
    public function setFirstname($firstname)
    {
        $this->propertyChanged('firstname', $this->firstname, $firstname);
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return Speaker
     */
    public function setLastname($lastname)
    {
        $this->propertyChanged('lastname', $this->lastname, $lastname);
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getFirstname() . " " . mb_strtoupper($this->getLastname());
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Speaker
     */
    public function setEmail($email)
    {
        $this->propertyChanged('email', $this->email, $email);
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     * @return Speaker
     */
    public function setCompany($company)
    {
        $this->propertyChanged('company', $this->company, $company);
        $this->company = $company;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param string $locality
     * @return Speaker
     */
    public function setLocality($locality)
    {
        $this->propertyChanged('locality', $this->locality, $locality);
        $this->locality = $locality;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->propertyChanged('phoneNumber', $this->phoneNumber, $phoneNumber);
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string|null
     */
    public function getReferentPerson()
    {
        return $this->referentPerson;
    }

    /**
     * @param string|null $referentPerson
     */
    public function setReferentPerson($referentPerson)
    {
        $this->propertyChanged('referentPerson', $this->referentPerson, $referentPerson);
        $this->referentPerson = $referentPerson;
    }

    /**
     * @return string|null
     */
    public function getReferentPersonEmail()
    {
        return $this->referentPersonEmail;
    }

    /**
     * @param string|null $referentPersonEmail
     */
    public function setReferentPersonEmail($referentPersonEmail)
    {
        $this->propertyChanged('referentPersonEmail', $this->referentPersonEmail, $referentPersonEmail);
        $this->referentPersonEmail = $referentPersonEmail;
    }


    /**
     * @return string
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * @param string $biography
     * @return Speaker
     */
    public function setBiography($biography)
    {
        $this->propertyChanged('biography', $this->biography, $biography);
        $this->biography = $biography;
        return $this;
    }

    /**
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * @return bool|string
     */
    public function getCleanedTwitter()
    {
        $twitter = $this->getTwitter();
        $twitter = trim($twitter, '@');
        $twitter = preg_replace('!^https?://twitter.com/!', '', $twitter);

        if (0 === strlen(trim($twitter))) {
            return null;
        }

        return $twitter;
    }

    /**
     * @param string $twitter
     * @return Speaker
     */
    public function setTwitter($twitter)
    {
        $this->propertyChanged('twitter', $this->twitter, $twitter);
        $this->twitter = $twitter;
        return $this;
    }

    /**
     * @return GithubUser
     */
    public function getGithubUser()
    {
        return $this->githubUser;
    }

    /**
     * @param GithubUser $githubUser
     * @return Speaker
     */
    public function setGithubUser(GithubUser $githubUser)
    {
        $this->githubUser = $githubUser;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     * @return Speaker
     */
    public function setPhoto($photo)
    {
        if ($this->photo === null || $photo !== null) {
            $this->propertyChanged('photo', $this->photo, $photo);
            $this->photo = $photo;
        }
        return $this;
    }

    /**
     * @return UploadedFile|null
     */
    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    /**
     * @param UploadedFile|null $photoFile
     * @return Speaker
     */
    public function setPhotoFile(UploadedFile $photoFile)
    {
        $this->photoFile = $photoFile;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getWillAttendSpeakersDiner()
    {
        return $this->willAttendSpeakersDiner;
    }

    /**
     * @param bool|null $willAttendSpeakersDiner
     *
     * @return $this
     */
    public function setWillAttendSpeakersDiner($willAttendSpeakersDiner)
    {
        $this->propertyChanged('willAttendSpeakersDiner', $this->willAttendSpeakersDiner, $willAttendSpeakersDiner);

        $this->willAttendSpeakersDiner = $willAttendSpeakersDiner;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasSpecialDiet()
    {
        return $this->hasSpecialDiet;
    }

    /**
     * @param bool|null $hasSpecialDiet
     *
     * @return $this
     */
    public function setHasSpecialDiet($hasSpecialDiet)
    {
        $this->propertyChanged('hasSpecialDiet', $this->hasSpecialDiet, $hasSpecialDiet);

        $this->hasSpecialDiet = $hasSpecialDiet;

        return $this;
    }

    /**
     * @return string
     */
    public function getSpecialDietDescription()
    {
        return $this->specialDietDescription;
    }

    /**
     * @param string $specialDietDescription
     *
     * @return $this
     */
    public function setSpecialDietDescription($specialDietDescription)
    {
        $this->propertyChanged('specialDietDescription', $this->specialDietDescription, $specialDietDescription);
        $this->specialDietDescription = $specialDietDescription;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getHotelNights()
    {
        return $this->hotelNights;
    }

    /**
     * @return array
     */
    public function getHotelNightsArray()
    {
        if (null === $this->hotelNights) {
            return null;
        }

        if (0 === strlen($this->hotelNights)) {
            return [];
        }

        return explode(',', $this->hotelNights);
    }

    /**
     * @param array $hotelNights
     *
     * @return Speaker
     */
    public function setHotelNightsArray(array $hotelNights)
    {
        return $this->setHotelNights(implode(',', $hotelNights));
    }

    /**
     * @param null|string $hotelNights
     *
     * @return $this
     */
    public function setHotelNights($hotelNights)
    {
        $this->propertyChanged('hotelNights', $this->hotelNights, $hotelNights);
        $this->hotelNights = $hotelNights;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function hasHotelNightBefore()
    {
        if (null === ($hotelNights = $this->getHotelNightsArray())) {
            return null;
        }

        return in_array(self::NIGHT_BEFORE, $hotelNights);
    }

    /**
     * @return bool|null
     */
    public function hasHotelNightBetween()
    {
        if (null === ($hotelNights = $this->getHotelNightsArray())) {
            return null;
        }

        return in_array(self::NIGHT_BETWEEN, $hotelNights);
    }

    /**
     * @return bool|null
     */
    public function hasHotelNightAfter()
    {
        if (null === ($hotelNights = $this->getHotelNightsArray())) {
            return null;
        }

        return in_array(self::NIGHT_AFTER, $hotelNights);
    }

    /**
     * @return bool|null
     */
    public function hasNoHotelNight()
    {
        if (null === ($hotelNights = $this->getHotelNightsArray())) {
            return null;
        }

        return 0 === count($hotelNights);
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        // check if the name is actually a fake name
        if ($this->getPhoto() === null && $this->getPhotoFile() === null) {
            $context->buildViolation('Please, upload a photo.')
                ->atPath('photoFile')
                ->addViolation();
        }
    }
}
