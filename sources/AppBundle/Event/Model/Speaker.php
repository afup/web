<?php

declare(strict_types=1);

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
     */
    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private $eventId;

    /**
     * @var int
     */
    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private $user;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $civility;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $firstname;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $lastname;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    #[Assert\Email]
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
     * @var string
     */
    #[Assert\NotBlank]
    private $biography;

    /**
     * @var string
     */
    private $twitter;

    private ?string $mastodon = null;

    private ?string $bluesky = null;

    private ?GithubUser $githubUser = null;

    /**
     * @var string|null
     */
    private $photo;

    /**
     * Wrapper for SpeakerType to allow picture upload
     */
    #[Assert\File(mimeTypes: ['image/jpeg', 'image/png'])]
    private ?UploadedFile $photoFile = null;

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
     */
    public function setId($id): self
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
     */
    public function setUser($user): self
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
     */
    public function setEventId($eventId): self
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
     */
    public function setCivility($civility): self
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
     */
    public function setFirstname($firstname): self
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
     */
    public function setLastname($lastname): self
    {
        $this->propertyChanged('lastname', $this->lastname, $lastname);
        $this->lastname = $lastname;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->getFirstname() . " " . ($this->getLastname() ? mb_strtoupper($this->getLastname()) : null);
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
     */
    public function setEmail($email): self
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
     */
    public function setCompany($company): self
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
     */
    public function setLocality($locality): self
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
    public function setPhoneNumber($phoneNumber): void
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
    public function setReferentPerson($referentPerson): void
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
    public function setReferentPersonEmail($referentPersonEmail): void
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
     */
    public function setBiography($biography): self
    {
        $this->propertyChanged('biography', $this->biography, $biography);
        $this->biography = $biography;
        return $this;
    }

    public function getTwitter(): string
    {
        return (string) $this->twitter;
    }

    public function getMastodon(): ?string
    {
        return $this->mastodon;
    }

    public function getBluesky(): ?string
    {
        return $this->bluesky;
    }

    public function getUrlBluesky(): ?string
    {
        if ($this->bluesky === null) {
            return null;
        }

        return 'https://bsky.app/profile/' . $this->bluesky;
    }

    public function getUsernameTwitter(): string
    {
        $twitter = $this->getTwitter();
        $twitter = trim($twitter, '@');
        $twitter = preg_replace('!^(https?://(twitter|x).com/)!', '', $twitter);

        return trim((string) $twitter);
    }

    public function getUrlTwitter(): string
    {
        return $this->getUsernameTwitter() !== '' && $this->getUsernameTwitter() !== '0' ? sprintf('https://x.com/%s', $this->getUsernameTwitter()) : '';
    }

    public function getUsernameMastodon(): string
    {
        $mastodon = $this->getMastodon();

        if ($mastodon === null) {
            return '';
        }

        if (!str_contains($mastodon, '@')) {
            return '';
        }

        [, $username] = explode('@', $mastodon);
        return trim($username);
    }

    public function getUrlMastodon(): string
    {
        if ($this->getUsernameMastodon() === '' || $this->getUsernameMastodon() === '0') {
            return '';
        }

        $mastodon = $this->getMastodon();

        if ($mastodon === null) {
            return '';
        }

        if (preg_match('#https?://@(.+)@(.+)#', $mastodon, $matches)) {
            return sprintf('https://%s/@%s', $matches[2], $matches[1]);
        }

        return trim($mastodon);
    }

    public function getCleanedMastodon(): string
    {
        $mastodon = $this->getMastodon();
        if (!str_contains((string) $mastodon, '@')) {
            return '';
        }

        [, $username] = explode('@', (string) $mastodon);
        return trim($username);
    }

    /**
     * @param string $twitter
     */
    public function setTwitter($twitter): self
    {
        $this->propertyChanged('twitter', $this->twitter, $twitter);
        $this->twitter = $twitter;
        return $this;
    }

    /**
     * @param string $mastodon
     */
    public function setMastodon($mastodon): self
    {
        $this->propertyChanged('mastodon', $this->mastodon, $mastodon);
        $this->mastodon = $mastodon;
        return $this;
    }

    public function setBluesky(?string $bluesky): self
    {
        if ($bluesky !== null) {
            $bluesky = str_replace('https://bsky.app/profile/', '', $bluesky);

            if (str_starts_with($bluesky, '@')) {
                $bluesky = substr($bluesky, 1);
            }
        }

        $this->propertyChanged('bluesky', $this->bluesky, $bluesky);
        $this->bluesky = $bluesky;
        return $this;
    }

    /**
     * @return GithubUser
     */
    public function getGithubUser(): ?GithubUser
    {
        return $this->githubUser;
    }

    public function setGithubUser(GithubUser $githubUser): self
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
     */
    public function setPhoto($photo): self
    {
        if ($this->photo === null || $photo !== null) {
            $this->propertyChanged('photo', $this->photo, $photo);
            $this->photo = $photo;
        }
        return $this;
    }

    public function getPhotoFile(): ?UploadedFile
    {
        return $this->photoFile;
    }

    public function setPhotoFile(?UploadedFile $photoFile): self
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
    public function setWillAttendSpeakersDiner($willAttendSpeakersDiner): self
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
    public function setHasSpecialDiet($hasSpecialDiet): self
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
    public function setSpecialDietDescription($specialDietDescription): self
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

    public function getHotelNightsArray(): ?array
    {
        if (null === $this->hotelNights) {
            return null;
        }

        if ((string) $this->hotelNights === '') {
            return [];
        }

        return explode(',', $this->hotelNights);
    }

    public function setHotelNightsArray(array $hotelNights): self
    {
        return $this->setHotelNights(implode(',', $hotelNights));
    }

    /**
     * @param null|string $hotelNights
     *
     * @return $this
     */
    public function setHotelNights($hotelNights): self
    {
        $this->propertyChanged('hotelNights', $this->hotelNights, $hotelNights);
        $this->hotelNights = $hotelNights;

        return $this;
    }

    public function hasHotelNightBefore(): ?bool
    {
        if (null === ($hotelNights = $this->getHotelNightsArray())) {
            return null;
        }

        return in_array(self::NIGHT_BEFORE, $hotelNights);
    }

    public function hasHotelNightBetween(): ?bool
    {
        if (null === ($hotelNights = $this->getHotelNightsArray())) {
            return null;
        }

        return in_array(self::NIGHT_BETWEEN, $hotelNights);
    }

    public function hasHotelNightAfter(): ?bool
    {
        if (null === ($hotelNights = $this->getHotelNightsArray())) {
            return null;
        }

        return in_array(self::NIGHT_AFTER, $hotelNights);
    }

    public function hasNoHotelNight(): ?bool
    {
        if (null === ($hotelNights = $this->getHotelNightsArray())) {
            return null;
        }

        return 0 === count($hotelNights);
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, $payload): void
    {
        // check if the name is actually a fake name
        if ($this->getPhoto() === null && !$this->getPhotoFile() instanceof UploadedFile) {
            $context->buildViolation('Please, upload a photo.')
                ->atPath('photoFile')
                ->addViolation();
        }
    }
}
