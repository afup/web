<?php

declare(strict_types=1);

namespace AppBundle\Antennes\Form;

use AppBundle\Antennes\ReseauxSociaux;
use Symfony\Component\Validator\Constraints as Assert;

final class ReseauxSociauxFormData
{
    #[Assert\Url(requireTld: true)]
    public ?string $youtube = null;

    #[Assert\Url(requireTld: true)]
    public ?string $blog = null;

    public ?string $twitter = null;

    public ?string $linkedin = null;

    public ?string $bluesky = null;

    #[Assert\Url(requireTld: true)]
    public ?string $cfp = null;

    public ?string $github = null;

    #[Assert\Url(requireTld: true)]
    public ?string $discord = null;

    #[Assert\Email]
    public ?string $email = null;

    public static function fromReseauxSociaux(ReseauxSociaux $reseauxSociaux): self
    {
        $data = new self();
        $data->youtube = $reseauxSociaux->youtube;
        $data->blog = $reseauxSociaux->blog;
        $data->twitter = $reseauxSociaux->twitter;
        $data->linkedin = $reseauxSociaux->linkedin;
        $data->bluesky = $reseauxSociaux->bluesky;
        $data->cfp = $reseauxSociaux->cfp;
        $data->github = $reseauxSociaux->github;
        $data->discord = $reseauxSociaux->discord;
        $data->email = $reseauxSociaux->email;

        return $data;
    }

    public function toReseauxSociaux(): ReseauxSociaux
    {
        return new ReseauxSociaux(
            youtube: self::nullIfEmpty($this->youtube),
            blog: self::nullIfEmpty($this->blog),
            twitter: self::nullIfEmpty($this->twitter),
            linkedin: self::nullIfEmpty($this->linkedin),
            bluesky: self::nullIfEmpty($this->bluesky),
            cfp: self::nullIfEmpty($this->cfp),
            github: self::nullIfEmpty($this->github),
            discord: self::nullIfEmpty($this->discord),
            email: self::nullIfEmpty($this->email),
        );
    }

    private static function nullIfEmpty(?string $value): ?string
    {
        $value = $value !== null ? trim($value) : null;

        return $value === '' ? null : $value;
    }
}
