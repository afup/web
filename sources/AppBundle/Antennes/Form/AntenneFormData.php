<?php

declare(strict_types=1);

namespace AppBundle\Antennes\Form;

use AppBundle\Antennes\Entity\Antenne;
use AppBundle\Antennes\Meetup;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class AntenneFormData
{
    public const string GROUPE_CREATION = 'creation';

    /**
     * Uniquement saisi à la création : le code est la clé primaire et il est référencé ailleurs
     * (super apéros, antenne la plus proche des membres, billets, indexation des meetups).
     * Son unicité est vérifiée par AntenneType.
     * Il sert d'alias dans la requête GraphQL de l'indexation des meetups (MeetupClient) : il doit donc
     * respecter la syntaxe d'un nom GraphQL, sans quoi l'indexation de toutes les antennes échoue.
     */
    #[Assert\NotBlank(groups: [self::GROUPE_CREATION])]
    #[Assert\Length(max: 50, groups: [self::GROUPE_CREATION])]
    #[Assert\Regex(
        pattern: '/^[a-z][a-z0-9_]*$/',
        message: 'Le code doit commencer par une lettre minuscule et ne contenir que des lettres minuscules non accentuées, des chiffres et des underscores.',
        groups: [self::GROUPE_CREATION],
    )]
    public ?string $code = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public ?string $libelle = null;

    /**
     * Les logos existants sont servis par le site (`/images/offices/…`) : un chemin absolu est donc accepté
     * en plus d'une URL complète.
     */
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Assert\AtLeastOneOf(
        constraints: [
            new Assert\Url(requireTld: true),
            new Assert\Regex(pattern: '#^/[^/\s]\S*$#'),
        ],
        message: 'Le logo doit être une URL valide (ou un chemin commençant par « / »).',
        includeInternalMessages: false,
    )]
    public ?string $urlLogo = null;

    /**
     * Les espaces autour de la valeur sont tolérés (et une valeur vide ignorée) : nullIfEmpty() les retire
     * avant l'enregistrement.
     */
    #[Assert\Length(max: 255)]
    #[Assert\Regex(
        pattern: '/^\s*[A-Za-z0-9_-]*\s*$/',
        message: "Le nom d'URL Meetup ne doit contenir que le nom du groupe (ex. afup-lyon-php), pas l'URL complète.",
    )]
    public ?string $meetupNomUrl = null;

    /**
     * Injecté sans guillemets dans la requête GraphQL de l'indexation des meetups (MeetupClient).
     */
    #[Assert\Length(max: 50)]
    #[Assert\Regex(
        pattern: '/^\s*\d*\s*$/',
        message: "L'identifiant Meetup doit être numérique.",
    )]
    public ?string $meetupIdExterne = null;

    /**
     * Identifiant (et non nom) du canal de l'antenne sur le Slack Membres : l'API Slack n'accepte que les IDs.
     */
    #[Assert\Length(max: 50)]
    #[Assert\Regex(
        pattern: '/^\s*([CG][A-Z0-9]{8,})?\s*$/',
        message: "L'identifiant du canal Slack doit être un ID de canal (ex. C0123ABCDEF), pas son nom.",
    )]
    public ?string $slackMembresIdCanal = null;

    #[Assert\Valid]
    public ReseauxSociauxFormData $reseauxSociaux;

    #[Assert\Valid]
    public CarteFormData $carte;

    /** @var string[]|null */
    public ?array $departements = null;

    /** @var string[]|null */
    public ?array $pays = null;

    #[Assert\Length(max: 255)]
    public ?string $region = null;

    public bool $actif = true;

    public function __construct()
    {
        $this->reseauxSociaux = new ReseauxSociauxFormData();
        $this->carte = new CarteFormData();
    }

    public static function fromEntity(Antenne $antenne): self
    {
        $data = new self();
        $data->code = $antenne->code;
        $data->libelle = $antenne->libelle;
        $data->urlLogo = $antenne->urlLogo;
        $data->meetupNomUrl = $antenne->meetup?->nomUrl;
        $data->meetupIdExterne = $antenne->meetup?->id;
        $data->slackMembresIdCanal = $antenne->slackMembresIdCanal;
        $data->reseauxSociaux = ReseauxSociauxFormData::fromReseauxSociaux($antenne->reseauxSociaux);
        $data->carte = CarteFormData::fromCarte($antenne->carte);
        $data->departements = $antenne->departements;
        $data->pays = $antenne->pays;
        $data->region = $antenne->region;
        $data->actif = !$antenne->masqueePageAntennes;

        return $data;
    }

    #[Assert\Callback]
    public function validateMeetup(ExecutionContextInterface $context): void
    {
        $nomUrlRenseigne = self::nullIfEmpty($this->meetupNomUrl) !== null;
        $idRenseigne = self::nullIfEmpty($this->meetupIdExterne) !== null;

        if ($nomUrlRenseigne && !$idRenseigne) {
            $context->buildViolation("L'identifiant Meetup est obligatoire quand le nom d'URL est renseigné.")
                ->atPath('meetupIdExterne')
                ->addViolation();
        }

        if ($idRenseigne && !$nomUrlRenseigne) {
            $context->buildViolation("Le nom d'URL Meetup est obligatoire quand l'identifiant est renseigné.")
                ->atPath('meetupNomUrl')
                ->addViolation();
        }
    }

    public function toEntity(): Antenne
    {
        return new Antenne(
            code: (string) $this->code,
            libelle: (string) $this->libelle,
            meetup: $this->meetup(),
            urlLogo: (string) $this->urlLogo,
            reseauxSociaux: $this->reseauxSociaux->toReseauxSociaux(),
            carte: $this->carte->toCarte(),
            departements: $this->departements,
            pays: $this->pays,
            region: self::nullIfEmpty($this->region),
            masqueePageAntennes: !$this->actif,
            slackMembresIdCanal: self::nullIfEmpty($this->slackMembresIdCanal),
        );
    }

    public function applyTo(Antenne $antenne): void
    {
        $antenne->libelle = (string) $this->libelle;
        $antenne->meetup = $this->meetup();
        $antenne->urlLogo = (string) $this->urlLogo;
        $antenne->reseauxSociaux = $this->reseauxSociaux->toReseauxSociaux();
        $antenne->carte = $this->carte->toCarte();
        $antenne->departements = $this->departements;
        $antenne->pays = $this->pays;
        $antenne->region = self::nullIfEmpty($this->region);
        $antenne->masqueePageAntennes = !$this->actif;
        $antenne->slackMembresIdCanal = self::nullIfEmpty($this->slackMembresIdCanal);
    }

    private function meetup(): ?Meetup
    {
        $nomUrl = self::nullIfEmpty($this->meetupNomUrl);
        $id = self::nullIfEmpty($this->meetupIdExterne);

        return $nomUrl !== null && $id !== null ? new Meetup($nomUrl, $id) : null;
    }

    private static function nullIfEmpty(?string $value): ?string
    {
        $value = $value !== null ? trim($value) : null;

        return $value === '' ? null : $value;
    }
}
