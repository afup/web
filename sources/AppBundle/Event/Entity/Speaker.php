<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Association\Genre;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Speaker\MicrophoneType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: SpeakerRepository::class)]
#[ORM\Table(name: 'afup_conferenciers')]
class Speaker
{
    public const string NIGHT_BEFORE = 'before';
    public const string NIGHT_BETWEEN = 'between';
    public const string NIGHT_AFTER = 'after';

    private const string LINKEDIN_URL_PREFIX = 'https://www.linkedin.com/in/';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'conferencier_id')]
    public int $id;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    #[ORM\Column(name: 'id_forum', type: 'smallint')]
    public int $eventId;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    #[ORM\Column(name: 'user_github', nullable: true, options: ['unsigned' => true])]
    public ?int $githubUserId = null;

    #[ORM\Column(name: 'personne_id', nullable: true, options: ['unsigned' => true])]
    public ?int $personneId = null;

    #[ORM\Column(name: 'genre', length: 255, nullable: true, enumType: Genre::class)]
    public ?Genre $genre = null;

    #[Assert\NotBlank]
    #[ORM\Column(name: 'prenom', length: 50, nullable: true)]
    public ?string $firstname = null;

    #[Assert\NotBlank]
    #[ORM\Column(name: 'nom', length: 70, nullable: true)]
    public ?string $lastname = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(name: 'email', length: 65, nullable: true)]
    public ?string $email = null;

    #[ORM\Column(name: 'societe', length: 120, nullable: true)]
    public ?string $company = null;

    #[ORM\Column(name: 'ville', length: 255, nullable: true)]
    public ?string $locality = null;

    #[ORM\Column(name: 'phone_number', length: 255, nullable: true)]
    public ?string $phoneNumber = null;

    #[Assert\NotBlank]
    #[ORM\Column(name: 'biographie', type: 'text', nullable: true)]
    public ?string $biography = null;

    #[ORM\Column(name: 'twitter', length: 255, nullable: true)]
    public ?string $twitter = null;

    #[ORM\Column(name: 'mastodon', length: 255, nullable: true)]
    public ?string $mastodon = null;

    #[ORM\Column(name: 'linkedin', length: 255, nullable: true)]
    public ?string $linkedin = null;

    /**
     * Seul l'identifiant est conservé : l'URL du profil et l'arobase sont retirés.
     */
    #[ORM\Column(name: 'bluesky', length: 255, nullable: true)]
    public ?string $bluesky = null {
        set(?string $bluesky) {
            if ($bluesky !== null) {
                $bluesky = str_replace('https://bsky.app/profile/', '', $bluesky);

                if (str_starts_with($bluesky, '@')) {
                    $bluesky = substr($bluesky, 1);
                }
            }

            $this->bluesky = $bluesky;
        }
    }

    #[ORM\Column(name: 'photo', length: 255, nullable: true)]
    public ?string $photo = null;

    /**
     * Fichier envoyé par le formulaire SpeakerType, non persisté.
     */
    #[Assert\File(mimeTypes: ['image/jpeg', 'image/png'])]
    public ?UploadedFile $photoFile = null;

    #[ORM\Column(name: 'will_attend_speakers_diner', type: 'boolean', nullable: true, options: ['unsigned' => true])]
    public ?bool $willAttendSpeakersDiner = null;

    #[ORM\Column(name: 'has_special_diet', type: 'boolean', nullable: true, options: ['unsigned' => true])]
    public ?bool $hasSpecialDiet = null;

    #[ORM\Column(name: 'special_diet_description', type: 'text', nullable: true)]
    public ?string $specialDietDescription = null;

    /**
     * Nuits d'hôtel séparées par des virgules (colonne SET), voir les constantes NIGHT_*.
     */
    #[ORM\Column(name: 'hotel_nights', length: 255, nullable: true)]
    public ?string $hotelNights = null;

    #[ORM\Column(name: 'referent_person', length: 255, nullable: true)]
    public ?string $referentPerson = null;

    #[ORM\Column(name: 'referent_person_email', length: 255, nullable: true)]
    public ?string $referentPersonEmail = null;

    #[ORM\Column(name: 'has_hosting_sponsor', type: 'boolean', options: ['default' => false])]
    public bool $hasHostingSponsor = false;

    #[ORM\Column(name: 'travel_refund_needed', type: 'boolean', options: ['default' => true])]
    public bool $travelRefundNeeded = true;

    #[ORM\Column(name: 'travel_refund_sponsored', type: 'boolean', options: ['default' => false])]
    public bool $travelRefundSponsored = false;

    #[ORM\Column(name: 'mic_type', length: 255, nullable: true, enumType: MicrophoneType::class)]
    public ?MicrophoneType $micType = null;

    /**
     * « Prénom NOM », le même libellé que PublishedSpeaker::$label.
     */
    public string $label {
        get => ($this->firstname ?? '') . ' ' . ($this->lastname !== null && $this->lastname !== '' ? mb_strtoupper($this->lastname) : '');
    }

    public function getUrlBluesky(): ?string
    {
        if ($this->bluesky === null) {
            return null;
        }

        return 'https://bsky.app/profile/' . $this->bluesky;
    }

    public function getUrlLinkedin(): ?string
    {
        $username = $this->getUsernameLinkedin();

        if ($username === null) {
            return null;
        }

        return self::LINKEDIN_URL_PREFIX . $username;
    }

    public function getUsernameLinkedin(): ?string
    {
        if (null === $this->linkedin) {
            return null;
        }

        if (0 === strlen(trim($this->linkedin))) {
            return null;
        }

        if (str_starts_with($this->linkedin, self::LINKEDIN_URL_PREFIX)) {
            return rtrim(substr($this->linkedin, strlen(self::LINKEDIN_URL_PREFIX)), '/');
        }

        if (str_starts_with($this->linkedin, 'https://')) {
            return null;
        }

        return rtrim($this->linkedin, '/');
    }

    public function getUsernameTwitter(): string
    {
        $twitter = trim((string) $this->twitter, '@');
        $twitter = preg_replace('!^(https?://(twitter|x).com/)!', '', $twitter);

        return trim((string) $twitter);
    }

    public function getUrlTwitter(): string
    {
        $username = $this->getUsernameTwitter();

        return $username !== '' && $username !== '0' ? sprintf('https://x.com/%s', $username) : '';
    }

    public function getUsernameMastodon(): string
    {
        if ($this->mastodon === null || !str_contains($this->mastodon, '@')) {
            return '';
        }

        [, $username] = explode('@', $this->mastodon);

        return trim($username);
    }

    public function getUrlMastodon(): ?string
    {
        $username = $this->getUsernameMastodon();
        if ($username === '' || $username === '0' || $this->mastodon === null) {
            return null;
        }

        if (preg_match('#https?://@(.+)@(.+)#', $this->mastodon, $matches)) {
            return sprintf('https://%s/@%s', $matches[2], $matches[1]);
        }

        return trim($this->mastodon);
    }

    /**
     * @return list<string>|null null tant que le conférencier n'a rien indiqué
     */
    public function getHotelNightsArray(): ?array
    {
        if (null === $this->hotelNights) {
            return null;
        }

        if ($this->hotelNights === '') {
            return [];
        }

        return explode(',', $this->hotelNights);
    }

    /**
     * @param array<string> $hotelNights
     */
    public function setHotelNightsArray(array $hotelNights): void
    {
        $this->hotelNights = implode(',', $hotelNights);
    }

    public function hasHotelNightBefore(): ?bool
    {
        return $this->hasHotelNight(self::NIGHT_BEFORE);
    }

    public function hasHotelNightBetween(): ?bool
    {
        return $this->hasHotelNight(self::NIGHT_BETWEEN);
    }

    public function hasHotelNightAfter(): ?bool
    {
        return $this->hasHotelNight(self::NIGHT_AFTER);
    }

    public function hasNoHotelNight(): ?bool
    {
        if (null === ($hotelNights = $this->getHotelNightsArray())) {
            return null;
        }

        return 0 === count($hotelNights);
    }

    private function hasHotelNight(string $night): ?bool
    {
        if (null === ($hotelNights = $this->getHotelNightsArray())) {
            return null;
        }

        return in_array($night, $hotelNights, true);
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->photo === null && !$this->photoFile instanceof UploadedFile) {
            $context->buildViolation('Please, upload a photo.')
                ->atPath('photoFile')
                ->addViolation();
        }
    }
}
