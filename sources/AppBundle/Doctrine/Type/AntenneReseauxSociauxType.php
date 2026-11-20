<?php

declare(strict_types=1);

namespace AppBundle\Doctrine\Type;

use AppBundle\Antennes\ReseauxSociaux;

/**
 * Colonne JSON `afup_antenne.reseaux_sociaux` hydratée en objet ReseauxSociaux.
 *
 * Format stocké : {"youtube": "...", "blog": "...", ...}, seules les clés renseignées sont présentes.
 *
 * @extends JsonValueObjectType<ReseauxSociaux>
 */
final class AntenneReseauxSociauxType extends JsonValueObjectType
{
    public const string NAME = 'antenne_reseaux_sociaux';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function valueClass(): string
    {
        return ReseauxSociaux::class;
    }

    protected function fromArray(array $data): ReseauxSociaux
    {
        return new ReseauxSociaux(
            youtube: self::nullableString($data, 'youtube'),
            blog: self::nullableString($data, 'blog'),
            twitter: self::nullableString($data, 'twitter'),
            linkedin: self::nullableString($data, 'linkedin'),
            bluesky: self::nullableString($data, 'bluesky'),
            cfp: self::nullableString($data, 'cfp'),
            github: self::nullableString($data, 'github'),
            discord: self::nullableString($data, 'discord'),
        );
    }

    protected function toArray(object $value): array
    {
        return array_filter(
            [
                'youtube' => $value->youtube,
                'blog' => $value->blog,
                'twitter' => $value->twitter,
                'linkedin' => $value->linkedin,
                'bluesky' => $value->bluesky,
                'cfp' => $value->cfp,
                'github' => $value->github,
                'discord' => $value->discord,
            ],
            static fn(?string $reseau): bool => $reseau !== null,
        );
    }
}
